<?php

namespace App\Components\Menu;

use Nette\Application\UI\Control;
use Nette\Security\User;

class MenuFactory extends Control
{

    private User $user;
    private array $menu;

    public function __construct(array $menu, User $user)
    {
        $this->user = $user;
        $this->menu = $menu;
    }


    public function render()
    {
        $this->template->user = $this->user;
        $this->template->menu = $this->menu;
        $this->template->currentPage = $this->presenter->name. ":";
        $this->template->render(__DIR__ . '/menuFactory.latte');
    }
}