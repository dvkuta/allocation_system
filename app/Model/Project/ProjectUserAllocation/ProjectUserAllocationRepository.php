<?php

namespace App\Model\Project\ProjectUserAllocation;


use App\Model\Mapper\Mapper;
use App\Model\Project\ProjectRepository;
use App\Model\Repository\Base\BaseRepository;
use App\Model\Repository\Base\IProjectUserAllocationRepository;
use App\Model\Repository\Domain\Allocation;
use App\Model\User\UserRepository;
use DateTime;
use Nette\Database\Explorer;
use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\Selection;


/**
 * Přístup k datům z tabulky project_user_allocation
 */
class ProjectUserAllocationRepository extends BaseRepository implements IProjectUserAllocationRepository
{

    public const TABLE_NAME = 'project_user_allocation';

    public const COL_ID = 'id';
    public const COL_PROJECT_USER_ID = 'project_user_id';
    public const COL_ALLOCATION = 'allocation';
    public const COL_FROM = 'from';
    public const COL_TO = 'to';
    public const COL_DESCRIPTION = 'description';
    public const COL_STATE = 'state';

    protected $tableName = self::TABLE_NAME;


    public function __construct(
        Explorer $explorer,
    )
    {
        parent::__construct($explorer);

    }

    public function getAllocation(int $id): ?Allocation
    {
        $allocation = $this->findRow($id);

        if($allocation)
        {
            /** @var ActiveRow $user */
            $user = $allocation->project_user->user;

            /** @var ActiveRow $project */
            $project = $allocation->project_user->project;

            return Mapper::mapAllocation(
                $allocation->id,
                $allocation->project_user_id,
                $allocation->allocation,
                $allocation->from,
                $allocation->to,
                $allocation->description,
                EState::from($allocation->state),
                $project->id,
                $project[ProjectRepository::COL_NAME],
                $user->id,
                $user[UserRepository::COL_FIRSTNAME] . ' ' . $user[UserRepository::COL_LASTNAME]
            );
        }
        else
        {
            return null;
        }
    }


    public function saveAllocation(Allocation $allocation, int $projectUserId)
    {
        $data = [
            self::COL_PROJECT_USER_ID => $projectUserId,
            self::COL_ALLOCATION => $allocation->getAllocation(),
            self::COL_FROM => $allocation->getFrom(),
            self::COL_TO => $allocation->getTo(),
            self::COL_DESCRIPTION => $allocation->getDescription(),
            self::COL_STATE => $allocation->getState()->value
        ];

        $this->saveFiltered($data, $allocation->getId());
    }

    /**
     * selekce pro grid
     * @param array $usersOnProjectIds
     * @return Selection
     */
    public function getAllAllocations(array $usersOnProjectIds): Selection
    {
        $by = [self::COL_PROJECT_USER_ID => $usersOnProjectIds];
        return $this->findBy($by);
    }

    /**
     * Spocita pracovni zatizeni pracovnika v case
     * @param DateTime $from
     * @param DateTime $to
     * @param array $userProjectIds pole vsech clenstvi na projektech
     * @return int soucet v hodinach
     */
    public function getCurrentWorkload(DateTime $from, DateTime $to, array $userProjectIds): int
    {
        $where = [
            self::COL_FROM . ' BETWEEN ? AND ?' => [$from, $to],
            self::COL_TO . ' BETWEEN ? AND ?' => [$from, $to],
            self::COL_FROM . ' <= ? AND ' . self::COL_TO . ' >= ?' => [$from, $to]
            ];

        $result = $this->findAll()
            ->whereOr($where)
            ->where(self::COL_PROJECT_USER_ID, $userProjectIds)
            ->where(self::COL_STATE, EState::ACTIVE->value)
            ->select('SUM(allocation) AS currWorkLoad')->fetch();

        return intval($result->currWorkLoad);
    }

    /**
     * Vrati soucet vsech alokaci
     * @param array $userProjectIds pole vsech clenstvi na projektech
     * @return int soucet v hodinach
     */
    public function getSumOfAllWorkload(array $userProjectIds ): int
    {
        $result = $this->getAllAllocations($userProjectIds)->select('SUM(allocation) AS allWorkLoad')->fetch();
        return intval($result->allWorkLoad);
    }

    /**
     * Kontrola, jestli je user s id projekt manager projektu, na kterem se tvori alokace
     * @param int $userId
     * @param int $allocationId
     * @return bool
     */
    public function isUserProjectManagerOfProjectOfThisAllocation(int $userId, int $allocationId): bool
    {
        $allocation = $this->findRow($allocationId);

        if($allocation === null)
        {
            return false;
        }

        $project = $allocation->project_user->project;

        if($project === null)
        {
            return false;
        }

        return intval($project->user_id) === $userId;

    }



}