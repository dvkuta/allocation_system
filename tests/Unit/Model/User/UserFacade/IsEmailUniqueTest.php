<?php

$container = require __DIR__ . '/../../../../bootstrap.php';

use App\Model\User\UserFacade;
use Tester\Assert;

class IsEmailUniqueTest extends Tester\TestCase
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

    public function testIsEmailUniqueTrue()
    {
        $email = "pepa@seznam.cz";
        $this->userRepository
            ->shouldReceive('emailExists')
            ->with($email)
            ->times(1)
            ->andReturn(false);
        $userFacade = new UserFacade($this->userRepository, $this->transaction, $this->userRoleRepository, $this->passwords);

        Assert::noError(function () use ($email, $userFacade) {
            $userFacade->isEmailUnique($email);
        });

    }

    public function testIsEmailUniqueFalse()
    {
        $email = "pepa@seznam.cz";
        $this->userRepository
            ->shouldReceive('emailExists')
            ->with($email)
            ->times(1)
            ->andReturn(true);
        $userFacade = new UserFacade($this->userRepository, $this->transaction, $this->userRoleRepository, $this->passwords);

        Assert::exception(function () use ($email, $userFacade) {
            $userFacade->isEmailUnique($email);
        }, \App\Model\Exceptions\ProcessException::class);

    }



}

# Spuštění testovacích metod
(new IsEmailUniqueTest($container))->run();