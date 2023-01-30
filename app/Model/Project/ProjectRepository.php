<?php

namespace App\Model\Project;


use App\Model\DTO\ProjectDTO;
use App\Model\Repository\Base\BaseRepository;

use App\Model\Repository\Base\IProjectRepository;
use Nette\Database\Explorer;
use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\Selection;
use Nette\Utils\ArrayHash;

/**
 * Přístup k datům z tabulky Project
 */
class ProjectRepository extends BaseRepository implements IProjectRepository
{

    public const TABLE_NAME = 'project';

    public const COL_ID = 'id';
    public const COL_NAME = 'name';
    public const COL_USER_ID = 'user_id';
    public const COL_FROM = 'from';
    public const COL_TO ='to';
    public const COL_DESCRIPTION = 'description';

    protected $tableName = self::TABLE_NAME;


    public function __construct(
        Explorer $explorer,
    )
    {
        parent::__construct($explorer);

    }

    public function saveProject(ProjectDTO $project)
    {
        $data = [
            self::COL_NAME => $project->getName(),
            self::COL_USER_ID => $project->getProjectManagerId(),
            self::COL_FROM => $project->getFrom(),
            self::COL_TO => $project->getTo(),
            self::COL_DESCRIPTION => $project->getDescription()
        ];

        $this->saveFiltered($data, $project->getId());
    }

    public function getProject(int $id): ?ProjectDTO
    {
        $project = $this->findRow($id);

        if($project)
        {
            return new ProjectDTO($project->id, $project->name, $project->user_id,
                $project->user->firstname . " " . $project->user->lastname,
                $project->from, $project->to, $project->description);
        }
        else
        {
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
        if(isset($projectManagerId))
        {
            $by = [self::COL_USER_ID => $projectManagerId];
            return $this->findBy($by);
        }

        return $this->findAll();
    }


}