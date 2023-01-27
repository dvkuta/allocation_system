<?php

namespace App\Model\User\Role;


use App\Model\Repository\Base\BaseRepository;
use Nette\Database\Explorer;



class UserRoleRepository extends BaseRepository
{
    public const TABLE_NAME = 'user_role';
    protected $tableName = self::TABLE_NAME;

    public const COL_ID = 'id';
    public const COL_USER_ID = 'user_id';
    public const COL_ROLE_ID = 'role_id';


    public function __construct(
        Explorer $explorer,
    )
    {
        parent::__construct($explorer);

    }


    public function findRolesForUser(int $user_id): array
    {
        if($user_id <= 0)
        {
            return [];
        }

        $by = [self::COL_USER_ID => $user_id];

        return $this->findBy($by)
            ->joinWhere(RoleRepository::TABLE_NAME, 'role_id = role.id')
            ->fetchPairs('role.id', 'role.type');
    }


}