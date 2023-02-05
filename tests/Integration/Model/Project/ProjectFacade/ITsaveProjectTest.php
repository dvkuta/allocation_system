<?php

$container = require __DIR__ . '/../../../../bootstrap.php';

use App\Model\Repository\Domain\Project;
use Tester\Assert;

class ITsaveProjectTest extends Tester\TestCase
{
    private Nette\DI\Container $container;
    private $facade;
    public function __construct(\Nette\DI\Container $container)
    {
        $this->container = $container;
        $this->facade = $facade = $this->container->getByType('App\Model\Project\ProjectFacade');
        echo $facade::class;
    }

    public function testEdit()
    {

        $dateFrom = new DateTime();
        $dateTo = new DateTime();

        $dateFrom = $dateFrom->setDate(2023,3,1);
        $dateTo = $dateTo->setDate(2023,3,31);


        Assert::noError(function () use ($dateTo, $dateFrom) {
            $this->facade->saveProject(2, 'Projekt one',
                61,'', $dateFrom,
                $dateTo, 'popis nejaky');
        });
    }

    public function testCreate()
    {
        Assert::noError(function () {
            $this->facade->saveProject(null, 'Projekt oneZ',
                61,'', new DateTime(),
                new DateTime(), 'popis nejaky');
        });
    }




}

# Spuštění testovacích metod
(new ITsaveProjectTest($container))->run();