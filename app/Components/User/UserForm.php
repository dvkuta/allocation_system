<?php

namespace App\Components\User;

use App\Components\Base\BaseComponent;
use App\Model\User\Role\UserRoleRepository;
use App\Model\User\UserRepository;
use App\Tools\Transaction;
use Contributte\FormsBootstrap\BootstrapForm;
use Contributte\FormsBootstrap\BootstrapRenderer;
use Contributte\FormsBootstrap\Enums\RenderMode;
use Exception;
use Nette\Application\UI\Form;
use Nette\Localization\Translator;
use Nette\Security\AuthenticationException;
use Nette\Security\User;
use Nette\Utils\ArrayHash;
use Tracy\Debugger;
use Tracy\ILogger;

/**
 * Form component class for user CRUD
 * @package App\Components
 */
class UserForm extends BaseComponent {

    private UserRepository $userRepository;
    private Translator $translator;
    private UserRoleRepository $userRoleRepository;
    private Transaction $transaction;

    /**
     * @param UserRepository $userRepository
     */
    public function __construct(
        UserRepository $userRepository,
        Translator $translator,
        UserRoleRepository $userRoleRepository,
        Transaction $transaction,
    )
    {
        $this->userRepository = $userRepository;
        $this->translator = $translator;
        $this->userRoleRepository = $userRoleRepository;
        $this->transaction = $transaction;
    }


    /**
     * Factory function for creating sign in form
     * @return Form
     */
    public function createComponentForm():Form {

        $form = new BootstrapForm();
        $form->setTranslator($this->translator);
        $form->setRenderer(new BootstrapRenderer(RenderMode::SIDE_BY_SIDE_MODE));
        $form->addText('login','app.userForm.login');
        $form->addText('password',"app.userForm.password");
        $form->addText('firstname', 'app.userForm.firstname');
        $form->addText('lastname',  'app.userForm.lastname');
        $form->addText('workplace', 'app.userForm.workplace');
        $form->addEmail('email','app.userForm.email');
        $form->addSelect('user_role_id', 'app.userForm.roles', $this->userRoleRepository->fetchDataForSelect());


            $parentRow = $form->addRow();
        $parentRow->addCell(8)->addSubmit('cancel', 'app.baseForm.cancel')->setBtnClass('btn-danger');
        $submitCell = $parentRow->addCell(4)->addHtmlClass('inline-buttons');
        $submitCell->addSubmit('submit', 'app.baseForm.submit')->setBtnClass('btn-success');



        //$form->onValidate[] = [$this, 'validateForm'];
        $form->onSuccess[] = [$this, 'saveForm'];
        return $form;
    }

    /**
     * Function that is triggered by a successful form submission
     * @param Form $form
     * @param ArrayHash $values
     */
    public function saveForm(Form $form, ArrayHash $values) {

        try
        {
            //todo doresit heslo
            $this->transaction->begin();
            $this->userRepository->saveFiltered($values);
            $this->transaction->commit();
            $this->flashMessage('Operace se zdařila');
            bdump('aa');
        }
        catch (Exception $e)
        {
            $this->transaction->rollback();
            $this->flashMessage('Operace se nezdařila');
            Debugger::log($e,ILogger::EXCEPTION);
            bdump("eee");
        }

    }
}

interface IUserFormFactory
{
    public function create() :UserForm;
}