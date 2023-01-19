<?php
namespace App\Components\BasicGrid;

use App\Components\Base\BaseComponent;
use App\Components\Base\BaseGrid;
use Nette\Database\Explorer;
use Nette\Database\Table\ActiveRow;
use Nette\Localization\ITranslator;
use Nette\Utils\DateTime;
use Ublaboo\DataGrid\DataGrid;

class BasicGrid extends BaseGrid
{
	private Explorer $explorer;



	/**
	 * @param Explorer $explorer
	 */
	public function __construct(Explorer $explorer, ITranslator $translator)
	{
        parent::__construct($translator);
		$this->explorer = $explorer;
	}



	public function createComponentGrid(): DataGrid
	{
		$grid = parent::createGrid();

		$grid->setDataSource($this->explorer->table('user'));

		$grid->addColumnText('id', 'Id');

		$grid->addColumnText('firstname', 'firstname')
        ->setSortable();

		$grid->addColumnText('lastname', 'lastname');

		$grid->addColumnText('email', 'email');

		$grid->addColumnText('user_role.name', 'role');


		return $grid;
	}


}


interface IBasicGridFactory {

    public function create(): BasicGrid;
}

