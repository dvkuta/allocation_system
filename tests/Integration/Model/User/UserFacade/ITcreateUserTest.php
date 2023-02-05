<?php

$container = require __DIR__ . '/../../../../bootstrap.php';

use App\Model\Repository\Domain\User;
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



        $roles = [\App\Model\User\Role\ERole::worker->value => \App\Model\User\Role\ERole::worker->value,
            \App\Model\User\Role\ERole::project_manager->value => \App\Model\User\Role\ERole::project_manager->value];

        Assert::noError(function () use ($roles) {
            $this->facade->createUser('Lada',
                'Horak',
                'lh@t.cz',
                'lada',
                'KIV',
                'heslo',
            $roles);
        });
    }




}

# Spuštění testovacích metod
(new ITcreateUserTest($container))->run();