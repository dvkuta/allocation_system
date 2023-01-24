<?php
namespace App\Components\Project\ProjectGrid;

use App\Components\Base\BaseComponent;
use App\Components\Base\BaseGrid;
use App\Model\Project\ProjectRepository;
use App\Model\User\Role\UserRoleRepository;
use App\Model\User\UserRepository;
use Nette\Database\Explorer;
use Nette\Database\Table\ActiveRow;
use Nette\Localization\ITranslator;
use Nette\Utils\DateTime;
use Ublaboo\DataGrid\AggregationFunction\FunctionSum;
use Ublaboo\DataGrid\Column\Action\Confirmation\StringConfirmation;
use Ublaboo\DataGrid\DataGrid;
use Ublaboo\DataGrid\Row;

class ProjectGrid extends BaseGrid
{


    private ProjectRepository $projectRepository;

    public function __construct(
        ITranslator $translator,
        ProjectRepository $projectRepository
    )
	{
        parent::__construct($translator);

        $this->projectRepository = $projectRepository;
    }



	public function createComponentGrid(): DataGrid
	{
		$grid = parent::createGrid();

		$grid->setDataSource($this->projectRepository->findAll());

		$grid->addColumnText('id', 'app.project.id');

        $grid->addColumnLink('name', 'app.project.name', ':detail')
            ->setSortable()
            ->setFilterText();

        $grid->addColumnText('user_id', 'app.project.user_id')
            ->setRenderer(function( ActiveRow $row) {
            return $row->user->firstname . " " . $row->user->lastname ;
            })
            ->setSortable();

        $grid->addColumnText('status','app.project.worker_count')
            ->setRenderer(function (ActiveRow $row)
            {
               return $row->related('project_user.project_id')->count() ;
            });

        $grid->addColumnDateTime('from', 'app.project.from')
            ->setSortable();

        $grid->addColumnDateTime('to', 'app.project.to')
            ->setRendererOnCondition(
                function( ActiveRow $row)
                    {
                        return "Konec není určen";
                    },
                function (ActiveRow $row)
                    {
                        return $row->to === null;
                    })
            ->setSortable();

        $grid->addColumnText('description', 'app.project.description');


//		$grid->addColumnText('user_role.type', 'app.user.role')
//            ->setRenderer(function( ActiveRow $row) {
//            return $this->translator->translate($row->user_role->type);
//        });

        $grid->addAction("edit", 'app.actions.edit', ":edit");

        $grid->addAction("addUser", 'app.project.addUser', ":addUser");

        $grid->addAction('delete','app.actions.delete')
            ->setConfirmation(
                new StringConfirmation($this->translator->translate('ublaboo_datagrid.delete_record_quote'))
            );;


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


interface IProjectGridFactory {

    public function create(): ProjectGrid;
}

