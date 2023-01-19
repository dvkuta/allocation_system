<?php

namespace App\Model\User;


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



class UserRepository extends BaseRepository
{
    use NotDeletedTraitRepository;

    public const TABLE_NAME = 'user';
    protected $tableName = self::TABLE_NAME;

    public const COLUMN_ID = 'id';
    public const COLUMN_FIRSTNAME = 'firstname';
    public const COLUMN_LASTNAME = 'lastname';
    public const COLUMN_EMAIL = 'email';
    public const COLUMN_WORKSPACE = 'workspace';

    public const COLUMN_ACTIVE = 'active';
    public const COLUMN_NOT_DELETED = 'not_deleted';

    public const MIN_PSWD_LEN = 8;


    public function __construct(
        Explorer $explorer,
    )
    {
        parent::__construct($explorer);

    }

}