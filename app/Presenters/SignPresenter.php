<?php declare(strict_types = 1);

namespace App\Presenters;

use App\Components\Sign\IRegisterFormFactory;
use App\Components\Sign\ISignInFormFactory;
use App\Components\Sign\RegisterForm;
use App\Components\Sign\SignInForm;
use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;

final class SignPresenter extends Presenter
{


    private ISignInFormFactory $signInFormFactory;
    private IRegisterFormFactory $registerFormFactory;

    public function __construct(ISignInFormFactory $signInFormFactory, IRegisterFormFactory $registerFormFactory)
    {
        parent::__construct();
        $this->signInFormFactory = $signInFormFactory;
        $this->registerFormFactory = $registerFormFactory;
    }



    public function createComponentSignInForm(): SignInForm
    {
        $loginForm = $this->signInFormFactory->create();
        return $loginForm;

    }


}
