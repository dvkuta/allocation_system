<?php
namespace App\Components\Project\ProjectUserGrid;

use App\Components\Base\BaseComponent;
use App\Components\Base\BaseGrid;
use App\Model\Project\ProjectRepository;
use App\Model\Project\ProjectUser\ProjectUserRepository;
use App\Model\User\Role\RoleRepository;
use App\Model\User\UserRepository;
use Nette\Database\Explorer;
use Nette\Database\Table\ActiveRow;
use Nette\Localization\ITranslator;
use Nette\Utils\DateTime;
use Ublaboo\DataGrid\AggregationFunction\FunctionSum;
use Ublaboo\DataGrid\Column\Action\Confirmation\StringConfirmation;
use Ublaboo\DataGrid\DataGrid;
use Ublaboo\DataGrid\Row;

class ProjectUserGrid extends BaseGrid
{
    private ?int $projectId;
    private ?int $userId;

    private ProjectUserRepository $projectUserRepository;


    public function __construct(
        ?int $projectId,
        ?int $userId,
        ITranslator           $translator,
        ProjectUserRepository $projectUserRepository,
    )
	{
        parent::__construct($translator);

        $this->projectUserRepository = $projectUserRepository;
        $this->projectId = $projectId;
        $this->userId = $userId;
    }



	public function createComponentGrid(): DataGrid
	{
		$grid = parent::createGrid();

        if(isset($this->projectId))
        {
            $grid->setDataSource($this->projectUserRepository->getAllUsersOnProject($this->projectId));
        }

        if(isset($this->userId))
        {
            $grid->setDataSource($this->projectUserRepository->getAllUserProjects($this->userId));
        }


		$grid->addColumnText('id', 'app.projectAllocation.id');

        if(isset($this->userId))
        {
            $grid->addColumnText('project_id','app.projectAllocation.name', 'project.name');
        }

        $grid->addColumnText('user_id', 'app.projectAllocation.user_id')
            ->setRenderer(function( ActiveRow $row) {
                return $row->user->firstname . " " . $row->user->lastname ;
            })
            ->setSortable();

        /*$grid->addColumnText('allocation','app.projectAllocation.allocation');

        $grid->addColumnDateTime('from', 'app.projectAllocation.from')
            ->setSortable();

        $grid->addColumnDateTime('to', 'app.projectAllocation.to')
            ->setSortable();

        $grid->addColumnText('description', 'app.projectAllocation.description');
        $grid->addColumnText('state', 'app.projectAllocation.state');*/

//		$grid->addColumnText('user_role.type', 'app.user.role')
//            ->setRenderer(function( ActiveRow $row) {
//            return $this->translator->translate($row->user_role->type);
//        });

//        $grid->addAction("edit", 'app.actions.edit', ":editAllocation");


		return $grid;
	}

    public function handleDelete(int $id)
    {
        //todo
        $this->projectRepository->delete($id);
        if($this->presenter->isAjax()) {
            /** @var BaseGrid $grid */
            $grid = $this["grid"];

            $grid->reload();
        }
        $this->presenter->flashMessage("Smazání proběhlo úspěšně", "bg-success");

    }


}


interface IProjectUserGridFactory {

    public function create(?int $projectId = null, ?int $userId = null): ProjectUserGrid;
}

