<?php

namespace App\Model\User;

use App\Model\Exceptions\ProcessException;
use App\Model\User\Role\ERole;
use App\Model\User\Role\RoleRepository;
use App\Model\User\Role\UserRoleRepository;
use App\Tools\ITransaction;
use Nette\Security\Passwords;
use Nette\Utils\ArrayHash;
use Tracy\Debugger;
use Tracy\ILogger;

class UserFacade
{
    private UserRepository $userRepository;
    private RoleRepository $roleRepository;
    private ITransaction $transaction;
    private Passwords $passwords;
    private UserRoleRepository $userRoleRepository;

    public function __construct(
        UserRepository $userRepository,
        RoleRepository $roleRepository,
        ITransaction    $transaction,
        UserRoleRepository $userRoleRepository,
        Passwords      $passwords,
    )
    {

        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;
        $this->transaction = $transaction;
        $this->passwords = $passwords;
        $this->userRoleRepository = $userRoleRepository;
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

            $savedUser = $this->userRepository->saveUser($user);
            $this->userRoleRepository->saveUserRoles($user->user_role, $savedUser['id']);


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
            $this->userRoleRepository->saveUserRoles($user->user_role, $userId);

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
     * @param ERole|null $role
     * @return array
     */
    public function getAllUsersInfoForSelect(?ERole $role = null): array
    {
        $users = $this->userRepository->findAll()
            ->select('user.id, CONCAT_WS( " ", firstname, lastname) AS fullName');

        if($role !== null)
        {
            $users->joinWhere('user_role', 'user.id = user_role.id')
                ->where('user_role.role_id', $role->value);
        }

        return $users->fetchPairs(UserRepository::COL_ID, 'fullName');
    }
}
