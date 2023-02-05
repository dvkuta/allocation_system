<?php

namespace App\Model\Project;

use App\Model\Exceptions\ProcessException;
use App\Model\Mapper\Mapper;
use App\Model\Repository\Base\IProjectRepository;
use App\Model\Repository\Domain\Project;
use App\Tools\ITransaction;
use DateTime;
use Nette\Database\Table\Selection;
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
    public function saveProject(?int      $id,
                                string    $name,
                                int       $project_manager_id,
                                string    $project_manager_name,
                                DateTime  $from,
                                ?DateTime $to,
                                string $description ): void
    {
        $project = new Project($id, $name, $project_manager_id, $project_manager_name, $from, $to, $description);
        try {

            $this->transaction->begin();

            if ($project->getId() !== null) {
                $storedProject = $this->projectRepository->getProject($project->getId());
                if ($storedProject === null) {
                    throw new ProcessException('app.project.notExists');
                }
            }

            $this->projectRepository->saveProject($project);
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
        return $this->projectRepository->getProject($id);
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
