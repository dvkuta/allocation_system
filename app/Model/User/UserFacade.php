<?php

namespace App\Model\User;

use App\Model\Exceptions\ProcessException;
use App\Model\Mapper\Mapper;
use App\Model\Repository\Base\IUserRepository;
use App\Model\Repository\Base\IUserRoleRepository;
use App\Model\Repository\Domain\User;
use App\Model\User\Role\ERole;
use App\Tools\ITransaction;
use Nette\Database\Table\Selection;
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



    public function getUser(int $id): ?User
    {
        return $this->userRepository->getUser($id);
    }

    public function getUserByLogin(string $login): ?User
    {
        if(empty($login))
        {
            return null;
        }

        return $this->userRepository->getUserByLogin($login);
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
     * @param string $firstname
     * @param string $lastname
     * @param string $email
     * @param string $login
     * @param string $workplace
     * @param string $password
     * @param array $roles
     * @throws ProcessException obsahujici kod chybove hlasky pro translator
     */
    public function createUser(string $firstname, string $lastname, string $email,
                               string $login, string $workplace, string $password, array $roles) :void
    {

        $user = new User(null, $firstname, $lastname, $email, $login, $workplace, $password, $roles);

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
    public function editUser(int $id, string $firstname, string $lastname, string $email,
                             string $login, string $workplace, string $password, array $roles): void
    {
        $user = new User($id, $firstname, $lastname, $email, $login, $workplace, $password, $roles);
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

    /**
     * @param int $user_id
     * @return array ve tvaru [id => typ]
     */
    public function findRolesForUser(int $user_id): array
    {
        return $this->userRoleRepository->findRolesForUser($user_id);
    }

    /**
     * Vrati jmena a prijmeni vsech uzivatelu v dane roli.
     * Vraci pole, jelikoz je vyuzita pouze jako zdroj vyctu moznosti pro formulare
     * @param ERole $role
     * @return array ve formatu [id => cele jmeno]
     */
    public function getAllUsersInRole(ERole $role): array
    {
        return $this->userRoleRepository->getAllUsersInRole($role);
    }

    /**
     * Objekt selection pro komponentu gridu uživatelů
     * @return Selection
     */
    public function getAllUsersGridSelection(): Selection
    {
        return $this->userRepository->getAllUsersGridSelection();
    }

}
