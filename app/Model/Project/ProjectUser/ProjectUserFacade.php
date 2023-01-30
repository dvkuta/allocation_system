<?php

namespace App\Model\Project\ProjectUser;

use App\Model\DTO\ProjectUserDTO;
use App\Model\Exceptions\ProcessException;
use App\Model\Project\ProjectRepository;
use App\Tools\ITransaction;
use Tracy\Debugger;
use Tracy\ILogger;

/**
 * Akce týkající se přiřazení pracovníka do projektu
 */
class ProjectUserFacade
{

    private ProjectUserRepository $projectUserRepository;
    private ITransaction $transaction;
    private ProjectRepository $projectRepository;

    public function __construct(
        ITransaction           $transaction,
        ProjectUserRepository $projectUserRepository,
        ProjectRepository $projectRepository,
    )
    {

        $this->transaction = $transaction;
        $this->projectUserRepository = $projectUserRepository;
        $this->projectRepository = $projectRepository;
    }

    /**
     * Uloží uživatele do existujícího projektu.
     * @throws ProcessException Projekt neexistuje, nebo chyba databáze
     */
    public function saveUserToProject(ProjectUserDTO $projectUserDTO): void
    {
        $projectId = $projectUserDTO->getProjectId();

        if($projectId === null)
        {
            throw new ProcessException('app.baseForm.saveError');
        }

        try {
            $this->transaction->begin();
            $userProjectId = $this->projectUserRepository->isUserOnProject($projectUserDTO->getUserId(), $projectId);
            if($userProjectId > 0)
            {
                throw new ProcessException('app.baseForm.saveError');
            }

            $this->projectUserRepository->saveUserToProject($projectUserDTO);
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
            Debugger::log($e, ILogger::EXCEPTION);
            throw new ProcessException('app.baseForm.saveError');

        }

    }

}
