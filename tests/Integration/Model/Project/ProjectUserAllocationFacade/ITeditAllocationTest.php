<?php

$container = require __DIR__ . '/../../../../bootstrap.php';

use App\Model\Project\ProjectUserAllocation\EState;
use App\Model\Repository\Domain\Allocation;
use Tester\Assert;

class ITeditAllocationTest extends Tester\TestCase
{
    private Nette\DI\Container $container;
    private $facade;
    public function __construct(\Nette\DI\Container $container)
    {
        $this->container = $container;
        $this->facade = $facade = $this->container->getByType('App\Model\Project\ProjectUserAllocation\ProjectUserAllocationFacade');
        echo $facade::class;
    }

    public function testSuccess()
    {

        $dateFrom = new DateTime();
        $dateTo = new DateTime();

        $dateFrom = $dateFrom->setDate(2023,3,1);
        $dateTo = $dateTo->setDate(2023,3,31);


        Assert::noError(function () use ($dateTo, $dateFrom) {
             $this->facade->editAllocation(10,
                 1,
                 $dateFrom,
                 $dateTo,
                 "popis",
                 EState::from('active'));
        });
    }




}

# Spuštění testovacích metod
(new ITeditAllocationTest($container))->run();