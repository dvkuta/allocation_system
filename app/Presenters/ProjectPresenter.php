<?php declare(strict_types=1);

namespace App\Presenters;


use App\Components\Project\ProjectForm\IProjectFormFactory;
use App\Components\Project\ProjectForm\ProjectForm;
use App\Components\Project\ProjectGrid\IProjectGridFactory;
use App\Components\Project\ProjectGrid\ProjectGrid;
use App\Presenters\Base\AbstractPresenter;
use App\Tools\Utils;
use App\UI\TEmptyLayoutView;


final class ProjectPresenter extends AbstractPresenter
{


    private IProjectFormFactory $projectFormFactory;
    private IProjectGridFactory $projectGridFactory;

    public function __construct(
        IProjectFormFactory $projectFormFactory,
        IProjectGridFactory $projectGridFactory,

    )
    {
        parent::__construct();
        ;
        $this->projectFormFactory = $projectFormFactory;
        $this->projectGridFactory = $projectGridFactory;
    }


    use TEmptyLayoutView;


    public function actionAdd()
    {

    }

    public function actionEdit(int $id)
    {

    }

    public function createComponentProjectGrid(): ProjectGrid
    {

        $grid = $this->projectGridFactory->create();
        return $grid;

    }

    public function createComponentProjectForm(): ProjectForm
    {
        $id = Utils::transformId($this->getParameter("id"));
        $form = $this->projectFormFactory->create($id);
        return $form;

    }
}
