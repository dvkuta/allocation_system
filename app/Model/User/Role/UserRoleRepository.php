<?php

namespace App\Model\User\Role;


use App\Model\Repository\Base\BaseRepository;
use App\Model\User\UserRepository;
use Nette\Database\Explorer;


/**
 * Přístup k datům z tabulky UserRole
 */
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


    /**
     * @param int $user_id
     * @return array ve tvaru [id => typ]
     */
    public function findRolesForUser(int $user_id): array
    {
        if($user_id <= 0)
        {
            return [];
        }

        $by = [self::COL_USER_ID => $user_id];


        return $this->findBy($by)->select('role_id, type')
            ->joinWhere(RoleRepository::TABLE_NAME, 'role_id = role.id')
            ->fetchPairs('role_id', 'type');
    }

    /**
     * Vrati jmena a prijmeni vsech uzivatelu v dane roli.
     * Vraci pole, jelikoz je vyuzita pouze jako zdroj vyctu moznosti pro formulare
     * @param ERole $role
     * @return array ve formatu [id => cele jmeno]
     */
    public function getAllUsersInRole(ERole $role): array
    {
        $users = $this->findAll()
            ->select('user.id, CONCAT_WS( " ", firstname, lastname) AS fullName')
            ->where('role_id',$role->value);

        return $users->fetchPairs(UserRepository::COL_ID, 'fullName');
    }

    public function saveUserRoles(array $roles, int $userId)
    {
        $this->findAll()->where([self::COL_USER_ID => $userId])->delete();

        if(empty($roles))
        {
            return;
        }


        $insertData = [];
        foreach ($roles as $role)
        {
            $insertData[] = [self::COL_USER_ID => $userId, self::COL_ROLE_ID => $role];
        }
        $this->insert($insertData);


    }


}