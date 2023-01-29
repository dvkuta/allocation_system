<?php declare(strict_types = 1);

namespace App\Presenters;

use App\Components\Sign\IRegisterFormFactory;
use App\Components\Sign\ISignInFormFactory;
use App\Components\Sign\RegisterForm;
use App\Components\Sign\SignInForm;
use App\Presenters\Base\AbstractPresenter;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;

final class SignPresenter extends AbstractPresenter
{


    private ISignInFormFactory $signInFormFactory;
    private IRegisterFormFactory $registerFormFactory;

    public function __construct(ISignInFormFactory $signInFormFactory,
                                IRegisterFormFactory $registerFormFactory)
    {
        parent::__construct();
        $this->signInFormFactory = $signInFormFactory;
        $this->registerFormFactory = $registerFormFactory;
    }

    public function actionIn()
    {
        if($this->getUser()->isLoggedIn())
        {
            $this->redirect("Homepage:");
        }
    }

    /**
     * Odhlášení.
     * @throws AbortException
     */
    public function actionOut()
    {

        $this->getUser()->logout();
        $this->flashMessage($this->translator->translate('admin.sign.signOut'));
        $this->redirect('in');
    }

    public function createComponentSignInForm(): SignInForm
    {
        $loginForm = $this->signInFormFactory->create();
        return $loginForm;

    }


}
