<?php

namespace App\Model\Project\ProjectUser;


use App\Model\Repository\Base\BaseRepository;
use Nette\Database\Explorer;
use Nette\Utils\ArrayHash;


class ProjectUserRepository extends BaseRepository
{

    public const TABLE_NAME = 'project_user';

    public const COL_ID = 'id';
    public const COL_USER_ID = 'user_id';
    public const COL_PROJECT_ID = 'project_id';
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

    public function saveUserToProject(ArrayHash $formValues, ?int $projectId)
    {
        $data = [
            self::COL_USER_ID => $formValues->user_id,
            self::COL_PROJECT_ID => $projectId,
            self::COL_DESCRIPTION => $formValues->description
        ];

        $this->saveFiltered($data);
    }

    public function getAllUsersThatDoesNotWorkOnProject(int $projectId): array
    {
        $users = $this->explorer->query('select user.id, CONCAT_WS(" ", firstname, lastname) as fullName
            from user
            where  user.id not in
            (select user_id
            from project_user
            where project_id = ? );', $projectId)->fetchPairs('id', 'fullName');

        return $users;

    }

    public function getAllUsersOnProject(int $projectId): \Nette\Database\Table\Selection
    {
        return $this->findAll()->where(self::COL_PROJECT_ID, $projectId);

    }

    public function getUser(int $allocationId)
    {

    }


}