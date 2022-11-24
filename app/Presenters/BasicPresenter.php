<?php declare(strict_types = 1);

namespace App\Presenters;

use App\Components\BasicGrid\BasicGrid;
use App\Components\BasicGrid\IBasicGridFactory;
use App\Components\Sign\ISignInFormFactory;
use App\Components\Sign\SignInForm;
use App\Presenters\Base\AbstractPresenter;
use App\UI\TEmptyLayoutView;
use Nette\Application\UI\Form;
use Ublaboo\DataGrid\DataGrid;

final class BasicPresenter extends AbstractPresenter
{



	private ISignInFormFactory $signInFormFactory;
    private IBasicGridFactory $basicGridFactory;


    public function __construct(IBasicGridFactory $basicGridFactory,  ISignInFormFactory $signInFormFactory)
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

	public function createComponentSignInForm(): SignInForm
	{
		$grid = $this->signInFormFactory->create();
		return $grid;

	}

    public function createComponentGrid(): DataGrid
    {
        // TODO: Implement createComponentGrid() method.
        return new DataGrid();
    }
}
