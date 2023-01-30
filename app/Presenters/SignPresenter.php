<?php declare(strict_types = 1);

namespace App\Presenters;

use App\Components\Sign\ISignInFormFactory;
use App\Components\Sign\SignInForm;
use App\Presenters\Base\BasePresenter;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;

/**
 * Stranka pro prihlaseni
 * /sign
 */
final class SignPresenter extends BasePresenter
{


    private ISignInFormFactory $signInFormFactory;

    public function __construct(ISignInFormFactory $signInFormFactory,)
    {
        parent::__construct();
        $this->signInFormFactory = $signInFormFactory;
    }

    /**
     * prihlaseni
     * /sign/in
     * @return void
     * @throws AbortException
     */
    public function actionIn(): void
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
