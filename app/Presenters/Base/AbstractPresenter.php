<?php declare(strict_types = 1);

namespace App\Presenters\Base;
use App\Components\Menu\MenuFactory;
use Nette;
use Dibi\Connection;
use Nette\Application\UI\Presenter;
use Nette\DI\Attributes\Inject;
use stdClass;
use Ublaboo\DataGrid\DataGrid;
use UnexpectedValueException;

abstract class AbstractPresenter extends Presenter
{
    #[Inject]
    public MenuFactory $menuFactory;

	#[Inject]
	public Connection $dibiConnection;

	abstract public function createComponentGrid(): DataGrid;


	/**
	 * @param mixed $id
	 */
	public function changeStatus($id, string $newStatus): void
	{
		$id = (int) $id;

		if (in_array($newStatus, ['active', 'inactive', 'deleted'], true)) {
			$data = ['status' => $newStatus];

			$this->dibiConnection->update('users', $data)
				->where('id = ?', $id)
				->execute();
		}

		if ($this->isAjax()) {
			$grid = $this['grid'];

			if (!$grid instanceof DataGrid) {
				throw new UnexpectedValueException();
			}

			$grid->redrawItem($id);
			$this->flashMessage('Status changed'. $newStatus);
			$this->redrawControl('flashes');
		} else {
			$this->redirect('this');
		}
	}

	/**
	 * Aby se zobrazily flash messages, kdyz jdeme pres ajax.
	 *
	 * @param string $message
	 * @param string $type
	 * @return stdClass
	 */
	public function flashMessage($message, string $type = 'info'): stdClass
	{
		$flashMessage = parent::flashMessage($message, $type);
		if ($this->isAjax())
		{
			$this->redrawControl("flashes");
		}

		return $flashMessage;
	}

    public function createComponentMenu(): MenuFactory
    {
        return $this->menuFactory;
    }

}
