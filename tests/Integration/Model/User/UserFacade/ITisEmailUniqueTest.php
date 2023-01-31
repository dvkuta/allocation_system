<?php

$container = require __DIR__ . '/../../../../bootstrap.php';

use Tester\Assert;

class ITisEmailUniqueTest extends Tester\TestCase
{
    private Nette\DI\Container $container;
    private $facade;
    public function __construct(\Nette\DI\Container $container)
    {
        $this->container = $container;
        $this->facade = $facade = $this->container->getByType('App\Model\User\UserFacade');
        echo $facade::class;
    }

    public function testIsLoginUnique()
    {

        Assert::noError(function ()
        {
            $this->facade->isEmailUnique("bb@ss.cz");
        });
    }

    public function testIsLoginUnique_failure()
    {

        Assert::exception(function ()
        {
            $this->facade->isEmailUnique("aa@ss.cz");
        }, \App\Model\Exceptions\ProcessException::class);
    }



}

# Spuštění testovacích metod
(new ITisEmailUniqueTest($container))->run();