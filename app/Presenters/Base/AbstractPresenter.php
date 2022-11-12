<?php declare(strict_types = 1);

namespace App\Presenters\Base;
use Nette;
use Dibi\Connection;
use Nette\Application\UI\Presenter;
use Nette\ComponentModel\IComponent;
use Nette\DI\Attributes\Inject;
use Ublaboo\DataGrid\DataGrid;
use UnexpectedValueException;

abstract class AbstractPresenter extends Presenter
{

	private $context;

	public function injectContext(Nette\DI\Container $context)
	{
		$this->context = $context;
	}

	public function getContext(): Nette\DI\Container
	{
		return $this->context;
	}

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
			$this->flashMessage('Status changed');
			$this->redrawControl('flashes');
		} else {
			$this->redirect('this');
		}
	}

	/**
	 * Univerzální metoda pro vytváření komponent.
	 * Pokud se vyskytne v latte {control xyz}, tato metoda je zavolána s parametrem $name = "xyz".
	 * V metodě se otestuje, zda náhodou neexistuje metoda createComponentXyz,
	 * - pokud ano, zavolá se parent z Nette, ve kterém se zavolá createComponentXyz,
	 * - pokud ne, tak se zavolá vytvoření služby, jejíž název musí být v config.neon
	 *   (pokud není, spadne to).
	 *
	 * @param string $name Jmeno komponenty.
	 * @return ?IComponent Komponenta.
	 */
	protected function createComponent(string $name): ?IComponent
	{
		if (method_exists($this, "createComponent" . ucfirst($name)))
		{
			return parent::createComponent($name);
		}

		// Komponenta pro sluzbu Xyz se muze jmenovat xyz nebo Xyz
		try
		{
			// Nazev komponenty se shoduje presne
			$c = $this->context->createService($name);
		}
		catch (\Nette\DI\MissingServiceException $e)
		{
			// Zkusit "ucfirst" nazev
			try
			{
				// Pokus o prevod xyz na Xyz
				$c = $this->context->createService(ucfirst($name));
			}
			catch (\Nette\DI\MissingServiceException $e)
			{
				// "Vlastni" hlaska pro informaci o 2 pokusech
				throw new \Nette\DI\MissingServiceException("Services '$name' or '" . ucfirst($name) . "' not found.");
			}
		}

		// predani http requestu, pokud se v komponente vyskytuje setter pro nej
		if (method_exists($c, "setHttpRequest"))
		{
			$c->setHttpRequest($this->getHttpRequest()); // @phpstan-ignore-line
		}

		// predani translatoru, pokud se v komponente vyskytuje setter pro nej
		if (method_exists($c, "setTranslator"))
		{
			$c->setTranslator($this->translator); // @phpstan-ignore-line
		}

		// predani translatoru, pokud se v komponente vyskytuje setter pro nej
		if (method_exists($c, "setTranslator"))
		{
			$c->setTranslator($this->translator); // @phpstan-ignore-line
		}

		/* predani dalsich promennych komponente */

		// id
		$id = $this->getParameter("id");
		if ($id && method_exists($c, "setId"))
		{
			$c->setId($id); // @phpstan-ignore-line
		}

		// select
		$select = $this->getParameter("select");
		if ($select && method_exists($c, "setSelect"))
		{
			$c->setSelect($select); // @phpstan-ignore-line
		}

		// vrati se komponenta
		return $c;
	}

	/**
	 * Aby se zobrazily flash messages, kdyz jdeme pres ajax.
	 *
	 * @param string $message
	 * @param string $type
	 * @return \stdClass
	 */
	public function flashMessage($message, string $type = 'info'): \stdClass
	{
		$f = parent::flashMessage($message, $type);
		if ($this->isAjax())
		{
			$this->redrawControl("flashes");
		}

		return $f;
	}

}
