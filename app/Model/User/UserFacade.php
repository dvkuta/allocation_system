<?php

namespace App\Model\User;

use App\Model\DTO\UserDTO;
use App\Model\Exceptions\ProcessException;
use App\Model\Repository\Base\IUserRepository;
use App\Model\Repository\Base\IUserRoleRepository;
use App\Model\User\Role\ERole;
use App\Tools\ITransaction;
use Nette\Security\Passwords;
use Tracy\Debugger;
use Tracy\ILogger;

/**
 * Komplexni akce tykajici se uzivatele
 */
class UserFacade
{
    private IUserRepository $userRepository;
    private ITransaction $transaction;
    private Passwords $passwords;
    private IUserRoleRepository $userRoleRepository;

    public function __construct(
        IUserRepository     $userRepository,
        ITransaction       $transaction,
        IUserRoleRepository $userRoleRepository,
        Passwords          $passwords,
    )
    {

        $this->userRepository = $userRepository;
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
        $emailExists = $this->userRepository->emailExists($email);

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
        $loginExists = $this->userRepository->loginExists($login);

        if($loginExists)
        {
            throw new ProcessException('app.user.loginExists');
        }
    }

    /**
     * Overi, jestli jsou role validni, tj - nejsou prazde, nebo neobsahuji zaroven workera a superiora
     * @param array $roles ve tvaru [idRole, idRole2, ....]
     * @return void
     * @throws ProcessException
     */
    public function validateRoles(array $roles): void
    {
        if(empty($roles))
        {
            throw new ProcessException('app.user.roleEmptyError');
        }
        if(in_array(ERole::worker->value, $roles) && in_array(ERole::superior->value, $roles))
        {

            throw new ProcessException('app.user.roleCombError');
        }
    }

    /**
     * Vytvori uzivatele v databazi a overi, jestli neexistuje duplicita emailu, loginu
     * Pokud uz existuje, tak ho upravi
     * @throws ProcessException obsahujici kod chybove hlasky pro translator
     */
    public function createUser(UserDTO $user): void
    {
        try {
            $this->transaction->begin();

            $this->isLoginUnique($user->getLogin());
            $this->isEmailUnique($user->getEmail());

            $this->validateRoles($user->getRoles());

            $hash = $this->passwords->hash($user->getPassword());
            $user->setPassword($hash);

            $savedUser = $this->userRepository->saveUser($user);
            $this->userRoleRepository->saveUserRoles($user->getRoles(), $savedUser->getId());

            $this->transaction->commit();

        }
        catch (ProcessException $e)
        {
            $this->transaction->rollback();
            throw $e;
        }
        catch (\PDOException $e)
        {
            $this->transaction->rollback();
            Debugger::log($e,ILogger::EXCEPTION);
            throw new ProcessException('app.baseForm.saveError');
        }
    }

    /**
     * Upravi uzivatele v databazi a overi, jestli neexistuje duplicita emailu, loginu
     * Pokud uz existuje, tak ho upravi
     * @throws ProcessException obsahujici kod chybove hlasky pro translator
     */
    public function editUser(UserDTO $user): void
    {
        try
        {
            $this->transaction->begin();


            $updatedUser = $this->userRepository->getUser($user->getId());

            if($updatedUser === null)
            {
                throw new ProcessException('User not exists');
            }

            if($updatedUser->getEmail() != $user->getEmail())
            {
                $this->isEmailUnique($user->getEmail());
            }

            if($updatedUser->getLogin() != $user->getLogin())
            {
                $this->isLoginUnique($user->getLogin());
            }

            $this->validateRoles($user->getRoles());

            if(!empty($user->getPassword()))
            {
                $hash = $this->passwords->hash($user->getPassword());
                $user->setPassword($hash);
            }

            $savedUser = $this->userRepository->updateUser($user);
            $this->userRoleRepository->saveUserRoles($user->getRoles(), $savedUser->getId());
            $this->transaction->commit();

        }
        catch (ProcessException $e)
        {
            $this->transaction->rollback();
            throw $e;
        }
        catch (\PDOException $e)
        {
            $this->transaction->rollback();
            Debugger::log($e,ILogger::EXCEPTION);
            throw new ProcessException('app.baseForm.saveError');
        }
    }

}
