<?php

namespace App\Model\Project\ProjectUserAllocation;


use App\Model\Exceptions\ProcessException;
use App\Model\Project\ProjectRepository;
use App\Model\Project\ProjectUser\EState;
use App\Model\Project\ProjectUser\ProjectUserRepository;
use App\Model\Repository\Base\BaseRepository;
use App\Tools\Transaction;
use Cassandra\Date;
use DateTime;
use Nette\Database\Explorer;
use Nette\Database\Table\Selection;
use Nette\Utils\ArrayHash;
use Tracy\Debugger;
use Tracy\ILogger;


class ProjectUserAllocationFacade
{
    private ProjectUserRepository $projectUserRepository;
    private ProjectUserAllocationRepository $allocationRepository;
    private Transaction $transaction;
    private ProjectRepository $projectRepository;

    public const  MAX_ALLOCATION = 40;

    public function __construct(
        ProjectUserRepository $projectUserRepository,
        ProjectUserAllocationRepository $allocationRepository,
        ProjectRepository $projectRepository,
        Transaction $transaction
    )
    {

        $this->projectUserRepository = $projectUserRepository;
        $this->allocationRepository = $allocationRepository;
        $this->transaction = $transaction;
        $this->projectRepository = $projectRepository;
    }


    /**
     * @throws ProcessException
     */
    public function validateAllocationTime(ArrayHash $allocation, int $projectUserId, int $projectId): void
    {
        if ($projectUserId <= 0) {
            throw new ProcessException('app.projectAllocation.userNotOnProject');
        }

        $project = $this->projectRepository->getProject($projectId);

        if (empty($project)) {
            throw new ProcessException('app.projectAllocation.projectNotExists');
        }

        /** @var DateTime $allocFrom */
        $allocFrom = $allocation['from'];
        /** @var DateTime $allocTo */
        $allocTo = $allocation['to'];

        $allocFrom->setTime(0,0);
        $allocTo->setTime(0,0);

        if ($allocFrom < $project['from'])
        {
            throw new ProcessException('app.projectAllocation.timeWindowError');
        }

        if($project['to'] !== null)
        {

            if($allocTo > $project['to'])
            {
                throw new ProcessException('app.projectAllocation.timeWindowError');
            }
        }
    }

    /**
     * @throws ProcessException
     */
    public function validateAllocationPossibility(ArrayHash $allocation, int $userId, array $storedAllocation = []): void
    {

        $from = $allocation['from']->setTime(0,0);
        $to = $allocation['to']->setTime(0,0);
        $userProjectMemberships = $this->projectUserRepository->getAllProjectMembershipIds($userId);
        $currentWorkLoad = $this->allocationRepository->getCurrentWorkload($from, $to, $userProjectMemberships);
        $storedAllocationValue = empty($storedAllocation) ? 0 : $storedAllocation['allocation'];
        $potentionalAllocation = 0;
        $storedState = empty($storedAllocation) ? EState::ACTIVE->value : $storedAllocation['state'];
        //todo
        $currentWorkLoad = $this->getWorkloadForUser($from, $to, $userId);

        if($storedState === EState::ACTIVE->value && $allocation['state'] === EState::ACTIVE->value)
        {
            $potentionalAllocation = $currentWorkLoad + $allocation['allocation'] - $storedAllocationValue;
        }
        if($storedState === EState::ACTIVE->value && $allocation['state'] !== EState::ACTIVE->value)
        {
            $potentionalAllocation = 0;
        }
        if($storedState !== EState::ACTIVE->value && $allocation['state'] === EState::ACTIVE->value)
        {
            $inc = $allocation['allocation'] > $storedAllocationValue ? $allocation['allocation'] : $storedAllocationValue;
            $potentionalAllocation = $currentWorkLoad + $inc;
        }
        if($storedState !== EState::ACTIVE->value && $allocation['state'] !== EState::ACTIVE->value)
        {
            $potentionalAllocation = 0;
        }

        if($potentionalAllocation > self::MAX_ALLOCATION)
        {
            throw new ProcessException('app.projectAllocation.allocationError');
        }


    }

    /**
     * @throws ProcessException
     */
    public function createAllocation(ArrayHash $allocation, int $projectId): void
    {
        try {

        $this->transaction->begin();
        $user_id = $allocation['user_id'];
        $projectUserId = $this->projectUserRepository->isUserOnProject($user_id, $projectId);
        $this->validateAllocationTime($allocation, $projectUserId, $projectId);

        $this->validateAllocationPossibility($allocation, $user_id);


        $this->allocationRepository->saveAllocation($allocation, $projectUserId);
        $this->transaction->commit();
        }
        catch (\PDOException $e)
        {
            $this->transaction->rollback();
            Debugger::log($e,ILogger::EXCEPTION);
            throw new ProcessException('app.baseForm.saveError');
        }

    }

    /**
     * @throws ProcessException
     */
    public function editAllocation(ArrayHash $allocation, int $allocationId): void
    {

        try {
            $this->transaction->begin();
            $storedAllocation = $this->allocationRepository->getAllocation($allocationId);
            if(empty($storedAllocation))
            {
                throw new ProcessException('app.projectAllocation.allocationNotExists');
            }
            $userId = $storedAllocation['curr_user_id'];
            $projectId = $storedAllocation['curr_project_id'];
            $projectUserId = $this->projectUserRepository->isUserOnProject($userId, $projectId);
            $this->validateAllocationTime($allocation, $projectUserId, $projectId);

            $this->validateAllocationPossibility($allocation, $userId, $storedAllocation);

            $this->allocationRepository->saveAllocation($allocation, $projectUserId, $allocationId);

            $this->transaction->commit();
        }
        catch (\PDOException $e)
        {
            $this->transaction->rollback();
            Debugger::log($e, ILogger::EXCEPTION);
            throw new ProcessException('app.baseForm.saveError');
        }



    }

    public function getWorkloadForUser(DateTime $from, DateTime $to, int $userId): int
    {
        $userProjectMemberships = $this->projectUserRepository->getAllProjectMembershipIds($userId);
        $currentWorkLoad = $this->allocationRepository->getCurrentWorkload($from, $to, $userProjectMemberships);
        return $currentWorkLoad;
    }

    public function getCurrentWorkloadForUser(int $userId): int
    {
        $dateFrom = new DateTime();
        $dateFrom->setTime(0,0);
        $dateTo = new DateTime();
        $dateTo->setTime(0,0);//->modify("+1 day");
        bdump($dateFrom);
        bdump($dateTo);
        return $this->getWorkloadForUser($dateFrom, $dateTo, $userId);

    }

    public function getProjectUserAllocationGridSelection(int $projectId): Selection
    {

        $usersOnProject = $this->projectUserRepository->getAllUsersOnProjectIds($projectId);
        bdump($usersOnProject);
        return $this->allocationRepository->getAllAllocationsOnProject($usersOnProject);
    }





}