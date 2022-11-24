<?php declare(strict_types = 1);

namespace App\Presenters;

use App\Components\FormComponents\Sign\SignInForm;
use App\Components\GridComponents\BasicGrid\BasicGrid;
use App\Components\GridComponents\BasicGrid\IBasicGridFactory;
use App\Presenters\Base\AbstractPresenter;
use App\UI\TEmptyLayoutView;
use Nette\Application\UI\Form;
use Ublaboo\DataGrid\DataGrid;

final class BasicPresenter extends AbstractPresenter
{



	private SignInForm $signInFormFactory;
    private IBasicGridFactory $basicGridFactory;


    public function __construct(IBasicGridFactory $basicGridFactory,  SignInForm $signInFormFactory)
	{
		parent::__construct();

		$this->signInFormFactory = $signInFormFactory;
        $this->basicGridFactory = $basicGridFactory;
    }


	use TEmptyLayoutView;

	public function createComponentBasicGrid(): BasicGrid
	{

		$grid = $this->basicGridFactory->create();
		return $grid;

	}

	public function createComponentSignInForm(): Form
	{
        $this->flashMessage("ahoj z formu");
		$grid = $this->signInFormFactory->create();
		$grid->onSuccess[] = function () {$this->flashMessage("ahoj z formu");};
		return $grid;

	}

    public function createComponentGrid(): DataGrid
    {
        // TODO: Implement createComponentGrid() method.
        return new DataGrid();
    }
}
