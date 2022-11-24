<?php
namespace App\Components\GridComponents\BasicGrid;

use App\Components\Base\BaseComponent;
use Nette\Database\Explorer;
use Nette\Database\Table\ActiveRow;
use Nette\Utils\DateTime;
use Ublaboo\DataGrid\DataGrid;

class BasicGrid extends BaseComponent
{
	private Explorer $explorer;

	/**
	 * @param Explorer $explorer
	 */
	public function __construct(Explorer $explorer)
	{
		$this->explorer = $explorer;
	}



	public function createComponentGrid(): DataGrid
	{
		$grid = new DataGrid();

		$grid->setDataSource($this->explorer->table('users'));

		$grid->setItemsPerPageList([20, 50, 100], true);

		$grid->addColumnText('id', 'Id')
			->setSortable();

		$grid->addColumnText('email', 'E-mail')
			->setSortable()
			->setFilterText();

		$grid->addColumnText('name', 'Name')
			->setFilterText();

		$grid->addColumnDateTime('birth_date', 'Birthday')
			->setFormat('j. n. Y');

		$grid->addColumnNumber('age', 'Age')
			->setRenderer(function (ActiveRow $row): int {
				return $row['birth_date']->diff(new DateTime())->y;
			});


		return $grid;
	}


}


interface IBasicGridFactory {

    public function create(): BasicGrid;
}

