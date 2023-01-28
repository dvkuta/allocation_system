<?php

namespace App\Model\Project\ProjectUserAllocation;


use App\Model\Exceptions\ProcessException;
use App\Model\Project\ProjectRepository;
use App\Model\Project\ProjectUser\ProjectUserRepository;
use App\Model\Repository\Base\BaseRepository;
use App\Tools\Transaction;
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
    public function validateAllocation(ArrayHash $allocation, int $projectUserId, int $projectId): void
    {
        if ($projectUserId <= 0) {
            throw new ProcessException('app.projectAllocation.userNotOnProject');
        }

        $project = $this->projectRepository->getProject($projectId);

        if (empty($project)) {
            throw new ProcessException('app.projectAllocation.projectNotExists'); //todo
        }

        $allocFrom = $allocation['from'];
        $allocTo = $allocation['to'];

        if ($allocFrom < $project['from'])
        {
            throw new ProcessException('app.projectAllocation.timeWindowError'); //todo
        }

        if($project['to'] !== null)
        {
            if($allocTo > $project['to'])
            {
                throw new ProcessException('app.projectAllocation.timeWindowError'); //todo
            }
        }

        //validace hooo
        /*
         *
         *
         *
          SELECT * from project_user_allocation where
    (`from` BETWEEN '2022-01-25'AND '2022-01-29') OR
    (`to` BETWEEN '2022-01-25' AND '2022-01-29') OR
    (`from` <= '2022-01-25' AND `to` >= '2022-01-29') and state = 'active'
         */
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
        $this->validateAllocation($allocation, $projectUserId, $projectId);

        //kontrola, jestli muze alokaci vlozit todo

        $this->allocationRepository->saveAllocation($allocation, $projectUserId);
        }
        catch (\PDOException $e)
        {
            $this->transaction->rollback();
            Debugger::log($e,ILogger::EXCEPTION);
            throw new ProcessException('app.baseForm.saveError');
        }

    }

    public function getProjectUserAllocationGridSelection(int $projectId): Selection
    {

        $usersOnProject = $this->projectUserRepository->getAllUsersOnProjectIds($projectId);
        return $this->allocationRepository->getAllAllocationsOnProject($usersOnProject);
    }





}