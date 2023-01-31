<?php
namespace App\Components\Project\ProjectUserGrid;

use App\Components\Base\BaseGrid;
use App\Model\Project\ProjectUserAllocation\ProjectUserAllocationFacade;
use App\Model\Repository\Base\IProjectUserRepository;
use App\Tools\Utils;
use Nette\Database\Table\ActiveRow;
use Nette\Localization\ITranslator;
use Nette\Security\User;
use Ublaboo\DataGrid\DataGrid;


/**
 * Grid sloužící k zobrazení vztahu projekt - pracovník
 * na základě projectId a userId rozliší, který vztah má zobrazovat
 */
class ProjectUserGrid extends BaseGrid
{
    private ?int $projectId;
    private ?int $userId;

    private iProjectUserRepository $projectUserRepository;
    private ProjectUserAllocationFacade $allocationFacade;
    private User $user;


    public function __construct(
        ?int                        $projectId,
        ?int                        $userId,
        ITranslator                 $translator,
        IProjectUserRepository       $projectUserRepository,
        ProjectUserAllocationFacade $allocationFacade,
        User $user
    )
	{
        parent::__construct($translator);

        $this->projectUserRepository = $projectUserRepository;
        $this->projectId = $projectId;
        $this->userId = $userId;
        $this->allocationFacade = $allocationFacade;
        $this->user = $user;
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


		$grid->addColumnNumber('id', 'app.projectAllocation.id', 'project_id')
        ->setDefaultHide();

        if(isset($this->userId))
        {
        $grid->addColumnText('projectName','app.projectAllocation.name','project.name');
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

