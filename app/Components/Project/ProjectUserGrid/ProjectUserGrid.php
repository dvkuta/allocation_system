<?php
namespace App\Components\Project\ProjectUserGrid;

use App\Components\Base\BaseComponent;
use App\Components\Base\BaseGrid;
use App\Model\Project\ProjectRepository;
use App\Model\Project\ProjectUser\ProjectUserRepository;
use App\Model\Project\ProjectUserAllocation\ProjectUserAllocationFacade;
use App\Model\User\Role\RoleRepository;
use App\Model\User\UserRepository;
use App\Tools\Utils;
use Nette\Database\Explorer;
use Nette\Database\Table\ActiveRow;
use Nette\Localization\ITranslator;
use Nette\Utils\DateTime;
use Ublaboo\DataGrid\AggregationFunction\FunctionSum;
use Ublaboo\DataGrid\Column\Action\Confirmation\StringConfirmation;
use Ublaboo\DataGrid\DataGrid;
use Ublaboo\DataGrid\Row;

/**
 * Grid sloužící k zobrazení vztahu projekt - pracovník
 * na základě projectId a userId rozliší, který vztah má zobrazovat
 */
class ProjectUserGrid extends BaseGrid
{
    private ?int $projectId;
    private ?int $userId;

    private ProjectUserRepository $projectUserRepository;
    private ProjectUserAllocationFacade $allocationFacade;


    public function __construct(
        ?int $projectId,
        ?int $userId,
        ITranslator           $translator,
        ProjectUserRepository $projectUserRepository,
        ProjectUserAllocationFacade $allocationFacade,
    )
	{
        parent::__construct($translator);

        $this->projectUserRepository = $projectUserRepository;
        $this->projectId = $projectId;
        $this->userId = $userId;
        $this->allocationFacade = $allocationFacade;
    }


    /**
     *  Definice gridu
     */
	public function createComponentGrid(): DataGrid
	{
		$grid = parent::createGrid();

        if(isset($this->projectId))
        {
            $grid->setDataSource($this->projectUserRepository->getAllUsersOnProjectGridSelection($this->projectId));
        }
        else if(isset($this->userId))
        {
            $grid->setDataSource($this->projectUserRepository->getAllUserProjectGridSelection($this->userId));
        }
        else
        {
            $this->error();
        }


		$grid->addColumnNumber('id', 'app.projectAllocation.id', 'project_id');

        if(isset($this->userId))
        {
            $grid->addColumnLink('projectName','app.projectAllocation.name',  "Project:detail", 'project.name', ["id" => "project_id"]);
        }

        $grid->addColumnText('user_id', 'app.projectAllocation.user_id')
            ->setRenderer(function(ActiveRow $row) {
                return $row->user->firstname . " " . $row->user->lastname . " (". $row->user->email. ")" ;
            })
            ->setSortable();

        if(isset($this->projectId))
        {
            $grid->addColumnNumber('currentAllocation', 'app.projectAllocation.currentAllocation')
                ->setRenderer(function(ActiveRow $row) {
                    $userId = $row->user->id;
                    $allocation = $this->allocationFacade->getCurrentWorkloadForUser($userId);
                    return Utils::getAllocationString($allocation, ProjectUserAllocationFacade::MAX_ALLOCATION);
                });

            $grid->addColumnNumber('allAllocations', 'app.projectAllocation.totalAllocation')
                ->setRenderer(function(ActiveRow $row) {
                    $userId = $row->user->id;
                    return $this->allocationFacade->getAllAllocationStatistic($userId);
                });
        }

		return $grid;
	}

}


interface IProjectUserGridFactory {

    public function create(?int $projectId = null, ?int $userId = null): ProjectUserGrid;
}

