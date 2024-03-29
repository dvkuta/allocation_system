<?php

namespace App\Model\User;

use App\Model\Mapper\Mapper;
use App\Model\Repository\Base\BaseRepository;

use App\Model\Repository\Base\IUserRepository;
use App\Model\Repository\Domain\User;
use Nette\Database\Explorer;
use Nette\Database\Table\Selection;

/**
 * Přístup k datům z tabulky user
 */
class UserRepository extends BaseRepository implements IUserRepository
{

    public const TABLE_NAME = 'user';

    public const COL_ID = 'id';
    public const COL_FIRSTNAME = 'firstname';
    public const COL_LASTNAME = 'lastname';
    public const COL_EMAIL ='email';
    public const COL_LOGIN = 'login';
    public const COL_PASSWORD ='password';
    public const COL_WORKPLACE ='workplace';

    protected $tableName = self::TABLE_NAME;


    public function __construct(
        Explorer $explorer,
    )
    {
        parent::__construct($explorer);

    }

    /**
     * Kontrola, jestli je registrovan uzivatel se zadanym emailem
     * @param string $email
     * @return bool
     */
    public function emailExists(string $email): bool
    {
        return $this->isColumnValueUsed(self::COL_EMAIL, $email);
    }

    public function getUser(int $id): ?User
    {
        $user =  $this->findRow($id);

        if($user)
        {
            return Mapper::mapUser($user->id,
                $user->firstname,
                $user->lastname,
                $user->email,
                $user->login,
                $user->workplace,
            );
        }
        else
        {
            return null;
        }
    }

    public function getUserByLogin(string $login): ?User
    {
        $by = [self::COL_LOGIN => $login];
        $user =  $this->findBy($by)->fetch();

        if($user)
        {
            return Mapper::mapUser(
                $user->id,
                $user->firstname,
                $user->lastname,
                $user->email,
                $user->login,
                $user->workplace,
                $user->password,
            );
        }
        else
        {
            return null;
        }
    }

    /**
     * Kontrola, jestli je registrovan uzivatel se zadanym loginem
     * @param string $login
     * @return bool
     */
    public function loginExists(string $login): bool
    {
        return $this->isColumnValueUsed(self::COL_LOGIN, $login);
    }

    private function isColumnValueUsed(string $column, $value): bool
    {
        $by = [$column => $value];
        return $this->findBy($by)->count() > 0;
    }

    /**
     * Uloží uživatele v databází a vrátí nová data pro nastavení rolí
     * @param User $user
     * @return User
     */
    public function saveUser(User $user): User
    {
        $data = [
            self::COL_EMAIL => $user->getEmail(),
            self::COL_LOGIN => $user->getLogin(),
            self::COL_FIRSTNAME => $user->getFirstname(),
            self::COL_LASTNAME => $user->getLastname(),
            self::COL_PASSWORD => $user->getPassword(),
            self::COL_WORKPLACE => $user->getWorkplace()
            ];

        $result = $this->saveFiltered($data)->toArray();
        return new User(
            $result[self::COL_ID],
            $result[self::COL_FIRSTNAME],
            $result[self::COL_LASTNAME],
            $result[self::COL_EMAIL],
            $result[self::COL_LOGIN],
            $result[self::COL_WORKPLACE]
        );
    }

    /**
     * Upraví uživatele v databází a vrátí nová data pro nastavení rolí
     */
    public function updateUser(User $user): User
    {
        $data = [
            self::COL_EMAIL => $user->getEmail(),
            self::COL_LOGIN => $user->getLogin(),
            self::COL_FIRSTNAME => $user->getFirstname(),
            self::COL_LASTNAME => $user->getLastname(),
            self::COL_WORKPLACE => $user->getWorkplace()
        ];

        if(!empty($user->getPassword()))
        {
            $data[self::COL_PASSWORD] = $user->getPassword();
        }

        $result = $this->saveFiltered($data, $user->getId())->toArray();
        return new User(
        $result[self::COL_ID],
        $result[self::COL_FIRSTNAME],
        $result[self::COL_LASTNAME],
        $result[self::COL_EMAIL],
        $result[self::COL_LOGIN],
        $result[self::COL_WORKPLACE]
    );
    }

    /**
     * Objekt selection pro komponentu gridu uživatelů
     * @return Selection
     */
    public function getAllUsersGridSelection(): Selection
    {
        return $this->findAll();
    }

}