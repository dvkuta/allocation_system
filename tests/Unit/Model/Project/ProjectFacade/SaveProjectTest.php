<?php

$container = require __DIR__ . '/../../../../bootstrap.php';

use App\Model\DTO\ProjectDTO;
use App\Model\User\Superior\SuperiorUserFacade;
use App\Model\User\UserFacade;
use Tester\Assert;

class SaveProjectTest extends Tester\TestCase
{
    private Nette\DI\Container $container;

    private $transaction;
    private $projectRepository;

    public function __construct(\Nette\DI\Container $container)
    {
        $this->container = $container;
    }

    public function setUp()
    {
        $this->explorer = Mockery::mock(\Nette\Database\Explorer::class)->makePartial();
        $this->transaction = Mockery::mock(App\Tools\Transaction::class)->makePartial();
        $this->projectRepository = Mockery::mock(App\Model\Project\ProjectRepository::class, array($this->explorer))->makePartial();

    }

    public function tearDown()
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testCreateCorrect()
    {
        $project = new ProjectDTO(null, 'Projekt one',
            3,'', new DateTime(),
            new DateTime(), 'popis nejaky');

        $this->transaction
            ->shouldReceive('begin')
            ->times(1)
            ->andReturn();

        $this->transaction
            ->shouldReceive('commit')
            ->times(1)
            ->andReturn();

//        $this->projectRepository
//            ->shouldReceive('getProject')
//            ->with($project)
//            ->times(1)
//            ->andReturn(false);

          $this->projectRepository
            ->shouldReceive('saveProject')
            ->with($project)
            ->times(1)
            ->andReturn();

        $superiorFacade = new \App\Model\Project\ProjectFacade($this->projectRepository, $this->transaction);

        Assert::noError(function () use ($project, $superiorFacade) {
            $superiorFacade->saveProject($project);
        });

    }


    public function testEditCorrect()
    {
        $project = new ProjectDTO(5, 'Projekt one',
            3,'', new DateTime(),
            new DateTime(), 'popis nejaky');

        $this->transaction
            ->shouldReceive('begin')
            ->times(1)
            ->andReturn();

        $this->transaction
            ->shouldReceive('commit')
            ->times(1)
            ->andReturn();

        $this->projectRepository
            ->shouldReceive('getProject')
            ->with($project->getId())
            ->times(1)
            ->andReturn($project);

        $this->projectRepository
            ->shouldReceive('saveProject')
            ->with($project)
            ->times(1)
            ->andReturn();

        $superiorFacade = new \App\Model\Project\ProjectFacade($this->projectRepository, $this->transaction);

        Assert::noError(function () use ($project, $superiorFacade) {
            $superiorFacade->saveProject($project);
        });

    }

    public function testEditIncorrect()
    {
        $project = new ProjectDTO(5, 'Projekt one',
            3,'', new DateTime(),
            new DateTime(), 'popis nejaky');

        $this->transaction
            ->shouldReceive('begin')
            ->times(1)
            ->andReturn();

        $this->transaction
            ->shouldReceive('rollback')
            ->times(1)
            ->andReturn();

        $this->projectRepository
            ->shouldReceive('getProject')
            ->with($project->getId())
            ->times(1)
            ->andReturn(null);

        $superiorFacade = new \App\Model\Project\ProjectFacade($this->projectRepository, $this->transaction);

        Assert::exception(function () use ($project, $superiorFacade) {
            $superiorFacade->saveProject($project);
        }, \App\Model\Exceptions\ProcessException::class);

    }



}

# Spuštění testovacích metod
(new SaveProjectTest($container))->run();