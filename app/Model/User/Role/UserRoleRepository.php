<?php

namespace App\Model\User\Role;


use App\Model\Repository\Base\BaseRepository;
use App\Model\Repository\Base\NotDeletedTraitRepository;
use Nette\Application\BadRequestException;
use Nette\Database\Context;
use Nette\Database\Explorer;
use Nette\Database\SqlLiteral;
use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\Selection;
use Nette\Http\FileUpload;

use Nette\Security\User;
use Nette\Utils\Random;


class UserRoleRepository extends BaseRepository
{
    use NotDeletedTraitRepository;

    public const TABLE_NAME = 'user_role';
    protected $tableName = self::TABLE_NAME;

    public const COL_ID = 'id';
    public const COL_TYPE = 'type';
    public const COL_NOT_DELETED = 'not_deleted';


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