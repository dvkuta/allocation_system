<?php declare(strict_types=1);

namespace App\Presenters;


use App\Components\Project\ProjectForm\IProjectFormFactory;
use App\Components\Project\ProjectForm\ProjectForm;
use App\Components\Project\ProjectGrid\IProjectGridFactory;
use App\Components\Project\ProjectGrid\ProjectGrid;
use App\Components\Project\ProjectUserForm\IProjectUserFormFactory;
use App\Components\Project\ProjectUserForm\ProjectUserForm;
use App\Components\Project\ProjectUserGrid\IProjectUserGridFactory;
use App\Components\Project\ProjectUserGrid\ProjectUserGrid;
use App\Presenters\Base\AbstractPresenter;
use App\Tools\Utils;
use App\UI\TEmptyLayoutView;


final class ProjectPresenter extends AbstractPresenter
{


    private IProjectFormFactory $projectFormFactory;
    private IProjectGridFactory $projectGridFactory;
    private IProjectUserFormFactory $projectUserFormFactory;
    private IProjectUserGridFactory $projectUserGridFactory;

    public function __construct(
        IProjectFormFactory $projectFormFactory,
        IProjectGridFactory $projectGridFactory,
        IProjectUserFormFactory $projectUserFormFactory,
        IProjectUserGridFactory $IProjectUserGridFactory

    )
    {
        parent::__construct();
        ;
        $this->projectFormFactory = $projectFormFactory;
        $this->projectGridFactory = $projectGridFactory;
        $this->projectUserFormFactory = $projectUserFormFactory;
        $this->projectUserGridFactory = $IProjectUserGridFactory;
    }


    use TEmptyLayoutView;


    public function actionAdd()
    {

    }

    public function actionEdit(int $id)
    {

    }

    public function actionAddUser(int $id)
    {

    }

    public function actionDetail(int $id)
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

    public function createComponentProjectUserForm(): ProjectUserForm
    {
        $id = Utils::transformId($this->getParameter("id"));
        $form = $this->projectUserFormFactory->create($id);
        return $form;

    }

    public function createComponentProjectUserGrid(): ProjectUserGrid
    {
        $id = Utils::transformId($this->getParameter("id"));
        $form = $this->projectUserGridFactory->create($id);
        return $form;

    }

}
