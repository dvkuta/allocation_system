<?php

$container = require __DIR__ . '/../../../../bootstrap.php';

use App\Model\User\UserFacade;
use Tester\Assert;

class ValidateRolesTest extends Tester\TestCase
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
        $this->userRoleRepository = Mockery::mock(\App\Model\User\Role\UserRoleRepository::class, array($this->explorer))->makePartial();
    }

    public function tearDown()
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testEmptyRoles()
    {
        $roles = [];

        $userFacade = new UserFacade($this->userRepository, $this->transaction, $this->userRoleRepository, $this->passwords);

        Assert::exception(function () use ($roles, $userFacade) {
            $userFacade->validateRoles($roles);
        }, \App\Model\Exceptions\ProcessException::class);

    }

    public function testSuccessRoles()
    {
        $roles = [\App\Model\User\Role\ERole::worker->value, \App\Model\User\Role\ERole::project_manager->value];

        $userFacade = new UserFacade($this->userRepository, $this->transaction, $this->userRoleRepository, $this->passwords);

        Assert::noError(function () use ($roles, $userFacade) {
            $userFacade->validateRoles($roles);
        });

    }

    public function testErrorCombinationRoles()
    {
        $roles = [\App\Model\User\Role\ERole::superior->value, \App\Model\User\Role\ERole::worker->value];

        $userFacade = new UserFacade($this->userRepository, $this->transaction, $this->userRoleRepository, $this->passwords);

        Assert::exception(function () use ($roles, $userFacade) {
            $userFacade->validateRoles($roles);
        }, \App\Model\Exceptions\ProcessException::class);

    }




}

# Spuštění testovacích metod
(new ValidateRolesTest($container))->run();