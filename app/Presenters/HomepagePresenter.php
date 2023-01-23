<?php declare(strict_types = 1);

namespace App\Presenters;

use App\Components\BasicGrid\UserGrid;
use App\Components\BasicGrid\IBasicGridFactory;
use App\Components\Sign\ISignInFormFactory;
use App\Components\Sign\SignInForm;
use App\Components\User\IUserFormFactory;
use App\Components\User\UserForm;
use App\Presenters\Base\AbstractPresenter;
use App\UI\TEmptyLayoutView;
use Nette\Application\UI\Form;
use Nette\Security\User;
use Ublaboo\DataGrid\DataGrid;

final class HomepagePresenter extends AbstractPresenter
{

    public function __construct(
    )
	{
		parent::__construct();

    }


	use TEmptyLayoutView;


    public function createComponentGrid(): DataGrid
    {
        // TODO: Implement createComponentGrid() method.
        return new DataGrid();
    }
}
