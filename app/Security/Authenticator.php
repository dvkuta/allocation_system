<?php
namespace App\Security;
use App\Model\User\Role\UserRoleRepository;
use App\Model\User\UserFacade;
use App\Model\User\UserRepository;
use Nette;
use Nette\Security\Passwords;
use Nette\Security\SimpleIdentity;

/**
 * Slouzi pro overeni uspesneho prihlaseni uzivatele
 */
class Authenticator implements Nette\Security\Authenticator
{
    private Passwords $passwords;
    private UserFacade $userFacade;

    public function __construct(
        Passwords          $passwords,
        UserFacade $userFacade
    ) {
        $this->passwords = $passwords;
        $this->userFacade = $userFacade;
    }

    public function authenticate(string $login, string $password): SimpleIdentity
    {
        $user = $this->userFacade->getUserByLogin($login);


        if ($user === null) {
            throw new Nette\Security\AuthenticationException('app.user.loginError');
        }


        if (!$this->passwords->verify($password, $user->getPassword())) {
            throw new Nette\Security\AuthenticationException('app.user.loginError');
        }

        $roles = $this->userFacade->findRolesForUser($user->getId());


        return new SimpleIdentity(
            $user->getId(),
            $roles,
            [
                'name' => $user->getFullName()
            ]
        );
    }
}