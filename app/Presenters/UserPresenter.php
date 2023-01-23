<?php declare(strict_types=1);

namespace App\Presenters;


use App\Components\Sign\ISignInFormFactory;


use App\Components\User\UserForm\IUserFormFactory;
use App\Components\User\UserForm\UserForm;
use App\Components\User\UserGrid\IUserGridFactory;
use App\Components\User\UserGrid\UserGrid;
use App\Presenters\Base\AbstractPresenter;
use App\Tools\Utils;
use App\UI\TEmptyLayoutView;


final class UserPresenter extends AbstractPresenter
{


    private ISignInFormFactory $signInFormFactory;
    private IUserGridFactory $userGridFactory;
    private IUserFormFactory $userFormFactory;


    public function __construct(IUserGridFactory $userGridFactory,
                                ISignInFormFactory $signInFormFactory,
                                IUserFormFactory   $userFormFactory,
    )
    {
        parent::__construct();

        $this->signInFormFactory = $signInFormFactory;

        $this->userFormFactory = $userFormFactory;
        $this->userGridFactory = $userGridFactory;
    }


    use TEmptyLayoutView;


    public function actionAdd()
    {

    }

    public function actionEdit(int $id)
    {

    }

    public function createComponentBasicGrid(): UserGrid
    {

        $grid = $this->userGridFactory->create();
        return $grid;

    }

    public function createComponentUserForm(): UserForm
    {
        $id = Utils::transformId($this->getParameter("id"));
        $form = $this->userFormFactory->create($id);
        return $form;

    }
}
