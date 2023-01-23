<?php

namespace App\Model\User\Role;


use App\Model\Repository\Base\BaseRepository;
use Nette\Database\Explorer;



class UserRoleRepository extends BaseRepository
{
    public const TABLE_NAME = 'user_role';
    protected $tableName = self::TABLE_NAME;

    public const COL_ID = 'id';
    public const COL_TYPE = 'type';


    public function __construct(
        Explorer $explorer,
    )
    {
        parent::__construct($explorer);

    }

    public function fetchDataForSelect(): array
    {
        return $this->findAll()->fetchPairs(self::COL_ID,self::COL_TYPE);
    }

}