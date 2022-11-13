<?php declare(strict_types = 1);

namespace App\Presenters;

use App\Components\FormComponents\Sign\SignInForm;
use App\Components\GridComponents\BasicGrid;
use App\Presenters\Base\AbstractPresenter;
use App\UI\TEmptyLayoutView;
use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;
use Ublaboo\DataGrid\DataGrid;

final class SignPresenter extends Presenter
{


    private SignInForm $signInFormFactory;

    public function __construct(SignInForm $signInFormFactory)
    {
        parent::__construct();

        $this->signInFormFactory = $signInFormFactory;
    }



    public function createComponentSignInForm(): Form
    {

        $grid = $this->signInFormFactory->create();
        $grid->onSuccess[] = function () {$this->flashMessage("ahoj z formu");};
        return $grid;

    }

}
