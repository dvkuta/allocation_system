<?php declare(strict_types=1);

namespace App\Presenters;


use App\Components\Project\ProjectForm\IProjectFormFactory;
use App\Components\Project\ProjectForm\ProjectForm;
use App\Components\Project\ProjectGrid\IProjectGridFactory;
use App\Components\Project\ProjectGrid\ProjectGrid;
use App\Components\Project\ProjectUserAllocationForm\IProjectUserAllocationFormFactory;
use App\Components\Project\ProjectUserAllocationForm\ProjectUserAllocationForm;
use App\Components\Project\ProjectUserAllocationGrid\IProjectUserAllocationGridFactory;
use App\Components\Project\ProjectUserAllocationGrid\ProjectUserAllocationGrid;
use App\Components\Project\ProjectUserForm\IProjectUserFormFactory;
use App\Components\Project\ProjectUserForm\ProjectUserForm;
use App\Components\Project\ProjectUserGrid\IProjectUserGridFactory;
use App\Components\Project\ProjectUserGrid\ProjectUserGrid;
use App\Model\Project\ProjectRepository;
use App\Presenters\Base\AbstractPresenter;
use App\Tools\Utils;
use App\UI\TEmptyLayoutView;
use Nette\Application\BadRequestException;


final class ProjectPresenter extends AbstractPresenter
{


    private IProjectFormFactory $projectFormFactory;
    private IProjectGridFactory $projectGridFactory;
    private IProjectUserFormFactory $projectUserFormFactory;
    private IProjectUserGridFactory $projectUserGridFactory;
    private IProjectUserAllocationGridFactory $allocationGridFactory;
    private IProjectUserAllocationFormFactory $allocationFormFactory;
    private ProjectRepository $projectRepository;

    public function __construct(
        IProjectFormFactory $projectFormFactory,
        IProjectGridFactory $projectGridFactory,
        IProjectUserFormFactory $projectUserFormFactory,
        IProjectUserGridFactory $IProjectUserGridFactory,
        IProjectUserAllocationGridFactory $allocationGridFactory,
        IProjectUserAllocationFormFactory $allocationFormFactory,
        ProjectRepository $projectRepository

    )
    {
        parent::__construct();
        ;
        $this->projectFormFactory = $projectFormFactory;
        $this->projectGridFactory = $projectGridFactory;
        $this->projectUserFormFactory = $projectUserFormFactory;
        $this->projectUserGridFactory = $IProjectUserGridFactory;
        $this->allocationGridFactory = $allocationGridFactory;
        $this->allocationFormFactory = $allocationFormFactory;
        $this->projectRepository = $projectRepository;
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
        $project = $this->projectRepository->getProject($id);

        if(empty($project))
        {
            $this->error("Error message", 404);
        }

        $this->template->project = $project;
        $link = $this->link('Project:addAllocation',$id);
        $this->template->addAllocationLink = $link;
    }

    public function actionEditAllocation(int $id)
    {

    }

    public function actionUser(int $id)
    {

    }

    public function actionAddAllocation(int $id)
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
        $action = $this->getAction();
        $projectId = null;
        $userId = null;

        if ($action == 'detail')
        {
            $projectId = $id;
        } else if ($action == 'user')
        {
            $userId = $id;
        }

        $form = $this->projectUserGridFactory->create($projectId, $userId);
        return $form;

    }

    public function createComponentProjectUserAllocationForm(): ProjectUserAllocationForm
    {

        $action = $this->getAction();
        $editAllocation = $action === 'editAllocation';
        $id = Utils::transformId($this->getParameter("id"));
        $form = $this->allocationFormFactory->create($id, $editAllocation);
        return $form;

    }


    public function createComponentProjectUserAllocationGrid(): ProjectUserAllocationGrid
    {
        $id = Utils::transformId($this->getParameter("id"));

        $grid = $this->allocationGridFactory->create($id);
        return $grid;

    }

}
