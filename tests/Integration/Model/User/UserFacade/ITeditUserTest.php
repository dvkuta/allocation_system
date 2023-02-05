<?php

$container = require __DIR__ . '/../../../../bootstrap.php';

use App\Model\Repository\Domain\User;
use Tester\Assert;

class ITeditUserTest extends Tester\TestCase
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

        $user = new User(58,
            'Jarda',
            'Blatníček',
            'aa@ss.cz',
            'jarda',
            'KIV',
            '1234'
        );

        $roles = [\App\Model\User\Role\ERole::worker->value => \App\Model\User\Role\ERole::worker->value,
            \App\Model\User\Role\ERole::project_manager->value => \App\Model\User\Role\ERole::project_manager->value];
        $user->setRoles($roles);

        Assert::noError(function () use ($user) {
            $this->facade->editUser($user);
        });
    }



}

# Spuštění testovacích metod
(new ITeditUserTest($container))->run();