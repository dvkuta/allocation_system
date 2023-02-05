<?php

namespace App\Model\Repository\Base;


use App\Model\Repository\Domain\Project;
use Nette\Database\Table\Selection;

/**
 * Přístup k datům z tabulky Project
 */
interface IProjectRepository
{

    public function getTableName(): string;

    public function saveProject(Project $project);

    public function getProject(int $id): ?Project;

    /**
     * Pokud je zadane ID, vrati pouze selekci projektu daneho projekt managera
     * @param int|null $projectManagerId
     * @return Selection
     */
    public function getAllProjects(?int $projectManagerId = null): Selection;

    /**
     * Overi, jestli je user opravdu project manager daneho projektu
     * @param int $userId
     * @param int $projectId
     * @return bool
     */
    public function isUserManagerOfProject(int $userId, int $projectId): bool;


}