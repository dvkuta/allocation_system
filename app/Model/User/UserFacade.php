<?php

namespace App\Model\User;

use App\Model\Exceptions\ProcessException;
use App\Model\User\Role\ERole;
use App\Model\User\Role\UserRoleRepository;
use App\Tools\Transaction;
use Nette\Security\Passwords;
use Nette\Utils\ArrayHash;
use Tracy\Debugger;
use Tracy\ILogger;

class UserFacade
{
    private UserRepository $userRepository;
    private UserRoleRepository $userRoleRepository;
    private Transaction $transaction;
    private Passwords $passwords;

    public function __construct(
        UserRepository     $userRepository,
        UserRoleRepository $userRoleRepository,
        Transaction $transaction,
        Passwords $passwords,
    )
    {

        $this->userRepository = $userRepository;
        $this->userRoleRepository = $userRoleRepository;
        $this->transaction = $transaction;
        $this->passwords = $passwords;
    }

    /**
     * @param string $email
     * @return void
     * @throws ProcessException
     */
    public function isEmailUnique(string $email): void
    {
        $emailExists = $this->userRepository->isEmailUnique($email);

        if($emailExists)
        {
            throw new ProcessException('app.user.emailExists');
        }
    }

    /**
     * @param string $login
     * @return void
     * @throws ProcessException
     */
    public function isLoginUnique(string $login): void
    {
        $loginExists = $this->userRepository->isLoginUnique($login);

        if($loginExists)
        {
            throw new ProcessException('app.user.loginExists');
        }
    }

    /**
     * Vytvori uzivatele a zalozi zaznam do databaze
     * Pokud uz existuje, tak ho upravi
     * @throws ProcessException
     */
    public function createUser(ArrayHash $user, ?int $userId): void
    {
        try {
            $this->transaction->begin();

            $this->isEmailUnique($user->email);
            $this->isLoginUnique($user->login);

            $user->password = $this->passwords->hash($user->password);

            $this->userRepository->saveUser($user, $userId);

            $this->transaction->commit();

        }
        catch (\PDOException $e)
        {
            $this->transaction->rollback();
            Debugger::log($e,ILogger::EXCEPTION);
            throw new ProcessException('app.baseForm.saveError');
        }
    }

    /**
     * @throws ProcessException
     */
    public function editUser(ArrayHash $user, int $userId,): void
    {
        try
        {
            $this->transaction->begin();

            $updatedUser = $this->userRepository->findRow($userId);

            if($updatedUser[UserRepository::COL_EMAIL] != $user->email)
            {
                $this->isEmailUnique($user->email);
            }

            if($updatedUser[UserRepository::COL_LOGIN] != $user->login)
            {
                $this->isLoginUnique($user->login);
            }


            if(!empty($user->password))
            {
                $user->password = $this->passwords->hash($user->password);
            }

            $this->userRepository->updateUser($user,$userId);

            $this->transaction->commit();

        }
        catch (\PDOException $e)
        {
            $this->transaction->rollback();
            Debugger::log($e,ILogger::EXCEPTION);
            throw new ProcessException('app.baseForm.saveError');
        }
    }

    /**
     * Vrati jmena a prijmeni vsech uzivatelu v dane roli.
     * @param ERole $role
     * @return array
     */
    public function getAllUsersInRole(ERole $role){
        $result = $this->userRepository->findAll()
            ->select('user.id, CONCAT_WS( " ", firstname, lastname) AS fullName' )->where('user_role.type', $role->value)
            ->fetchPairs(UserRepository::COL_ID, 'fullName');
        return $result;
    }
}
