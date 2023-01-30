<?php

namespace App\Components\Menu;

use App\Components\Base\BaseComponent;
use App\Components\Project\ProjectForm\ProjectForm;
use Nette\Application\UI\Control;
use Nette\Security\User;

/**
 * Komponenta pro menu
 * Vykreslí menu na základě práv uživatele
 */
class Menu extends BaseComponent
{

    private User $user;
    /** @var array Parametr, který je nunto konfigurovat. Nachází se v souboru menu.neon */
    private array $menu;

    /**
     * @param array $menu - konfigurovaný parametr je automaticky předán
     * @param User $user - předání aktuálně přihlášeného uživatele - DI
     */
    public function __construct(array $menu, User $user)
    {
        $this->user = $user;
        $this->menu = $menu;
    }


    public function render()
    {
        $this->template->user = $this->user;
        $this->template->menu = $this->menu;
        $this->template->currentPage = $this->presenter->name . ":";
        parent::render();
    }


}

interface IMenuFactory
{
    public function create(): Menu;

}
