<?php

namespace App\Model\Project;

use App\Model\DTO\ProjectDTO;
use App\Model\Exceptions\ProcessException;
use App\Model\Repository\Base\IProjectRepository;
use App\Tools\ITransaction;
use Tracy\Debugger;
use Tracy\ILogger;

/**
 * Komplexní akce týkající se projektu
 */
class ProjectFacade
{

    private IProjectRepository $projectRepository;
    private ITransaction $transaction;

    public function __construct(
        IProjectRepository $projectRepository,
        ITransaction      $transaction,
    )
    {

        $this->projectRepository = $projectRepository;
        $this->transaction = $transaction;
    }

    /**
     * Uloží stav projektu do databáze, tj pokud už existuje, tak ho upraví.
     * @throws ProcessException uložení selže, vrací identifikátor pro Translator
     */
    public function saveProject(ProjectDTO $project): void
    {
        try {

            $this->transaction->begin();

            if($project->getId() !== null)
            {
                $storedProject = $this->projectRepository->getProject($project->getId());
                if($storedProject === null)
                {
                    throw new ProcessException('app.project.notExists');
                }
            }

            $this->projectRepository->saveProject($project);
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
