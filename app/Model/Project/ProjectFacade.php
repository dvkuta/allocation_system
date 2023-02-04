<?php

namespace App\Model\Project;

use App\Model\Domain\Project;
use App\Model\DTO\ProjectDTO;
use App\Model\Exceptions\ProcessException;
use App\Model\Repository\Base\IProjectRepository;
use App\Tools\ITransaction;
use Nette\Database\Table\Selection;
use Nette\Security\User;
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
        ITransaction       $transaction,
    )
    {

        $this->projectRepository = $projectRepository;
        $this->transaction = $transaction;
    }

    /**
     * Uloží stav projektu do databáze, tj pokud už existuje, tak ho upraví.
     * @throws ProcessException uložení selže, vrací identifikátor pro Translator
     */
    public function saveProject(Project $project): void
    {
        try {

            $this->transaction->begin();

            if ($project->getId() !== null) {
                $storedProject = $this->projectRepository->getProject($project->getId());
                if ($storedProject === null) {
                    throw new ProcessException('app.project.notExists');
                }
            }

            $this->projectRepository->saveProject($project->toDTO());
            $this->transaction->commit();
        } catch (ProcessException $e) {
            $this->transaction->rollback();
            throw $e;
        } catch (\PDOException $e) {
            $this->transaction->rollback();
            Debugger::log($e, ILogger::EXCEPTION);
            throw new ProcessException('app.baseForm.saveError');

        }
    }


    public function getProject(int $id): ?Project
    {
        $projectDto = $this->projectRepository->getProject($id);

        if ($projectDto) {
            return Project::createProject($projectDto);
        } else {
            return null;
        }
    }

    /**
     * Pokud je zadane ID, vrati pouze selekci projektu daneho projekt managera
     * @param int|null $projectManagerId
     * @return Selection
     */
    public function getAllProjects(?int $projectManagerId = null): Selection
    {
        return $this->projectRepository->getAllProjects($projectManagerId);
    }

    /**
     * Overi, jestli je user opravdu project manager daneho projektu
     * @param int $userId
     * @param int $projectId
     * @return bool
     */
    public function isUserManagerOfProject(int $userId, int $projectId): bool
    {
        return $this->projectRepository->isUserManagerOfProject($userId, $projectId);
    }


}
