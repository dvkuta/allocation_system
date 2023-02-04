<?php
namespace App\Components\Project\ProjectGrid;

use App\Components\Base\BaseGrid;
use App\Model\Project\ProjectFacade;
use App\Model\Repository\Base\IProjectRepository;
use App\Model\User\Role\ERole;
use Nette\Database\Table\ActiveRow;
use Nette\Localization\ITranslator;
use Nette\Security\User;
use Ublaboo\DataGrid\DataGrid;

/**
 * Komponenta pro vytvoření tabulky pro projekty
 * Obsahuje odkazy na akce editace a přiřazení pracovníka do projektu
 */
class ProjectGrid extends BaseGrid
{
    private User $user;
    private ProjectFacade $projectFacade;

    public function __construct(

        //pouzity kvuli kompatibilite, jinak naprosto stejne, jako Translator
        ITranslator $translator,
        ProjectFacade $projectFacade,
        User $user
    )
	{
        parent::__construct($translator);
        $this->user = $user;
        $this->projectFacade = $projectFacade;
    }



	public function createComponentGrid(): DataGrid
	{
		$grid = parent::createGrid();

        if($this->user->isInRole(ERole::project_manager->name) && (!$this->user->isInRole(ERole::department_manager->name)))
        {
            $grid->setDataSource($this->projectFacade->getAllProjects($this->user->getId()));
        }
        else
        {
            $grid->setDataSource($this->projectFacade->getAllProjects());
        }



		$grid->addColumnText('id', 'app.project.id')
            ->setDefaultHide();

        if($this->user->isInRole(ERole::secretariat->name))
        {
        $grid->addColumnText('name', 'app.project.name')
            ->setSortable()
            ->setFilterText();
        }
        else
        {
            $grid->addColumnLink('name', 'app.project.name', ':detail')
                ->setSortable()
                ->setFilterText();
        }



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
                function()
                    {
                        return $this->translator->translate('app.project.indefiniteEnd');
                    },
                function (ActiveRow $row)
                    {
                        return $row->to === null;
                    })
            ->setSortable();

        $grid->addColumnText('description', 'app.project.description');


        $grid->addAction("edit", 'app.actions.edit', ":edit");
        $grid->addAction("addUser", 'app.project.addUser', ":addUser");

        $grid->allowRowsAction('edit', function(ActiveRow $row): bool {
            return $this->user->isInRole(ERole::secretariat->name) ||
                $this->user->isInRole(ERole::project_manager->name) ||
                $this->user->isInRole(ERole::department_manager->name);
        });

        $grid->allowRowsAction('addUser', function(ActiveRow $row): bool {
            return $this->user->isInRole(ERole::secretariat->name);
        });


		return $grid;
	}


}


interface IProjectGridFactory {

    public function create(): ProjectGrid;
}

