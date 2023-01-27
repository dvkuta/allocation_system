<?php

namespace App\Model\Project;

use App\Model\Exceptions\ProcessException;
use App\Model\User\Role\RoleRepository;
use App\Tools\Transaction;
use Nette\Security\Passwords;
use Nette\Utils\ArrayHash;
use Tracy\Debugger;
use Tracy\ILogger;

class ProjectFacade
{


    private ProjectRepository $projectRepository;
    private Transaction $transaction;

    public function __construct(
        ProjectRepository $projectRepository,
        Transaction           $transaction,
    )
    {

        $this->projectRepository = $projectRepository;
        $this->transaction = $transaction;
    }

    /**
     * @throws ProcessException
     */
    public function saveProject(ArrayHash $project, ?int $projectId): void
    {
        try {
            bdump($project);
            $this->transaction->begin();
            $this->projectRepository->saveProject($project, $projectId);
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
