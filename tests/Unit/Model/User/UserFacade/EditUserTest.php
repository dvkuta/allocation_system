<?php

$container = require __DIR__ . '/../../../../bootstrap.php';

use App\Model\DTO\UserDTO;
use App\Model\User\Role\UserRoleRepository;
use App\Model\User\UserFacade;
use Tester\Assert;

class EditUserTest extends Tester\TestCase
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

    public function testEditUser()
    {
        $user = new UserDTO(5,
            'Lada',
            'Horak',
            'lh@t.cz',
            'lada',
            'KIV',
            'heslo'
        );

        $updatedUser = new UserDTO(5,
            'Lada',
            'Horak',
            'lhh@t.cz',
            'ladaaa',
            'KIV',
            ''
        );

        $roles = [\App\Model\User\Role\ERole::worker->value => \App\Model\User\Role\ERole::worker->value,
            \App\Model\User\Role\ERole::project_manager->value => \App\Model\User\Role\ERole::project_manager->value];
        $user->setRoles($roles);
        $updatedUser->setRoles($roles);

        $this->transaction
            ->shouldReceive('begin')
            ->times(1)
            ->andReturn()
            ->shouldReceive('commit')
            ->times(1)
            ->andReturn();

        $this->userRepository
            ->shouldReceive('getUser')
            ->with($user->getId())
            ->times(1)
            ->andReturn($updatedUser);

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



        $this->userRepository
            ->shouldReceive('updateUser')
            ->with($user)
            ->times(1)
            ->andReturn($user);

        $this->userRoleRepository
            ->shouldReceive('saveUserRoles')
            ->with(Mockery::subset($user->getRoles()), $user->getId())
            ->times(1)
            ->andReturn();

        $userFacade = new UserFacade($this->userRepository, $this->transaction, $this->userRoleRepository, $this->passwords);

        Assert::noError(function () use ($user, $userFacade) {
            $userFacade->editUser($user);
        });
    }

    public function testEditUserFailure()
    {
        $user = new UserDTO(5,
            'Lada',
            'Horak',
            'lh@t.cz',
            'lada',
            'KIV',
            'heslo'
        );

        $updatedUser = null;

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
            ->shouldReceive('getUser')
            ->with($user->getId())
            ->times(1)
            ->andReturn($updatedUser);


        $userFacade = new UserFacade($this->userRepository, $this->transaction, $this->userRoleRepository, $this->passwords);

        Assert::exception(function () use ($user, $userFacade) {
            $userFacade->editUser($user);
        }, \App\Model\Exceptions\ProcessException::class);
    }

    public function testEditUserFailure_1()
    {
        $user = new UserDTO(5,
            'Lada',
            'Horak',
            'lh@t.cz',
            'lada',
            'KIV',
            'heslo'
        );

        $updatedUser = new UserDTO(5,
            'Lada',
            'Horak',
            'lhh@t.cz',
            'ladaaa',
            'KIV',
            ''
        );

        $roles = [\App\Model\User\Role\ERole::worker->value => \App\Model\User\Role\ERole::worker->value,
            \App\Model\User\Role\ERole::project_manager->value => \App\Model\User\Role\ERole::project_manager->value];
        $user->setRoles($roles);
        $updatedUser->setRoles($roles);

        $this->transaction
            ->shouldReceive('begin')
            ->times(1)
            ->andReturn()
            ->shouldReceive('rollback')
            ->times(1)
            ->andReturn();

        $this->userRepository
            ->shouldReceive('getUser')
            ->with($user->getId())
            ->times(1)
            ->andReturn($updatedUser);

        $this->userRepository
            ->shouldReceive('emailExists')
            ->with($user->getEmail())
            ->times(1)
            ->andReturn(true);



        $userFacade = new UserFacade($this->userRepository, $this->transaction, $this->userRoleRepository, $this->passwords);

        Assert::exception(function () use ($user, $userFacade) {
            $userFacade->editUser($user);
        },\App\Model\Exceptions\ProcessException::class);
    }

    public function testEditUserFailure_2()
    {
        $user = new UserDTO(5,
            'Lada',
            'Horak',
            'lh@t.cz',
            'lada',
            'KIV',
            'heslo'
        );

        $updatedUser = new UserDTO(5,
            'Lada',
            'Horak',
            'lhh@t.cz',
            'ladaaa',
            'KIV',
            ''
        );

        $roles = [\App\Model\User\Role\ERole::worker->value => \App\Model\User\Role\ERole::worker->value,
            \App\Model\User\Role\ERole::project_manager->value => \App\Model\User\Role\ERole::project_manager->value];
        $user->setRoles($roles);
        $updatedUser->setRoles($roles);

        $this->transaction
            ->shouldReceive('begin')
            ->times(1)
            ->andReturn()
            ->shouldReceive('rollback')
            ->times(1)
            ->andReturn();

        $this->userRepository
            ->shouldReceive('getUser')
            ->with($user->getId())
            ->times(1)
            ->andReturn($updatedUser);

        $this->userRepository
            ->shouldReceive('emailExists')
            ->with($user->getEmail())
            ->times(1)
            ->andReturn(false);

        $this->userRepository
            ->shouldReceive('loginExists')
            ->with($user->getLogin())
            ->times(1)
            ->andReturn(true);


        $userFacade = new UserFacade($this->userRepository, $this->transaction, $this->userRoleRepository, $this->passwords);

        Assert::exception(function () use ($user, $userFacade) {
            $userFacade->editUser($user);
        },\App\Model\Exceptions\ProcessException::class);
    }



}

# Spuštění testovacích metod
(new EditUserTest($container))->run();