<?php declare(strict_types = 1);

namespace App\Presenters;

use App\Components\BasicGrid;
use App\Components\BasicGridFactory;
use App\UI\TEmptyLayoutView;
use DateTime;
use Nette\Database\Explorer;
use Nette\Database\Row;
use Nette\Database\Table\ActiveRow;
use Ublaboo\DataGrid\DataGrid;

final class BasicPresenter extends AbstractPresenter
{


	private BasicGrid $basicGrid;

	public function __construct(BasicGrid $basicGrid)
	{
		parent::__construct();

		$this->basicGrid = $basicGrid;
	}


	use TEmptyLayoutView;

	public function createComponentGrid(): DataGrid
	{

		$grid = $this->basicGrid->createComponentGrid();

		return $grid;

	}

}
