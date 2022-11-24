<?php declare(strict_types = 1);

namespace App\Presenters;

use App\Components\FormComponents\Sign\RegisterForm;
use App\Components\FormComponents\Sign\SignInForm;
use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;

final class SignPresenter extends Presenter
{


    private SignInForm $signInFormFactory;
    private RegisterForm $registerFormFactory;

    public function __construct(SignInForm $signInFormFactory, RegisterForm $registerFormFactory)
    {
        parent::__construct();

        $this->signInFormFactory = $signInFormFactory;
        $this->registerFormFactory = $registerFormFactory;
    }



    public function createComponentSignInForm(): Form
    {
        $loginForm = $this->signInFormFactory->create();
        $loginForm->onSuccess[] = function () {$this->flashMessage("ahoj z formu");};
        return $loginForm;

    }

    public function createComponentRegisterForm(): Form
    {
        $loginForm = $this->registerFormFactory->create();
        $loginForm->onSuccess[] = function () {$this->flashMessage("ahoj z register formu");};
        return $loginForm;

    }

}
