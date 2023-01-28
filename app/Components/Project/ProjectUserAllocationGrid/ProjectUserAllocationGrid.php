<?php
namespace App\Components\Project\ProjectUserAllocationGrid;

use App\Components\Base\BaseComponent;
use App\Components\Base\BaseGrid;
use App\Model\Project\ProjectRepository;
use App\Model\Project\ProjectUser\ProjectUserRepository;
use App\Model\Project\ProjectUserAllocation\ProjectUserAllocationRepository;
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

class ProjectUserAllocationGrid extends BaseGrid
{
    private ?int $id;
    private ProjectUserAllocationRepository $allocationRepository;


    public function __construct(
        ?int $id,
        ITranslator           $translator,
        ProjectUserAllocationRepository $allocationRepository,
    )
	{
        parent::__construct($translator);

        $this->id = $id;
        $this->allocationRepository = $allocationRepository;
    }



	public function createComponentGrid(): DataGrid
	{
		$grid = parent::createGrid();


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

        $grid->addAction("edit", 'app.actions.edit', ":editAllocation");


		return $grid;
	}


}


interface IProjectUserAllocationGridFactory {

    public function create(?int $id = null): ProjectUserAllocationGrid;
}

