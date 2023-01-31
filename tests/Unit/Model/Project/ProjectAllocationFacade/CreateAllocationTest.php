<?php

$container = require __DIR__ . '/../../../../bootstrap.php';

use App\Model\DTO\AllocationDTO;
use App\Model\DTO\ProjectDTO;
use App\Model\Project\ProjectUser\EState;
use App\Model\Project\ProjectUserAllocation\ProjectUserAllocationFacade;
use Tester\Assert;
//todo
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

        $user_id = 5;
        $projectMemberships = [5=>5, 4=>4];
        $allocation = new AllocationDTO(
            3,5,
            5,
            $date1,
            $date2,
            "popis",
            EState::from('active'));

        $currentWorkload = 20;

        $this->projectUserRepository
            ->shouldReceive('getAllProjectMembershipIds')
            ->with($user_id)
            ->times(1)
            ->andReturn($projectMemberships);

        $this->allocationRepository
            ->shouldReceive('getCurrentWorkload')
            ->with($allocation->getFrom(), $allocation->getTo(),$projectMemberships)
            ->times(1)
            ->andReturn($currentWorkload);

        $allocationFacade = new ProjectUserAllocationFacade($this->projectUserRepository, $this->allocationRepository, $this->projectRepository, $this->superiorUserRepository, $this->transaction);

        Assert::noError(function () use ($allocation, $user_id, $allocationFacade) {
            $allocationFacade->validateAllocationPossibility($allocation, $user_id);
        });

    }

    public function testInValid()
    {

        $date1 = new DateTime();
        $date2 = new DateTime();
        $date1->setDate(2023,1,30);
        $date2->setDate(2023,1,31);

        $user_id = 5;
        $projectMemberships = [5=>5, 4=>4];
        $allocation = new AllocationDTO(
            3,5,
            25,
            $date1,
            $date2,
            "popis",
            EState::from('active'));

        $currentWorkload = 20;

        $this->projectUserRepository
            ->shouldReceive('getAllProjectMembershipIds')
            ->with($user_id)
            ->times(1)
            ->andReturn($projectMemberships);

        $this->allocationRepository
            ->shouldReceive('getCurrentWorkload')
            ->with($allocation->getFrom(), $allocation->getTo(),$projectMemberships)
            ->times(1)
            ->andReturn($currentWorkload);

        $allocationFacade = new ProjectUserAllocationFacade($this->projectUserRepository, $this->allocationRepository, $this->projectRepository, $this->superiorUserRepository, $this->transaction);

        Assert::exception(function () use ($allocation, $user_id, $allocationFacade) {
            $allocationFacade->validateAllocationPossibility($allocation, $user_id);
        }, \App\Model\Exceptions\ProcessException::class);

    }








}

# Spuštění testovacích metod
(new CreateAllocationTest($container))->run();