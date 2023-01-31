<?php

namespace App\Model\Project\ProjectUserAllocation;


use App\Model\DTO\AllocationDTO;
use App\Model\DTO\ProjectDTO;
use App\Model\Exceptions\ProcessException;
use App\Model\Project\ProjectUser\EState;
use App\Model\Repository\Base\IProjectRepository;
use App\Model\Repository\Base\IProjectUserAllocationRepository;
use App\Model\Repository\Base\IProjectUserRepository;
use App\Model\Repository\Base\ISuperiorUserRepository;
use App\Tools\ITransaction;
use DateTime;
use Nette\Database\Table\Selection;
use Tracy\Debugger;
use Tracy\ILogger;

/**
 * Akce spojené s alokacemi
 */
class ProjectUserAllocationFacade
{
    private IProjectUserRepository $projectUserRepository;
    private IProjectUserAllocationRepository $allocationRepository;
    private ITransaction $transaction;
    private IProjectRepository $projectRepository;

    public const MAX_ALLOCATION = 40;
    private ISuperiorUserRepository $superiorUserRepository;

    public function __construct(
        IProjectUserRepository                $projectUserRepository,
        IProjectUserAllocationRepository      $allocationRepository,
        IProjectRepository                    $projectRepository,
        ISuperiorUserRepository               $superiorUserRepository,
        ITransaction                          $transaction
    )
    {

        $this->projectUserRepository = $projectUserRepository;
        $this->allocationRepository = $allocationRepository;
        $this->transaction = $transaction;
        $this->projectRepository = $projectRepository;
        $this->superiorUserRepository = $superiorUserRepository;
    }


    /**
     * Ověři, jestli je časové okno alokace součástí projektu
     * @param AllocationDTO $allocation
     * @param int $projectId
     * @throws ProcessException
     */
    public function validateAllocationTime(AllocationDTO $allocation, int $projectId): void
    {
        /** @var ProjectDTO $project */
        $project = $this->projectRepository->getProject($projectId);

        if ($project === null) {
            throw new ProcessException('app.projectAllocation.projectNotExists');
        }

        $allocFrom = $allocation->getFrom();
        $allocTo = $allocation->getTo();

        $allocFrom->setTime(0,0);
        $allocTo->setTime(0,0);

        if ($allocFrom < $project->getFrom())
        {
            throw new ProcessException('app.projectAllocation.timeWindowError');
        }

        $projectTo = $project->getTo();

        if($projectTo !== null)
        {

            if($allocTo > $projectTo)
            {
                throw new ProcessException('app.projectAllocation.timeWindowError');
            }
        }
    }

    /**
     * Ověří, jestli je možné přiřadit alokaci pracovníkovi s id $userId
     * @param AllocationDTO $allocation - přiřazovaná alokace
     * @param int $userId - id pracovníka (uživatele), kterému se přiřazuje
     * @param AllocationDTO|null $storedAllocation vyplnit pouze při editaci alokace
     * @throws ProcessException Pokud není možné přiřadit alokaci
     */
    public function validateAllocationPossibility(AllocationDTO $allocation, int $userId, ?AllocationDTO $storedAllocation = null): void
    {

        $from = $allocation->getFrom()->setTime(0,0);
        $to = $allocation->getTo()->setTime(0,0);
        $storedAllocationValue = $storedAllocation === null ? 0 : $storedAllocation->getAllocation();
        $potentionalAllocation = 0;

        $storedState = $storedAllocation === null ? EState::ACTIVE : $storedAllocation->getState();

        $currentWorkLoad = $this->getWorkloadForUser($from, $to, $userId);

        if($storedState === EState::ACTIVE && $allocation->getState() === EState::ACTIVE)
        {
            $potentionalAllocation = $currentWorkLoad + $allocation->getAllocation() - $storedAllocationValue;
        }
        if($storedState === EState::ACTIVE && $allocation->getState() !== EState::ACTIVE)
        {
            $potentionalAllocation = 0;
        }
        if($storedState !== EState::ACTIVE && $allocation->getState() === EState::ACTIVE)
        {
            $inc = max($allocation->getAllocation(), $storedAllocationValue);
            $potentionalAllocation = $currentWorkLoad + $inc;
        }
        if($storedState !== EState::ACTIVE && $allocation->getState() !== EState::ACTIVE)
        {
            $potentionalAllocation = 0;
        }

        if($potentionalAllocation > self::MAX_ALLOCATION)
        {
            throw new ProcessException('app.projectAllocation.allocationError');
        }

    }

    /**
     * Ověří, jestli je pracovník přiřazen do projektu
     * @param int $userId
     * @param int $projectId
     * @return int id členství uživatele v projektu
     * @throws ProcessException pokud v projektu není
     */
    public function validateUserMembershipOnProject(int $userId, int $projectId): int
    {
        $projectUserId = $this->projectUserRepository->isUserOnProject($userId, $projectId);
        if ($projectUserId <= 0) {
            throw new ProcessException('app.projectAllocation.userNotOnProject');
        }

        return $projectUserId;
    }

    /**
     * Vytvoří alokaci na základě informací předaných v parametru - alokace musí mít nastavené
     * setCurrentProjectId(); asetCurrentWorkerId(); jinak nelze alokaci vytvořit
     * Zároveň ověří, jestli alokaci lze vytvořit (pracuje pracovník na projektu? existuje projekt? má pracovník volný úvazek? atd...)
     * @param AllocationDTO $allocation
     * @throws ProcessException
     */
    public function createAllocation(AllocationDTO $allocation): void
    {
        try {
        $projectId = $allocation->getCurrentProjectId() ?? throw new ProcessException('app.projectAllocation.projectNotExists');
        $this->transaction->begin();
        $user_id = $allocation->getCurrentWorkerId() ?? throw new ProcessException('app.projectAllocation.userNotOnProject');
        $projectUserId = $this->validateUserMembershipOnProject($user_id, $projectId);
        $this->validateAllocationTime($allocation, $projectId);

        $this->validateAllocationPossibility($allocation, $user_id);


        $this->allocationRepository->saveAllocation($allocation, $projectUserId);
        $this->transaction->commit();
        }
        catch (ProcessException $e)
        {
            $this->transaction->rollback();
            throw $e;
        }
        catch (\PDOException $e)
        {
            $this->transaction->rollback();
            Debugger::log($e,ILogger::EXCEPTION);
            throw new ProcessException('app.baseForm.saveError');
        }

    }

    /**
     * Upraví alokaci na základně informací předaných v parametru. Alokace musí mít nastavené ID, jinak nelze editovat
     * Zároveň ověří, jestli alokaci lze vytvořit (pracuje pracovník na projektu? existuje projekt? má pracovník volný úvazek? atd...)
     * @param AllocationDTO $allocation vyzaduje mit vyplnene id
     * @throws ProcessException
     */
    public function editAllocation(AllocationDTO $allocation): void
    {

        try {
            $allocationId = $allocation->getId();

            if($allocationId === null)
            {
                throw new ProcessException('app.projectAllocation.allocationNotExists');
            }

            $this->transaction->begin();
            /** @var AllocationDTO $storedAllocation */
            $storedAllocation = $this->allocationRepository->getAllocation($allocationId);
            if(empty($storedAllocation))
            {
                throw new ProcessException('app.projectAllocation.allocationNotExists');
            }
            $userId = $storedAllocation->getCurrentWorkerId() ?? throw new ProcessException('app.projectAllocation.allocationNotExists');
            $projectId = $storedAllocation->getCurrentProjectId() ?? throw new ProcessException('app.projectAllocation.allocationNotExists');
            $projectUserId = $this->validateUserMembershipOnProject($userId, $projectId);
            $this->validateAllocationTime($allocation, $projectId);

            $this->validateAllocationPossibility($allocation, $userId, $storedAllocation);

            $this->allocationRepository->saveAllocation($allocation, $projectUserId);

            $this->transaction->commit();
        }
        catch (ProcessException $e)
        {
            $this->transaction->rollback();
            throw $e;
        }
        catch (\PDOException $e)
        {
            $this->transaction->rollback();
            Debugger::log($e, ILogger::EXCEPTION);
            throw new ProcessException('app.baseForm.saveError');
        }



    }

    /**
     * Vrátí časové vytížení uživatele (součet alokovaných hodin v daný čas) s $userId v časovém intervalu $from a $to
     * @param DateTime $from
     * @param DateTime $to
     * @param int $userId
     * @return int
     */
    public function getWorkloadForUser(DateTime $from, DateTime $to, int $userId): int
    {
        $userProjectMemberships = $this->projectUserRepository->getAllProjectMembershipIds($userId);
        $currentWorkLoad = $this->allocationRepository->getCurrentWorkload($from, $to, $userProjectMemberships);
        return $currentWorkLoad;
    }

    /**
     * Vrátí časové vytížení (součet alokovaných hodin) uživatele s $userId právě v tento moment
     * @param int $userId
     * @return int
     */
    public function getCurrentWorkloadForUser(int $userId): int
    {
        $dateFrom = new DateTime();
        $dateFrom->setTime(0,0);
        $dateTo = new DateTime();
        $dateTo->setTime(0,0);//->modify("+1 day");
        return $this->getWorkloadForUser($dateFrom, $dateTo, $userId);

    }

    /**
     * Vrátí statistiku (součet všech alokací v hodinách) uživatele s $userId
     * @param int $userId
     * @return int
     */
    public function getAllAllocationStatistic(int $userId): int
    {
        $usersOnProject = $this->projectUserRepository->getAllProjectMembershipIds($userId);
        return $this->allocationRepository->getSumOfAllWorkload($usersOnProject);
    }


    /**
     * Vrátí objekt selection, který je dále využit pouze v gridu pro výpis všech alokací na projektu s $projectId
     * @param int $projectId
     * @return Selection
     */
    public function getProjectUserAllocationGridSelection(int $projectId): Selection
    {
        $usersOnProject = $this->projectUserRepository->getAllUsersOnProjectIds($projectId);
        return $this->allocationRepository->getAllAllocations($usersOnProject);
    }

    /**
     * Vrátí objekt selection, který je dále využit pouze v gridu pro výpis všech alokací pracovníka s $userId
     * @param int $userId
     * @return Selection
     */
    public function getAllUserAllocationsGridSelection(int $userId): Selection
    {
        $usersOnProject = $this->projectUserRepository->getAllProjectMembershipIds($userId);
        return $this->allocationRepository->getAllAllocations($usersOnProject);
    }

    /**
     * Vrátí objekt selection, který je dále využit pouze v gridu pro výpis všech alokací podřízených
     * @param int $userId
     * @return Selection
     */
    public function getAllSubordinateAllocationsGridSelection(int $superiorId): Selection
    {
        $subordinatesIds = $this->superiorUserRepository->getAllSubordinates($superiorId);
        $ids = $this->projectUserRepository->getAllProjectsMembershipsOfUserIds($subordinatesIds);
        return $this->allocationRepository->getAllAllocations($ids);
    }

    /**
     * Spočítá zobrazovaný stav projektu
     * @param DateTime $dateTo - čas do 'alokace'
     * @param EState $state
     * @return string vrací string pro překlad
     */
    public function calculateState(DateTime $dateTo, EState $state): string
    {
        if($state->value === EState::ACTIVE->value)
        {
            if($dateTo < new DateTime())
            {
                return 'past';
            }
        }

        return $state->value;
    }






}