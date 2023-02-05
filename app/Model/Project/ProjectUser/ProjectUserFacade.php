<?php

namespace App\Model\Project\ProjectUser;

use App\Model\Exceptions\ProcessException;
use App\Model\Mapper\Mapper;
use App\Model\Repository\Base\IProjectUserRepository;
use App\Model\Repository\Domain\ProjectUser;
use App\Tools\ITransaction;
use Nette\Database\Table\Selection;
use Tracy\Debugger;
use Tracy\ILogger;

/**
 * Akce týkající se přiřazení pracovníka do projektu
 */
class ProjectUserFacade
{

    private IProjectUserRepository $projectUserRepository;
    private ITransaction $transaction;

    public function __construct(
        ITransaction          $transaction,
        IProjectUserRepository $projectUserRepository,
    )
    {

        $this->transaction = $transaction;
        $this->projectUserRepository = $projectUserRepository;
    }

    /**
     * Uloží uživatele do existujícího projektu.
     * @param int $userId
     * @param int|null $projectId
     * @throws ProcessException Projekt neexistuje, nebo chyba databáze
     */
    public function saveUserToProject(int $userId, ?int $projectId): void
    {

        $projectUser = new ProjectUser($userId, $projectId);
        $projectId = $projectUser->getProjectId();

        if($projectId === null)
        {
            throw new ProcessException('app.baseForm.saveError');
        }

        try {
            $this->transaction->begin();
            $userProjectId = $this->projectUserRepository->isUserOnProject($projectUser->getUserId(), $projectId);
            if($userProjectId > 0)
            {
                throw new ProcessException('app.baseForm.saveError');
            }

            $this->projectUserRepository->saveUserToProject($projectUser);
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

    /**
     * Vrati informace o vsech pracovnikach na projektu
     * @param int $projectId
     * @return array
     */
    public function getAllUsersInfoOnProject(int $projectId): array
    {
        return $this->projectUserRepository->getAllUsersInfoOnProject($projectId);

    }

    /**
     * Overi, jestli uzivatel pracuje na projektu
     * @param int $userId
     * @param int $projectId
     * @return int pokud ano, tak vrati id jeho clenstvi, pokud ne, vraci -1
     */
    public function isUserOnProject(int $userId, int $projectId): int
    {
        return $this->projectUserRepository->isUserOnProject($userId, $projectId);
    }

    /**
     * Vrati selekci pro grid, kde najde vsechny uzivatele(pracovniky) na projektu
     * @param int $projectId
     * @return Selection
     */
    public function getAllUsersOnProjectGridSelection(int $projectId): Selection
    {
        return $this->projectUserRepository->getAllUsersOnProjectGridSelection($projectId);
    }

    /**
     * Vrati selekci pro grid, ktera ukazuje vsechny projekty uzivatele
     * @param int $userId
     * @return Selection
     */
    public function getAllUserProjectGridSelection(int $userId): Selection
    {
        return $this->projectUserRepository->getAllUserProjectGridSelection($userId);
    }

    /**
     * Vrati pole uzivatelu v roli pracovnik, kteri momentalne nepracuji na danem projektu
     * @param int $projectId
     * @return array pole ve tvaru [id => cele_jmeno]
     */
    public function getAllUsersThatDoesNotWorkOnProject(int $projectId): array
    {
        return $this->projectUserRepository->getAllUsersThatDoesNotWorkOnProject($projectId);

    }

}
