<?php

namespace App\Model\Project;


use App\Model\Repository\Base\BaseRepository;

use Nette\Database\Explorer;
use Nette\Database\Table\ActiveRow;
use Nette\Utils\ArrayHash;


class ProjectRepository extends BaseRepository
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

    public function saveProject(ArrayHash $project, ?int $projectId)
    {
        $data = [
            self::COL_NAME => $project->name,
            self::COL_USER_ID => $project->user_id,
            self::COL_FROM => $project->from,
            self::COL_TO => $project->to,
            self::COL_DESCRIPTION => $project->description
        ];

        $this->saveFiltered($data, $projectId);
    }

    public function getProject(int $id): array
    {
        $project = $this->findRow($id);

        if($project)
        {
            return $project->toArray();
        }
        else
        {
            return [];
        }

    }


}