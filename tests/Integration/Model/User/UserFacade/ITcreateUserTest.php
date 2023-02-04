<?php

$container = require __DIR__ . '/../../../../bootstrap.php';

use App\Model\Domain\User;
use Tester\Assert;

class ITcreateUserTest extends Tester\TestCase
{
    private Nette\DI\Container $container;
    private $facade;
    public function __construct(\Nette\DI\Container $container)
    {
        $this->container = $container;
        $this->facade = $facade = $this->container->getByType('App\Model\User\UserFacade');
        echo $facade::class;
    }

    public function testSuccess()
    {

        $user = new User(null,
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

        Assert::noError(function () use ($user) {
            $this->facade->createUser($user);
        });
    }




}

# Spuštění testovacích metod
(new ITcreateUserTest($container))->run();