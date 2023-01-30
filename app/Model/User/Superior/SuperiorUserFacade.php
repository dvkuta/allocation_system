<?php

namespace App\Model\User\Superior;


use App\Model\DTO\UserDTO;
use App\Model\Exceptions\ProcessException;
use App\Model\Repository\Base\BaseRepository;

use App\Tools\ITransaction;
use Nette\Database\Explorer;
use Nette\Database\Table\ActiveRow;
use Nette\Utils\ArrayHash;
use Tracy\Debugger;
use Tracy\ILogger;

/**
 * Komplexni akce nadrizeneho
 */
class SuperiorUserFacade
{

    private SuperiorUserRepository $superiorUserRepository;
    private ITransaction $transaction;


    public function __construct(
        SuperiorUserRepository $superiorUserRepository,
        ITransaction $transaction,
    )
    {
        $this->superiorUserRepository = $superiorUserRepository;
        $this->transaction = $transaction;
    }

    /**
     * Ulozi nadrizeni
     * Nadrizeny muze byt nadrizeny pouze uzivatelum s roli worker
     * (muze dostavat alokace) a zaroven nesmi byt zaroven nadrizeny a worker
     * @throws ProcessException pri chybe
     */
    public function save(int $superior_id, int $worker_id): void
    {
        try
        {
         $this->transaction->begin();
         $isSuperior = $this->superiorUserRepository->isSuperiorOfWorker($superior_id, $worker_id);

         if($isSuperior)
         {
             throw new ProcessException("app.subordinate.error");
         }

         $this->superiorUserRepository->saveData($superior_id, $worker_id);
         $this->transaction->commit();
        }
        catch (ProcessException $e)
        {
            $this->transaction->rollback();
            throw $e;
        }
        catch (\PDOException $e)
        {
            $this->transaction->rollback();
            Debugger::log($e,ILogger::EXCEPTION);
            throw new ProcessException('app.baseForm.saveError');
        }
    }


}