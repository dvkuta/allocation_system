<?php

namespace App\Model\User\Role;


use App\Model\Repository\Base\BaseRepository;
use App\Model\Repository\Base\IRoleRepository;
use Nette\Database\Explorer;


/**
 * Přístup k datům z tabulky Role
 */
class RoleRepository extends BaseRepository implements IRoleRepository
{
    public const TABLE_NAME = 'role';
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