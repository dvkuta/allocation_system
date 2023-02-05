<?php

$container = require __DIR__ . '/../../../../bootstrap.php';

use Tester\Assert;

class ITsaveTest extends Tester\TestCase
{
    private Nette\DI\Container $container;
    private $facade;
    public function __construct(\Nette\DI\Container $container)
    {
        $this->container = $container;
        $this->facade = $facade = $this->container->getByType('App\Model\User\Superior\SuperiorUserFacade');
        echo $facade::class;
    }

    public function testSuccess()
    {

        $superiorId = 61;
        $workerId = 62;
        Assert::noError(function () use ($superiorId, $workerId) {
            $this->facade->save($superiorId, $workerId);
        });
    }

    public function testSuccess_1()
    {
        $superiorId = 61;
        $workerId = 58;
        Assert::exception(function () use ($superiorId, $workerId) {
            $this->facade->save($superiorId, $workerId);
        }, \App\Model\Exceptions\ProcessException::class);
    }




}

# Spuštění testovacích metod
(new ITsaveTest($container))->run();