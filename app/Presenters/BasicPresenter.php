<?php declare(strict_types = 1);

namespace App\Presenters;

use App\Components\FormComponents\Sign\SignInForm;
use App\Components\GridComponents\BasicGrid;
use App\Presenters\Base\AbstractPresenter;
use App\UI\TEmptyLayoutView;
use Nette\Application\UI\Form;
use Ublaboo\DataGrid\DataGrid;

final class BasicPresenter extends AbstractPresenter
{


	private BasicGrid $basicGrid;
	private SignInForm $signInFormFactory;


	public function __construct(BasicGrid $basicGrid,  SignInForm $signInFormFactory)
	{
		parent::__construct();

		$this->basicGrid = $basicGrid;
		$this->signInFormFactory = $signInFormFactory;
	}


	use TEmptyLayoutView;

	public function createComponentGrid(): DataGrid
	{

		$grid = $this->basicGrid->createComponentGrid();
		return $grid;

	}

	public function createComponentSignInForm(): Form
	{
        $this->flashMessage("ahoj z formu");
		$grid = $this->signInFormFactory->create();
		$grid->onSuccess[] = function () {$this->flashMessage("ahoj z formu");};
		return $grid;

	}

}
