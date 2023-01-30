<?php declare(strict_types = 1);

namespace App\Presenters;


use App\Components\Project\ProjectUserAllocationGrid\IProjectUserAllocationGridFactory;
use App\Components\Project\ProjectUserAllocationGrid\ProjectUserAllocationGrid;
use App\Presenters\Base\BasePresenter;
use App\Presenters\Base\SecuredTrait;
use App\UI\TEmptyLayoutView;
use Nette\Security\User;

/**
 * Stranka /
 */
final class HomepagePresenter extends BasePresenter
{

    use SecuredTrait;

    private IProjectUserAllocationGridFactory $allocationGridFactory;
    private User $user;

    public function __construct(
        IProjectUserAllocationGridFactory $allocationGridFactory,
        User $user,
    )
	{
		parent::__construct();

        $this->allocationGridFactory = $allocationGridFactory;
        $this->user = $user;
    }

    public function actionDefault()
    {

    }

    public function createComponentMyAllocationsGrid(): ProjectUserAllocationGrid
    {
        $id = $this->user->getId();
        $form = $this->allocationGridFactory->create(null, $id);
        return $form;
    }

    public function createComponentSubordinateAllocationsGrid(): ProjectUserAllocationGrid
    {
        $id = $this->user->getId();
        $form = $this->allocationGridFactory->create(null, null, $id);
        return $form;
    }


	use TEmptyLayoutView;


}
