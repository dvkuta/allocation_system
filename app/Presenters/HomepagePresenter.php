<?php declare(strict_types = 1);

namespace App\Presenters;


use App\Components\Project\ProjectUserAllocationGrid\IProjectUserAllocationGridFactory;
use App\Components\Project\ProjectUserAllocationGrid\ProjectUserAllocationGrid;
use App\Presenters\Base\AbstractPresenter;
use App\Presenters\Base\SecuredTrait;
use App\UI\TEmptyLayoutView;


final class HomepagePresenter extends AbstractPresenter
{

    use SecuredTrait;

    private IProjectUserAllocationGridFactory $allocationGridFactory;

    public function __construct(
        IProjectUserAllocationGridFactory $allocationGridFactory
    )
	{
		parent::__construct();

        $this->allocationGridFactory = $allocationGridFactory;
    }

    public function createComponentMyAllocationsGrid(): ProjectUserAllocationGrid
    {
        $id = 58;
        $form = $this->allocationGridFactory->create(null, $id);
        return $form;
    }

    public function createComponentSubordinateAllocationsGrid(): ProjectUserAllocationGrid
    {
        $id = 61;
        $form = $this->allocationGridFactory->create(null, null, $id);
        return $form;
    }


	use TEmptyLayoutView;


}
