<?php

$container = require __DIR__ . '/../../../../bootstrap.php';


use App\Model\Domain\ProjectUser;
use Tester\Assert;

class ITsaveUserToProjectTest extends Tester\TestCase
{
    private Nette\DI\Container $container;
    private $facade;
    public function __construct(\Nette\DI\Container $container)
    {
        $this->container = $container;
        $this->facade = $facade = $this->container->getByType('App\Model\Project\ProjectUser\ProjectUserFacade');
        echo $facade::class;
    }


    public function testSave()
    {

        $projectUser = new ProjectUser(62, 6);

        Assert::noError(function () use ($projectUser) {
            $this->facade->saveUserToProject($projectUser);
        });
    }




}

# Spuštění testovacích metod
(new ITsaveuserToProjectTest($container))->run();