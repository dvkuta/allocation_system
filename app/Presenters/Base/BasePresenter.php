<?php declare(strict_types = 1);

namespace App\Presenters\Base;
use App\Components\Menu\IMenuFactory;
use App\Components\Menu\Menu;
use Contributte\Translation\LocalesResolvers\Session;
use Nette;
use Dibi\Connection;
use Nette\Application\UI\Presenter;
use Nette\DI\Attributes\Inject;
use Nette\Localization\Translator;
use stdClass;

/**
 * Spolecny presenter pro vsechny presentery, obsahuje translator, definici metu, atd...
 */
abstract class BasePresenter extends Presenter
{
    #[Inject]
    public IMenuFactory $menuFactory;

    #[Inject]
    public Translator $translator;

    #[Inject]
    public Session $translatorSessionResolver;


	/**
	 * Pro vykresleni flash message.
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

    public function createComponentMenu(): Menu
    {
        return $this->menuFactory->create();
    }

    public function handleChangeLocale(string $locale) {

        $this->translatorSessionResolver->setLocale($locale);
    }



}
