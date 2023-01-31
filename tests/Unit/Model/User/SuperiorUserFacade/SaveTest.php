<?php

$container = require __DIR__ . '/../../../../bootstrap.php';

use App\Model\User\Superior\SuperiorUserFacade;
use App\Model\User\UserFacade;
use Tester\Assert;

class SaveTest extends Tester\TestCase
{
    private Nette\DI\Container $container;

    private $transaction;
    private $superiorUserRepository;

    public function __construct(\Nette\DI\Container $container)
    {
        $this->container = $container;
    }

    public function setUp()
    {
        $this->explorer = Mockery::mock(\Nette\Database\Explorer::class)->makePartial();
        $this->transaction = Mockery::mock(App\Tools\Transaction::class)->makePartial();
        $this->superiorUserRepository = Mockery::mock(App\Model\User\Superior\SuperiorUserRepository::class, array($this->explorer))->makePartial();

    }

    public function tearDown()
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testCorrect()
    {
        $superiorId = 5;
        $workerId = 4;

        $this->transaction
            ->shouldReceive('begin')
            ->times(1)
            ->andReturn();

        $this->transaction
            ->shouldReceive('commit')
            ->times(1)
            ->andReturn();

        $this->superiorUserRepository
            ->shouldReceive('isSuperiorOfWorker')
            ->with($superiorId, $workerId)
            ->times(1)
            ->andReturn(false);

        $this->superiorUserRepository
            ->shouldReceive('saveData')
            ->with($superiorId, $workerId)
            ->times(1)
            ->andReturn();

        $superiorFacade = new SuperiorUserFacade($this->superiorUserRepository, $this->transaction);

        Assert::noError(function () use ($workerId, $superiorId, $superiorFacade) {
            $superiorFacade->save($superiorId, $workerId);
        });

    }

    public function testFailure()
    {
        $superiorId = 5;
        $workerId = 4;

        $this->transaction
            ->shouldReceive('begin')
            ->times(1)
            ->andReturn();

        $this->transaction
            ->shouldReceive('rollback')
            ->times(1)
            ->andReturn();

        $this->superiorUserRepository
            ->shouldReceive('isSuperiorOfWorker')
            ->with($superiorId, $workerId)
            ->times(1)
            ->andReturn(true);


        $superiorFacade = new SuperiorUserFacade($this->superiorUserRepository, $this->transaction);

        Assert::exception(function () use ($workerId, $superiorId, $superiorFacade) {
            $superiorFacade->save($superiorId, $workerId);
        }, \App\Model\Exceptions\ProcessException::class);

    }





}

# Spuštění testovacích metod
(new SaveTest($container))->run();