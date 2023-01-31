<?php

$container = require __DIR__ . '/../../../../bootstrap.php';

use App\Model\DTO\AllocationDTO;
use App\Model\DTO\ProjectDTO;
use App\Model\Project\ProjectUser\EState;
use App\Model\Project\ProjectUserAllocation\ProjectUserAllocationFacade;
use Tester\Assert;

class ValidateUserMembershipOnProjectTest extends Tester\TestCase
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


    public function testInValid()
    {

        $user_id = 5;
        $project_id = 3;

        $this->projectUserRepository
            ->shouldReceive('isUserOnProject')
            ->with($user_id, $project_id)
            ->times(1)
            ->andReturn(-1);


        $allocationFacade = new ProjectUserAllocationFacade($this->projectUserRepository, $this->allocationRepository, $this->projectRepository, $this->superiorUserRepository, $this->transaction);

        Assert::exception(function () use ($project_id, $user_id, $allocationFacade) {
            $allocationFacade->validateUserMembershipOnProject( $user_id, $project_id);
        }, \App\Model\Exceptions\ProcessException::class);

    }










}

# Spuštění testovacích metod
(new ValidateUserMembershipOnProjectTest($container))->run();