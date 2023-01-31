<?php
namespace App\Security;
use App\Model\User\Role\UserRoleRepository;
use App\Model\User\UserRepository;
use Nette;
use Nette\Security\Passwords;
use Nette\Security\SimpleIdentity;

/**
 * Slouzi pro overeni uspesneho prihlaseni uzivatele
 */
class Authenticator implements Nette\Security\Authenticator
{
    private $passwords;
    private UserRepository $userRepository;
    private UserRoleRepository $userRoleRepository;

    public function __construct(
        Passwords          $passwords,
        UserRepository     $userRepository,
        UserRoleRepository $userRoleRepository,
    ) {
        $this->passwords = $passwords;
        $this->userRepository = $userRepository;
        $this->userRoleRepository = $userRoleRepository;
    }

    public function authenticate(string $login, string $password): SimpleIdentity
    {
        $user = $this->userRepository->getUserByLogin($login);


        if ($user === null) {
            throw new Nette\Security\AuthenticationException('app.user.loginError');
        }


        if (!$this->passwords->verify($password, $user->getPassword())) {
            throw new Nette\Security\AuthenticationException('app.user.loginError');
        }

        $roles = $this->userRoleRepository->findRolesForUser($user->getId());


        return new SimpleIdentity(
            $user->getId(),
            $roles, // nebo pole více rolí
            [
                'name' => $user->getFullName()
            ]
        );
    }
}