<?php

namespace App\Components\User;

use App\Components\Base\BaseComponent;
use App\Model\User\UserRepository;
use Contributte\FormsBootstrap\BootstrapForm;
use Contributte\FormsBootstrap\BootstrapRenderer;
use Contributte\FormsBootstrap\Enums\RenderMode;
use Nette\Application\UI\Form;
use Nette\Localization\Translator;
use Nette\Security\AuthenticationException;
use Nette\Security\User;
use Nette\Utils\ArrayHash;

/**
 * Form component class for user CRUD
 * @package App\Components
 */
class UserForm extends BaseComponent {

    private UserRepository $userRepository;
    private Translator $translator;

    /**
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository, Translator $translator)
    {
        $this->userRepository = $userRepository;
        $this->translator = $translator;
    }


    /**
     * Factory function for creating sign in form
     * @return Form
     */
    public function createComponentForm():Form {

        $form = new BootstrapForm();
        $form->setTranslator($this->translator);
        $form->setRenderer(new BootstrapRenderer(RenderMode::VERTICAL_MODE));
        $form->addText('firstname', 'app.userForm.firstname');

        $form->addText('lastname',  'app.userForm.lastname');
        $form->addEmail('email','app.userForm.email');
        $form->addText('login','app.userForm.login');

        $parentRow = $form->addRow();
        $parentRow->addCell(8)->addSubmit('cancel', 'Cancel')->setBtnClass('btn-danger');
        $submitCell = $parentRow->addCell(4)->addHtmlClass('inline-buttons');
        $submitCell->addSubmit('submit', 'Submit')->setBtnClass('btn-success');



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


    }
}

interface IUserFormFactory
{
    public function create() :UserForm;
}