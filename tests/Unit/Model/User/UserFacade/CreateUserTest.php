<?php

$container = require __DIR__ . '/../../../../bootstrap.php';

use App\Model\DTO\UserDTO;
use App\Model\User\Role\UserRoleRepository;
use App\Model\User\UserFacade;
use Tester\Assert;

class CreateUserTest extends Tester\TestCase
{
    private Nette\DI\Container $container;

    private $transaction;
    private $userRepository;
    private $passwords;
    private $userRoleRepository;
    private $explorer;

    public function __construct(\Nette\DI\Container $container)
    {
        $this->container = $container;
    }

    public function setUp()
    {
        parent::setUp();

        $this->explorer = Mockery::mock(\Nette\Database\Explorer::class)->makePartial();
        $this->transaction = Mockery::mock(App\Tools\Transaction::class)->makePartial();
        $this->userRepository = Mockery::mock(App\Model\User\UserRepository::class, array($this->explorer))->makePartial();
        $this->passwords = Mockery::mock(Nette\Security\Passwords::class)->makePartial();
        $this->userRoleRepository = Mockery::mock(UserRoleRepository::class, array($this->explorer))->makePartial();
    }

    public function tearDown()
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testCreateUser()
    {
        $user = new UserDTO(null,
            'Lada',
            'Horak',
            'lh@t.cz',
            'lada',
            'KIV',
            'heslo'
        );

        $roles = [\App\Model\User\Role\ERole::worker->value => \App\Model\User\Role\ERole::worker->value,
            \App\Model\User\Role\ERole::project_manager->value => \App\Model\User\Role\ERole::project_manager->value];
        $user->setRoles($roles);

        $this->transaction
            ->shouldReceive('begin')
            ->times(1)
            ->andReturn()
            ->shouldReceive('commit')
            ->times(1)
            ->andReturn();


        $this->userRepository
            ->shouldReceive('emailExists')
            ->with($user->getEmail())
            ->times(1)
            ->andReturn(false);

        $this->userRepository
            ->shouldReceive('loginExists')
            ->with($user->getLogin())
            ->times(1)
            ->andReturn(false);

        $this->passwords
            ->shouldReceive('hash')
            ->with($user->getPassword())
            ->times(1)
            ->andReturn($user->getPassword());

        $savedUser = new UserDTO(5,
            'Lada',
            'Horak',
            'lh@t.cz',
            'lada',
            'KIV',
            ''
        );
        $savedUser->setRoles($roles);



        $this->userRepository
            ->shouldReceive('saveUser')
            ->with($user)
            ->times(1)
            ->andReturn($savedUser);

        $this->userRoleRepository
            ->shouldReceive('saveUserRoles')
            ->with(Mockery::subset($user->getRoles()), $savedUser->getId())
            ->times(1)
            ->andReturn();

        $userFacade = new UserFacade($this->userRepository, $this->transaction, $this->userRoleRepository, $this->passwords);

        Assert::noError(function () use ($user, $userFacade) {
            $userFacade->createUser($user);
        });
    }

    public function testCreateUserLoginError()
    {
        $user = new UserDTO(null,
            'Lada',
            'Horak',
            'lh@t.cz',
            'lada',
            'KIV',
            'heslo'
        );

        $roles = [\App\Model\User\Role\ERole::worker->value => \App\Model\User\Role\ERole::worker->value,
            \App\Model\User\Role\ERole::project_manager->value => \App\Model\User\Role\ERole::project_manager->value];
        $user->setRoles($roles);

        $this->transaction
            ->shouldReceive('begin')
            ->times(1)
            ->andReturn()
            ->shouldReceive('rollback')
            ->times(1)
            ->andReturn();


        $this->userRepository
            ->shouldReceive('loginExists')
            ->with($user->getLogin())
            ->times(1)
            ->andReturn(true);



        $userFacade = new UserFacade($this->userRepository, $this->transaction, $this->userRoleRepository, $this->passwords);

        Assert::exception(function () use ($user, $userFacade) {
            $userFacade->createUser($user);
        }, \App\Model\Exceptions\ProcessException::class);
    }

    public function testCreateUserEmailError()
    {
        $user = new UserDTO(null,
            'Lada',
            'Horak',
            'lh@t.cz',
            'lada',
            'KIV',
            'heslo'
        );

        $roles = [\App\Model\User\Role\ERole::worker->value => \App\Model\User\Role\ERole::worker->value,
            \App\Model\User\Role\ERole::project_manager->value => \App\Model\User\Role\ERole::project_manager->value];
        $user->setRoles($roles);

        $this->transaction
            ->shouldReceive('begin')
            ->times(1)
            ->andReturn()
            ->shouldReceive('rollback')
            ->times(1)
            ->andReturn();


        $this->userRepository
            ->shouldReceive('emailExists')
            ->with($user->getEmail())
            ->times(1)
            ->andReturn(true);

        $this->userRepository
            ->shouldReceive('loginExists')
            ->with($user->getLogin())
            ->times(1)
            ->andReturn(false);



        $userFacade = new UserFacade($this->userRepository, $this->transaction, $this->userRoleRepository, $this->passwords);

        Assert::exception(function () use ($user, $userFacade) {
            $userFacade->createUser($user);
        }, \App\Model\Exceptions\ProcessException::class);
    }



}

# Spuštění testovacích metod
(new CreateUserTest($container))->run();