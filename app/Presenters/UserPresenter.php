<?php declare(strict_types=1);

namespace App\Presenters;


use App\Components\Sign\ISignInFormFactory;


use App\Components\User\SubordinateForm\ISubordinateFormFactory;
use App\Components\User\SubordinateForm\SubordinateForm;
use App\Components\User\UserForm\IUserFormFactory;
use App\Components\User\UserForm\UserForm;
use App\Components\User\UserGrid\IUserGridFactory;
use App\Components\User\UserGrid\UserGrid;
use App\Presenters\Base\BasePresenter;
use App\Presenters\Base\SecuredTrait;
use App\Tools\Utils;
use App\UI\TEmptyLayoutView;

/**
 * Stranka pro uzivatele
 * /user
 */
final class UserPresenter extends BasePresenter
{
    use SecuredTrait;


    private IUserGridFactory $userGridFactory;
    private IUserFormFactory $userFormFactory;
    private ISubordinateFormFactory $subordinateFormFactory;


    public function __construct(
        IUserGridFactory $userGridFactory,
        IUserFormFactory   $userFormFactory,
        ISubordinateFormFactory $subordinateFormFactory,
    )
    {
        parent::__construct();

        $this->userFormFactory = $userFormFactory;
        $this->userGridFactory = $userGridFactory;
        $this->subordinateFormFactory = $subordinateFormFactory;
    }


    use TEmptyLayoutView;

    /**
     * /user/add
     * @return void
     */
    public function actionAdd()
    {

    }

    /**
     * /user/edit/$id
     * @return void
     */
    public function actionEdit(int $id)
    {

    }

    /**
     * /user/addSubordinate/$id
     * @return void
     */
    public function actionAddSubordinate(int $id)
    {

    }


    public function createComponentUserGrid(): UserGrid
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

    public function createComponentSubordinateForm(): SubordinateForm
    {
        $id = Utils::transformId($this->getParameter("id"));
        $form = $this->subordinateFormFactory->create($id);
        return $form;

    }
}
