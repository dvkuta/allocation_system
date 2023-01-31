<?php

$container = require __DIR__ . '/../../../bootstrap.php';

use Tester\Assert;

class UserFacadeTest extends Tester\TestCase
{
    private Nette\DI\Container $container;
    private $facade;
    public function __construct(\Nette\DI\Container $container)
    {
        $this->container = $container;
        $this->facade = $facade = $this->container->getByType('App\Model\User\UserRepository');
        echo $facade::class;
    }

    public function testOne()
    {
        $actual = $this->facade->getUser(58)->getFirstname();
        $expected = 'Jardaa';
        Assert::same($expected,$actual,"Spatny text alokace");
    }

}

# Spuštění testovacích metod
(new IsEmailUniqueTest($container))->run();