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
use App\Model\Repository\Base\IProjectRepository;
use App\Model\Repository\Base\IProjectUserAllocationRepository;
use App\Model\User\Role\ERole;
use App\Presenters\Base\BasePresenter;
use App\Presenters\Base\SecuredTrait;
use App\Tools\Utils;
use App\UI\TEmptyLayoutView;
use Nette\Application\BadRequestException;
use Nette\Application\UI\InvalidLinkException;

/**
 * Stranka /project
 */
final class ProjectPresenter extends BasePresenter
{
    use SecuredTrait;

    private IProjectFormFactory $projectFormFactory;
    private IProjectGridFactory $projectGridFactory;
    private IProjectUserFormFactory $projectUserFormFactory;
    private IProjectUserGridFactory $projectUserGridFactory;
    private IProjectUserAllocationGridFactory $allocationGridFactory;
    private IProjectUserAllocationFormFactory $allocationFormFactory;
    private IProjectRepository $projectRepository;
    private IProjectUserAllocationRepository $projectUserAllocationRepository;

    public function __construct(
        IProjectFormFactory $projectFormFactory,
        IProjectGridFactory $projectGridFactory,
        IProjectUserFormFactory $projectUserFormFactory,
        IProjectUserGridFactory $IProjectUserGridFactory,
        IProjectUserAllocationGridFactory $allocationGridFactory,
        IProjectUserAllocationFormFactory $allocationFormFactory,
        IProjectRepository $projectRepository,
        IProjectUserAllocationRepository $projectUserAllocationRepository,

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
        $this->projectUserAllocationRepository = $projectUserAllocationRepository;
    }


    use TEmptyLayoutView;


    /**
     * /project/add
     * @return void
     */
    public function actionAdd(): void
    {
        if(!$this->getUser()->isInRole(ERole::secretariat->name)){
            $this->error("",403);
        }
    }

    /**
     *
     * /project/edit/$id
     * @param int $id
     * @return void
     */
    public function actionEdit(int $id): void
    {
        if(!(
            $this->getUser()->isInRole(ERole::secretariat->name) ||
            $this->getUser()->isInRole(ERole::department_manager->name) ||
            $this->getUser()->isInRole(ERole::project_manager->name)
            )
        )
        {
            $this->error("",403);
        }

        if($this->getUser()->isInRole(ERole::project_manager->name)
            && (!$this->getUser()->isInRole(ERole::department_manager->name)
                && (!$this->getUser()->isInRole(ERole::secretariat->name))
            )
        )
        {
            if(!$this->projectRepository->isUserManagerOfProject($this->getUser()->getId(), $id))
            {
                $this->error("", 403);
            }
        }
    }

    /**
     * pridani uzivatele do projektu
     * /project/addUser/$id
     * @param int $id
     * @return void
     */
    public function actionAddUser(int $id): void
    {
        if(!$this->getUser()->isInRole(ERole::secretariat->name)){
            $this->error("",403);
        }
    }

    /**
     * /project/detail/$id
     * @param int $id
     * @return void
     * @throws BadRequestException
     * @throws InvalidLinkException
     */
    public function actionDetail(int $id): void
    {

        if(
            !(
                $this->getUser()->isInRole(ERole::project_manager->name) ||
                $this->getUser()->isInRole(ERole::department_manager->name)
            )

        )
        {
            $this->error("",403);
        }

        $project = $this->projectRepository->getProject($id);

        if(empty($project))
        {
            $this->error("", 404);
        }

        if($this->getUser()->isInRole(ERole::project_manager->name)
            && (!$this->getUser()->isInRole(ERole::department_manager->name))
        )
        {
            if(!$this->projectRepository->isUserManagerOfProject($this->getUser()->getId(), $id))
            {
                $this->error("", 403);
            }
        }

        $this->template->project = $project;
        $link = $this->link('Project:addAllocation',$id);
        $this->template->addAllocationLink = $link;
    }

    /**
     * /project/editAllocation/$id
     * @param int $id
     * @return void
     */
    public function actionEditAllocation(int $id): void
    {
        if(
            !(
            $this->getUser()->isInRole(ERole::project_manager->name) ||
            $this->getUser()->isInRole(ERole::department_manager->name)
            )
        )
        {
            $this->error("",403);
        }

        if($this->getUser()->isInRole(ERole::project_manager->name)
            && (!$this->getUser()->isInRole(ERole::department_manager->name))
        )
        {
            if(!$this->projectUserAllocationRepository->isUserProjectManagerOfProjectOfThisAllocation($this->getUser()->getId(), $id))
            {
                $this->error("", 403);
            }
        }
    }

    /** zobrazeni projektu uzivatele
     * /project/user/$id
     * @param int $id
     * @return void
     */
    public function actionUser(int $id): void
    {
        if(!$this->getUser()->isInRole(ERole::secretariat->name))
        {
            $this->error("",403);
        }


    }

    public function actionDefault()
    {
        if(
            !(
                $this->getUser()->isInRole(ERole::secretariat->name) ||
            $this->getUser()->isInRole(ERole::project_manager->name) ||
            $this->getUser()->isInRole(ERole::department_manager->name)
            )

        )
        {
            $this->error("",403);
        }
    }

    /**
     * /project/addAllocation/$id
     * @param int $id
     * @return void
     */
    public function actionAddAllocation(int $id): void
    {
        if(
            !(
                $this->getUser()->isInRole(ERole::project_manager->name) ||
                $this->getUser()->isInRole(ERole::department_manager->name)
            )
        )
        {
            $this->error("",403);
        }

        if($this->getUser()->isInRole(ERole::project_manager->name)
            && (!$this->getUser()->isInRole(ERole::department_manager->name))
        )
        {
            if(!$this->projectRepository->isUserManagerOfProject($this->getUser()->getId(), $id))
            {
                $this->error("", 403);
            }
        }
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
