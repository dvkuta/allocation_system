<?php

namespace App\Model\Repository\Base;


use App\Model\DTO\ProjectDTO;
use Nette\Database\Table\Selection;

/**
 * Přístup k datům z tabulky Project
 */
interface IProjectRepository
{

    public function getTableName(): string;

    public function saveProject(ProjectDTO $project);

    public function getProject(int $id): ?ProjectDTO;

    /**
     * Pokud je zadane ID, vrati pouze selekci projektu daneho projekt managera
     * @param int|null $projectManagerId
     * @return Selection
     */
    public function getAllProjects(?int $projectManagerId = null): Selection;


}