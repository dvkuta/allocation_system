<?php

$container = require __DIR__ . '/../../../../bootstrap.php';


use App\Model\Repository\Domain\Project;
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
        $id = null;
        $name = 'Projekt one';
        $project_manager_id = 3;
        $project_manager_name = '';
        $from = new DateTime();
        $to = new DateTime();
        $description = 'popis nejaky';
        $project = new Project($id, $name,
            $project_manager_id, $project_manager_name, $from,
            $to, $description);

        $this->transaction
            ->shouldReceive('begin')
            ->times(1)
            ->andReturn();

        $this->transaction
            ->shouldReceive('commit')
            ->times(1)
            ->andReturn();

          $this->projectRepository
            ->shouldReceive('saveProject')
            ->with(Mockery::any())
            ->times(1)
            ->andReturn();

        $projectFacade= new \App\Model\Project\ProjectFacade($this->projectRepository, $this->transaction);

        Assert::noError(function () use ($id, $name, $project_manager_id, $project_manager_name, $from, $to, $description, $projectFacade) {
            $projectFacade->saveProject($id, $name, $project_manager_id, $project_manager_name, $from, $to, $description);
        });

    }


    public function testEditCorrect()
    {
        $id = 5;
        $name = 'Projekt one';
        $project_manager_id = 3;
        $project_manager_name = '';
        $from = new DateTime();
        $to = new DateTime();
        $description = 'popis nejaky';
        $project = new Project($id, $name,
            $project_manager_id, $project_manager_name, $from,
            $to, $description);

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
            ->with(Mockery::any())
            ->times(1)
            ->andReturn();

        $projectFacade = new \App\Model\Project\ProjectFacade($this->projectRepository, $this->transaction);

        Assert::noError(function () use ($id, $name, $project_manager_id, $project_manager_name, $from, $to, $description, $projectFacade) {
            $projectFacade->saveProject($id, $name, $project_manager_id, $project_manager_name, $from, $to, $description);
        });

    }

    public function testEditIncorrect()
    {
        $id = 5;
        $name = 'Projekt one';
        $project_manager_id = 3;
        $project_manager_name = '';
        $from = new DateTime();
        $to = new DateTime();
        $description = 'popis nejaky';
        $project = new Project($id, $name,
            $project_manager_id, $project_manager_name, $from,
            $to, $description);

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

        $projectFacade = new \App\Model\Project\ProjectFacade($this->projectRepository, $this->transaction);

        Assert::exception(function () use ($id, $name, $project_manager_id, $project_manager_name, $from, $to, $description, $projectFacade) {
            $projectFacade->saveProject($id, $name, $project_manager_id, $project_manager_name, $from, $to, $description);
        }, \App\Model\Exceptions\ProcessException::class);

    }



}

# Spuštění testovacích metod
(new SaveProjectTest($container))->run();