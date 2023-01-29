<?php

namespace App\Model\User;


use App\Model\DTO\UserDTO;
use App\Model\Repository\Base\BaseRepository;

use Nette\Database\Explorer;
use Nette\Database\Table\ActiveRow;
use Nette\Utils\ArrayHash;


class UserRepository extends BaseRepository
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
    public function isEmailUnique(string $email): bool
    {
        return $this->isColumnValueUsed(self::COL_EMAIL, $email);
    }

    public function getUser(int $id): ?UserDTO
    {
        $user =  $this->findRow($id);

        if($user)
        {
            return new UserDTO(
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

    /**
     * Kontrola, jestli je registrovan uzivatel se zadanym loginem
     * @param string $login
     * @return bool
     */
    public function isLoginUnique(string $login): bool
    {
        return $this->isColumnValueUsed(self::COL_LOGIN, $login);
    }

    private function isColumnValueUsed(string $column, $value): bool
    {
        $by = [$column => $value];
        return $this->findBy($by)->count() > 0;
    }

    //taky vraci userDTO
    public function saveUser(ArrayHash $user): array
    {
        $data = [
            self::COL_EMAIL => $user->email,
            self::COL_LOGIN => $user->login,
            self::COL_FIRSTNAME => $user->firstname,
            self::COL_LASTNAME => $user->lastname,
            self::COL_PASSWORD => $user->password,
            self::COL_WORKPLACE => $user->workplace
            ];

        return $this->saveFiltered($data)->toArray();
    }

    //taky vraci userDTO
    public function updateUser(ArrayHash $user, int $userId): array
    {
        $data = [
            self::COL_EMAIL => $user->email,
            self::COL_LOGIN => $user->login,
            self::COL_FIRSTNAME => $user->firstname,
            self::COL_LASTNAME => $user->lastname,
            self::COL_WORKPLACE => $user->workplace
        ];

        if(!empty($user->password))
        {
            $data[self::COL_PASSWORD] = $user->password;
        }

        return $this->saveFiltered($data, $userId)->toArray();
    }

}