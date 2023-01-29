<?php declare(strict_types = 1);

namespace App\Presenters;

use App\Components\BasicGrid\UserGrid;
use App\Components\BasicGrid\IBasicGridFactory;
use App\Components\Project\ProjectForm\ProjectForm;
use App\Components\Project\ProjectUserAllocationGrid\IProjectUserAllocationGridFactory;
use App\Components\Project\ProjectUserAllocationGrid\ProjectUserAllocationGrid;
use App\Components\Sign\ISignInFormFactory;
use App\Components\Sign\SignInForm;
use App\Components\User\IUserFormFactory;
use App\Components\User\UserForm;
use App\Presenters\Base\AbstractPresenter;
use App\Tools\Utils;
use App\UI\TEmptyLayoutView;
use Nette\Application\UI\Form;
use Nette\Security\User;
use Ublaboo\DataGrid\DataGrid;

final class HomepagePresenter extends AbstractPresenter
{

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
