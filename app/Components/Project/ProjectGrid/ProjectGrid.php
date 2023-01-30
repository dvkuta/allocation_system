<?php
namespace App\Components\Project\ProjectGrid;

use App\Components\Base\BaseGrid;
use App\Model\Repository\Base\IProjectRepository;
use Nette\Database\Table\ActiveRow;
use Nette\Localization\ITranslator;
use Ublaboo\DataGrid\DataGrid;

/**
 * Komponenta pro vytvoření tabulky pro projekty
 * Obsahuje odkazy na akce editace a přiřazení pracovníka do projektu
 */
class ProjectGrid extends BaseGrid
{
    private IProjectRepository $projectRepository;

    public function __construct(

        //pouzity kvuli kompatibilite, jinak naprosto stejne, jako Translator
        ITranslator $translator,
        IProjectRepository $projectRepository
    )
	{
        parent::__construct($translator);

        $this->projectRepository = $projectRepository;
    }



	public function createComponentGrid(): DataGrid
	{
		$grid = parent::createGrid();

		$grid->setDataSource($this->projectRepository->getAllProjects());

		$grid->addColumnText('id', 'app.project.id')
            ->setDefaultHide();

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

        //TODO prava
        $grid->addAction("edit", 'app.actions.edit', ":edit");

        $grid->addAction("addUser", 'app.project.addUser', ":addUser");


		return $grid;
	}


}


interface IProjectGridFactory {

    public function create(): ProjectGrid;
}

