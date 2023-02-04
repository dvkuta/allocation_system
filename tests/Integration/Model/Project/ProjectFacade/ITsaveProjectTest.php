<?php

$container = require __DIR__ . '/../../../../bootstrap.php';

use App\Model\Domain\Project;

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

        $project = new Project(2, 'Projekt one',
            61,'', $dateFrom,
            $dateTo, 'popis nejaky');

        Assert::noError(function () use ($project) {
            $this->facade->saveProject($project);
        });
    }

    public function testCreate()
    {

        $project = new Project(null, 'Projekt oneZ',
            61,'', new DateTime(),
            new DateTime(), 'popis nejaky');

        Assert::noError(function () use ($project) {
            $this->facade->saveProject($project);
        });
    }




}

# Spuštění testovacích metod
(new ITsaveProjectTest($container))->run();