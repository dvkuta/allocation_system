<?php

$container = require __DIR__ . '/../../../../bootstrap.php';

use App\Model\Domain\Allocation;
use App\Model\Project\ProjectUser\EState;
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

        $allocation = new Allocation(
            10,2,
            1,
            $dateFrom,
            $dateTo,
            "popis",
            EState::from('active'));

        Assert::noError(function () use ($allocation) {
             $this->facade->editAllocation($allocation);
        });
    }




}

# Spuštění testovacích metod
(new ITeditAllocationTest($container))->run();