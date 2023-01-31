<?php

$container = require __DIR__ . '/../../../../bootstrap.php';

use App\Model\DTO\ProjectDTO;
use App\Model\DTO\ProjectUserDTO;
use App\Model\User\Superior\SuperiorUserFacade;
use App\Model\User\UserFacade;
use Tester\Assert;

class SaveUserToProjectTest extends Tester\TestCase
{
    private Nette\DI\Container $container;

    private $transaction;
    private $projectUserRepository;

    public function __construct(\Nette\DI\Container $container)
    {
        $this->container = $container;
    }

    public function setUp()
    {
        $this->explorer = Mockery::mock(\Nette\Database\Explorer::class)->makePartial();
        $this->transaction = Mockery::mock(App\Tools\Transaction::class)->makePartial();
        $this->projectUserRepository = Mockery::mock(App\Model\Project\ProjectUser\ProjectUserRepository::class, array($this->explorer))->makePartial();

    }

    public function tearDown()
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testSaveCorrect()
    {

        $projectUser = new ProjectUserDTO(5, 3);

        $this->transaction
            ->shouldReceive('begin')
            ->times(1)
            ->andReturn();

        $this->transaction
            ->shouldReceive('commit')
            ->times(1)
            ->andReturn();

        $this->projectUserRepository
            ->shouldReceive('isUserOnProject')
            ->with($projectUser->getUserId(), $projectUser->getProjectId())
            ->times(1)
            ->andReturn(-1);

        $this->projectUserRepository
            ->shouldReceive('saveUserToProject')
            ->with($projectUser)
            ->times(1)
            ->andReturn();

        $projectUserFacade = new \App\Model\Project\ProjectUser\ProjectUserFacade( $this->transaction, $this->projectUserRepository);

        Assert::noError(function () use ($projectUser, $projectUserFacade) {
            $projectUserFacade->saveUserToProject($projectUser);
        });

    }

    public function testSaveFailure()
    {

        $projectUser = new ProjectUserDTO(5, null);


        $projectUserFacade = new \App\Model\Project\ProjectUser\ProjectUserFacade( $this->transaction, $this->projectUserRepository);

        Assert::exception(function () use ($projectUser, $projectUserFacade) {
            $projectUserFacade->saveUserToProject($projectUser);
        },\App\Model\Exceptions\ProcessException::class);

    }


    public function testSaveFailure_2()
    {

        $projectUser = new ProjectUserDTO(5, 3);

        $this->transaction
            ->shouldReceive('begin')
            ->times(1)
            ->andReturn();

        $this->transaction
            ->shouldReceive('rollback')
            ->times(1)
            ->andReturn();

        $this->projectUserRepository
            ->shouldReceive('isUserOnProject')
            ->with($projectUser->getUserId(), $projectUser->getProjectId())
            ->times(1)
            ->andReturn(5);

        $projectUserFacade = new \App\Model\Project\ProjectUser\ProjectUserFacade( $this->transaction, $this->projectUserRepository);

        Assert::exception(function () use ($projectUser, $projectUserFacade) {
            $projectUserFacade->saveUserToProject($projectUser);
        }, \App\Model\Exceptions\ProcessException::class);

    }



}

# Spuštění testovacích metod
(new SaveUserToProjectTest($container))->run();