<?php
namespace App\Components\Project\ProjectUserAllocationGrid;

use App\Components\Base\BaseComponent;
use App\Components\Base\BaseGrid;
use App\Model\Project\ProjectRepository;
use App\Model\Project\ProjectUser\EState;
use App\Model\Project\ProjectUser\ProjectUserRepository;
use App\Model\Project\ProjectUserAllocation\ProjectUserAllocationFacade;
use App\Model\Project\ProjectUserAllocation\ProjectUserAllocationRepository;
use App\Model\User\Role\ERole;
use App\Model\User\Role\RoleRepository;
use App\Model\User\UserRepository;
use App\Tools\Utils;
use Nette\Database\Explorer;
use Nette\Database\Table\ActiveRow;
use Nette\Localization\ITranslator;
use Nette\Security\User;
use Nette\Utils\DateTime;
use Ublaboo\DataGrid\AggregationFunction\FunctionSum;
use Ublaboo\DataGrid\Column\Action\Confirmation\StringConfirmation;
use Ublaboo\DataGrid\DataGrid;
use Ublaboo\DataGrid\Row;

/**
 * Grid pro vykreslení alokací uživatelů.
 * Podle parametrů určí, jestli zobrazovat alokace relevantní k projektu, uživateli, či nadřízenému
 */
class ProjectUserAllocationGrid extends BaseGrid
{
    private ?int $projectId;
    private ProjectUserAllocationFacade $allocationFacade;
    private ?int $userId;
    private ?int $superiorId;
    private User $user;


    /**
     * @param int|null $projectId
     * @param int|null $userId
     * @param int|null $superiorId
     * @param ITranslator $translator
     * @param ProjectUserAllocationFacade $allocationFacade
     */
    public function __construct(
        ?int                        $projectId,
        ?int                        $userId,
        ?int                        $superiorId,
        ITranslator                 $translator,
        ProjectUserAllocationFacade $allocationFacade,
        User $user

    )
	{
        parent::__construct($translator);

        $this->projectId = $projectId;

        $this->allocationFacade = $allocationFacade;
        $this->userId = $userId;
        $this->superiorId = $superiorId;
        $this->user = $user;
    }


    /**
     * Definice gridu
     */
	public function createComponentGrid(): DataGrid
	{
		$grid = parent::createGrid();

        if(isset($this->projectId))
        {
            $grid->setDataSource($this->allocationFacade->getProjectUserAllocationGridSelection($this->projectId));
        }
        else if(isset($this->userId))
        {
            $grid->setDataSource($this->allocationFacade->getAllUserAllocationsGridSelection($this->userId));
        }
        else if(isset($this->superiorId))
        {
            $grid->setDataSource($this->allocationFacade->getAllSubordinateAllocationsGridSelection($this->superiorId));
        }
        else
        {
            $this->error();
        }

		$grid->addColumnText('id', 'app.projectAllocation.id')
            ->setDefaultHide();

        if(isset($this->userId)) {
            $grid->addColumnText('projectName','app.projectAllocation.name')
            ->setRenderer(function (ActiveRow $row) {
                return $row->project_user->project->name;

            });
        }

        $grid->addColumnText('user_id', 'app.projectAllocation.user_id')
            ->setRenderer(function( ActiveRow $row) {
                return $row->project_user->user->firstname . " " . $row->project_user->user->lastname ;
            });

        $grid->addColumnNumber('allocation','app.projectAllocation.allocation')
            ->setRenderer(function( ActiveRow $row) {
                return Utils::getAllocationString($row->allocation, ProjectUserAllocationFacade::MAX_ALLOCATION);
            })
        ->setSortable();

        $grid->addColumnDateTime('from', 'app.projectAllocation.from')
            ->setSortable();

        $grid->addColumnDateTime('to', 'app.projectAllocation.to')
            ->setSortable();

        $grid->addColumnText('description', 'app.projectAllocation.description');
        $grid->addColumnText('state', 'app.projectAllocation.state')
        ->setRenderer(function (ActiveRow $row) {
            $state = $this->allocationFacade->calculateState($row->to, EState::from($row->state));
            return $this->translator->translate('app.projectAllocation.' . $state);
        });


        $grid->addAction("edit", 'app.actions.edit', "Project:editAllocation");
        $grid->allowRowsAction('edit', function(ActiveRow $row): bool {
            return $this->user->isInRole(ERole::project_manager->name) || $this->user->isInRole(ERole::department_manager->name);
        });



		return $grid;
	}


}


interface IProjectUserAllocationGridFactory {

    public function create(?int $projectId = null, ?int $userId = null, ?int $superiorId = null): ProjectUserAllocationGrid;
}

