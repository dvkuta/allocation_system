<?php declare(strict_types=1);

namespace App\Presenters\Base;

/**
 * Trait Secured
 * Tento trait slouží k zamčení jednotlivých presentrů, pouze pro přihlášené uživatele
 * @package App\AdminModule\Presenter
 */
trait SecuredTrait
{
    public function startup()
    {
        parent::startup();
        if(!$this->user->isLoggedIn())
        {
            $this->redirect("Sign:in");
        }

    }
}
