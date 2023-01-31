<?php

$container = require __DIR__ . '/../../../../bootstrap.php';

use App\Model\User\UserFacade;
use Tester\Assert;

class IsLoginUniqueTest extends Tester\TestCase
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

    public function testIsLoginUniqueTrue()
    {
        $login = "pepa";
        $this->userRepository
            ->shouldReceive('loginExists')
            ->with($login)
            ->times(1)
            ->andReturn(false);
        $userFacade = new UserFacade($this->userRepository, $this->transaction, $this->userRoleRepository, $this->passwords);

        Assert::noError(function () use ($login, $userFacade) {
            $userFacade->isLoginUnique($login);
        });

    }

    public function testIsLoginUniqueFalse()
    {
        $login = "pepa";
        $this->userRepository
            ->shouldReceive('loginExists')
            ->with($login)
            ->times(1)
            ->andReturn(true);
        $userFacade = new UserFacade($this->userRepository, $this->transaction, $this->userRoleRepository, $this->passwords);

        Assert::exception(function () use ($login, $userFacade) {
            $userFacade->isLoginUnique($login);
        }, \App\Model\Exceptions\ProcessException::class);

    }



}

# Spuštění testovacích metod
(new IsLoginUniqueTest($container))->run();