<?php

namespace App\Model\Project\ProjectUserAllocation;


use App\Model\Project\ProjectRepository;
use App\Model\Project\ProjectUser\EState;
use App\Model\Repository\Base\BaseRepository;
use App\Model\User\UserRepository;
use DateTime;
use Nette\Database\Explorer;
use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\Selection;
use Nette\Utils\ArrayHash;


class ProjectUserAllocationRepository extends BaseRepository
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

    public function getAllocation(int $id): array
    {
        $allocation = $this->findRow($id);

        if($allocation)
        {
            /** @var ActiveRow $user */
            $user = $allocation->project_user->user;

            /** @var ActiveRow $project */
            $project = $allocation->project_user->project;



            $allocation = $allocation->toArray();
            $allocation['curr_project_id'] = $project->id;
            $allocation['curr_user_id'] = $user->id;
            $allocation['projectName'] = $project[ProjectRepository::COL_NAME];
            $allocation['userFullName'] = $user[UserRepository::COL_FIRSTNAME] . ' ' . $user[UserRepository::COL_LASTNAME];
            return $allocation;
        }
        else
        {
            return [];
        }
    }

    public function saveAllocation(ArrayHash $allocation, int $projectUserId, ?int $allocation_id = null)
    {
        $data = [
            self::COL_PROJECT_USER_ID => $projectUserId,
            self::COL_ALLOCATION => $allocation[self::COL_ALLOCATION],
            self::COL_FROM => $allocation[self::COL_FROM],
            self::COL_TO => $allocation[self::COL_TO],
            self::COL_DESCRIPTION => $allocation[self::COL_DESCRIPTION],
            self::COL_STATE => $allocation[self::COL_STATE]
        ];

        $this->saveFiltered($data, $allocation_id);
    }

    public function getAllAllocationsOnProject( array $usersOnProjectIds): Selection
    {
        $by = [self::COL_PROJECT_USER_ID => $usersOnProjectIds];
        return $this->findBy($by);
    }

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

    public function getSumOfAllAllocationsForUser(array $userProjectIds )
    {

    }



}