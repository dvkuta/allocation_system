<?php

namespace App\Model\Project\ProjectUser;

use App\Model\Exceptions\ProcessException;
use App\Tools\Transaction;
use Nette\Utils\ArrayHash;
use Tracy\Debugger;
use Tracy\ILogger;

class ProjectUserFacade
{


    private ProjectUserRepository $projectUserRepository;
    private Transaction $transaction;

    public function __construct(
        Transaction           $transaction,
        ProjectUserRepository $projectUserRepository,
    )
    {

        $this->transaction = $transaction;
        $this->projectUserRepository = $projectUserRepository;
    }

    /**
     * @throws ProcessException
     */
    public function saveUserToProject(ArrayHash $formValues, ?int $projectId): void
    {
        if($projectId === null)
        {
            throw new ProcessException('app.baseForm.saveError');
        }

        try {
            $this->transaction->begin();
            //todo metoda exists in project
            $this->projectUserRepository->saveUserToProject($formValues, $projectId);
            $this->transaction->commit();
        }
        catch (\PDOException $e)
        {
            $this->transaction->rollback();
            Debugger::log($e, ILogger::EXCEPTION);
            throw new ProcessException('app.baseForm.saveError');

        }

    }

}
