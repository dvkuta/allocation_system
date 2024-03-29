<?php

$container = require __DIR__ . '/../../../../bootstrap.php';

use App\Model\Project\ProjectUserAllocation\EState;
use App\Model\Project\ProjectUserAllocation\ProjectUserAllocationFacade;
use App\Model\Repository\Domain\Allocation;
use App\Model\Repository\Domain\Project;
use Tester\Assert;

class CreateAllocationTest extends Tester\TestCase
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

    public function testValid()
    {

        $date1 = new DateTime();
        $date2 = new DateTime();
        $date1->setDate(2023,1,30);
        $date2->setDate(2023,1,31);

        $projectMemberships = [5=>5, 4=>4];
        $allocationNumber = 5;
        $description = "popis";
        $state = EState::from('active');
        $allocation = new Allocation(
            3,5,
            $allocationNumber,
            $date1,
            $date2,
            $description,
            $state);
        $currentWorkerId = 5;
        $currentProjectId = 5;
        $allocation->setCurrentProjectId($currentProjectId);
        $allocation->setCurrentWorkerId($currentWorkerId);
        $currentWorkload = 20;

        $this->transaction
            ->shouldReceive('begin')
            ->times(1)
            ->andReturn();

        $this->transaction
            ->shouldReceive('commit')
            ->times(1)
            ->andReturn();

        //validate worker Id
        $projectUserId = 21;
        $this->projectUserRepository
            ->shouldReceive('isUserOnProject')
            ->with($allocation->getCurrentWorkerId(), $allocation->getCurrentProjectId())
            ->times(1)
            ->andReturn($projectUserId);

        $project = new Project(5, 'Projekt one',
            3,'', $date1,
            $date2, 'popis nejaky');

        //validate allocationTime
        $this->projectRepository
            ->shouldReceive('getProject')
            ->times(1)
            ->andReturn($project);

        //validate allocation possibility
        $this->projectUserRepository
            ->shouldReceive('getAllProjectMembershipIds')
            ->with(5)
            ->times(1)
            ->andReturn($projectMemberships);

        $this->allocationRepository
            ->shouldReceive('getCurrentWorkload')
            ->with($allocation->getFrom(), $allocation->getTo(),$projectMemberships)
            ->times(1)
            ->andReturn($currentWorkload);


        //saveAllocation
        $this->allocationRepository
            ->shouldReceive('saveAllocation')
            ->with(Mockery::any(), $projectUserId)
            ->times(1)
            ->andReturn();

        $allocationFacade = new ProjectUserAllocationFacade($this->projectUserRepository, $this->allocationRepository, $this->projectRepository, $this->superiorUserRepository, $this->transaction);

        Assert::noError(function () use ($currentWorkerId, $currentProjectId, $state, $description, $date1, $date2, $allocationNumber, $allocationFacade) {
            $allocationFacade->createAllocation($allocationNumber, $date1, $date2, $description, $state, $currentProjectId, $currentWorkerId);
        });

    }


}

# Spuštění testovacích metod
(new CreateAllocationTest($container))->run();