<?php

$container = require __DIR__ . '/../../../../bootstrap.php';


use App\Model\Project\ProjectUserAllocation\EState;
use App\Model\Project\ProjectUserAllocation\ProjectUserAllocationFacade;
use App\Model\Repository\Domain\Allocation;
use App\Model\Repository\Domain\Project;
use Tester\Assert;

class ValidateAllocationTimeTest extends Tester\TestCase
{
    private Nette\DI\Container $container;

    private $transaction;
    private $projectUserRepository;
    private $allocationRepository;
    private $projectRepository;
    private $superiorUserRepository;
    private $explorer;

    public function __construct(\Nette\DI\Container $container)
    {
        $this->container = $container;
    }

    public function setUp()
    {
        parent::setUp();

        $this->explorer = Mockery::mock(\Nette\Database\Explorer::class)->makePartial();
        $this->transaction = Mockery::mock(App\Tools\Transaction::class)->makePartial();
        $this->superiorUserRepository = Mockery::mock(\App\Model\User\Superior\SuperiorUserRepository::class, array($this->explorer))->makePartial();
        $this->projectUserRepository = Mockery::mock(\App\Model\Project\ProjectUser\ProjectUserRepository::class, array($this->explorer))->makePartial();
        $this->allocationRepository = Mockery::mock(\App\Model\Project\ProjectUserAllocation\ProjectUserAllocationRepository::class, array($this->explorer))->makePartial();
        $this->projectRepository = Mockery::mock(\App\Model\Project\ProjectRepository::class, array($this->explorer))->makePartial();
    }

    public function tearDown()
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testValidDate()
    {


        $date1 = new DateTime();
        $date2 = new DateTime();
        $date1->setDate(2023,1,30);
        $date2->setDate(2023,1,31);

        $project = new Project(5, 'Projekt one',
            3,'', $date1,
            $date2, 'popis nejaky');
        $allocation = new Allocation(
            3,5,
            4,
            $date1,
            $date2,
            "popis",
            EState::from('active'));

        $this->projectRepository
            ->shouldReceive('getProject')
            ->times(1)
            ->andReturn($project);

        $allocationFacade = new ProjectUserAllocationFacade($this->projectUserRepository, $this->allocationRepository, $this->projectRepository, $this->superiorUserRepository, $this->transaction);

        Assert::noError(function () use ($project, $allocation, $allocationFacade) {
            $allocationFacade->validateAllocationTime($allocation, $project->getId());
        });

    }

    public function testInvalidDate()
    {


        $date1 = new DateTime();
        $date2 = new DateTime();
        $date1->setDate(2023,1,30);
        $date2->setDate(2023,1,31);

        $date3 = new DateTime();
        $date4 = new DateTime();
        $date3->setDate(2023,1,30);
        $date4->setDate(2023,1,31);

        $project = new Project(5, 'Projekt one',
            3,'', $date3,
            $date4, 'popis nejaky');
        $allocation = new Allocation(
            3,5,
            4,
            $date1,
            $date2,
            "popis",
            EState::from('active'));

        $this->projectRepository
            ->shouldReceive('getProject')
            ->times(1)
            ->andReturn(null);

        $allocationFacade = new ProjectUserAllocationFacade($this->projectUserRepository, $this->allocationRepository, $this->projectRepository, $this->superiorUserRepository, $this->transaction);

        Assert::exception(function () use ($project, $allocation, $allocationFacade) {
            $allocationFacade->validateAllocationTime($allocation, $project->getId());
        }, \App\Model\Exceptions\ProcessException::class);

    }

    public function testInvalidDate_1()
    {

        $date1 = new DateTime();
        $date2 = new DateTime();
        $date1->setDate(2023,1,25);
        $date2->setDate(2023,1,31);

        $date3 = new DateTime();
        $date4 = new DateTime();
        $date3->setDate(2023,1,30);
        $date4->setDate(2023,1,31);

        $project = new Project(5, 'Projekt one',
            3,'', $date3,
            $date4, 'popis nejaky');
        $allocation = new Allocation(
            3,5,
            4,
            $date1,
            $date2,
            "popis",
            EState::from('active'));

        $this->projectRepository
            ->shouldReceive('getProject')
            ->times(1)
            ->andReturn($project);

        $allocationFacade = new ProjectUserAllocationFacade($this->projectUserRepository, $this->allocationRepository, $this->projectRepository, $this->superiorUserRepository, $this->transaction);

        Assert::exception(function () use ($project, $allocation, $allocationFacade) {
            $allocationFacade->validateAllocationTime($allocation, $project->getId());
        }, \App\Model\Exceptions\ProcessException::class);

    }







}

# Spuštění testovacích metod
(new ValidateAllocationTimeTest($container))->run();