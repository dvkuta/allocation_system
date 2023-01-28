<?php

namespace App\Components\User\UserForm;

use App\Components\Base\BaseComponent;
use App\Model\Exceptions\ProcessException;
use App\Model\User\Role\RoleRepository;
use App\Model\User\Role\UserRoleRepository;
use App\Model\User\UserFacade;
use App\Model\User\UserRepository;
use App\Tools\Transaction;
use Contributte\FormsBootstrap\BootstrapForm;
use Contributte\FormsBootstrap\BootstrapRenderer;
use Contributte\FormsBootstrap\Enums\RenderMode;
use Exception;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Nette\Forms\Form as FormAlias;
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
class UserForm extends BaseComponent
{

    private ?int $id;
    private UserRepository $userRepository;
    private Translator $translator;
    private RoleRepository $roleRepository;
    private UserFacade $userFacade;
    private UserRoleRepository $userRoleRepository;

    /**
     * @param UserRepository $userRepository
     */
    public function __construct(
        ?int           $id,
        UserRepository $userRepository,
        Translator     $translator,
        RoleRepository $roleRepository,
        UserRoleRepository $userRoleRepository,
        UserFacade     $userFacade,
    )
    {

        $this->id = $id;
        $this->userRepository = $userRepository;
        $this->translator = $translator;
        $this->roleRepository = $roleRepository;
        $this->userFacade = $userFacade;
        $this->userRoleRepository = $userRoleRepository;
    }

    public function render()
    {
        $defaults = array();

        if (isset($this->id)) {
            //todo
            $row = $this->userRepository->findRow($this->id);
            $roles =  $this->userRoleRepository->findRolesForUser($this->id);
            if ($row) {

                $defaults = $row->toArray();
                $defaults['user_role'] = array_flip($roles);
            } else {
                throw new BadRequestException();
            }
        }

        // nastaveni defaultnich hodnot formulare
        if (!empty($defaults)) {
            /** @var \Nette\Forms\Form $form */
            $form = $this["form"];
            $form->setDefaults($defaults);
        }

        parent::render();
    }

    /**
     * Factory function for creating sign in form
     * @return Form
     */
    public function createComponentForm(): Form
    {

        $form = new BootstrapForm();
        $form->setTranslator($this->translator);
        $form->setRenderer(new BootstrapRenderer(RenderMode::SIDE_BY_SIDE_MODE));
        $form->addText('login', 'app.user.login')
            ->addRule(FormAlias::REQUIRED, "app.baseForm.labelIsRequiredMasculine")
            ->addRule(FormAlias::MAX_LENGTH, "app.baseForm.labelCanBeOnlyLongMasculine",  100);

        $password = $form->addPassword('password', "app.user.password");

        if($this->id === null)
        {
            $password->addRule(FormAlias::REQUIRED, "app.baseForm.labelIsRequiredMasculine");
        }

        $form->addText('firstname', 'app.user.firstname')
            ->addRule(FormAlias::REQUIRED, "app.baseForm.labelIsRequiredMasculine")
            ->addRule(FormAlias::MAX_LENGTH, "app.baseForm.labelCanBeOnlyLongMasculine",  255);
        $form->addText('lastname', 'app.user.lastname')
            ->addRule(FormAlias::REQUIRED, "app.baseForm.labelIsRequiredMasculine")
            ->addRule(FormAlias::MAX_LENGTH, "app.baseForm.labelCanBeOnlyLongMasculine", 255);

        $form->addText('workplace', 'app.user.workplace')
            ->addRule(FormAlias::REQUIRED, "app.baseForm.labelIsRequiredMasculine")
            ->addRule(FormAlias::MAX_LENGTH, "app.baseForm.labelCanBeOnlyLongMasculine" ,255);

        $form->addText('email', 'app.user.email')
            ->addRule(FormAlias::EMAIL, "app.baseForm.labelDoesntHaveCorrectFormat")
            ->addRule(FormAlias::REQUIRED, "app.baseForm.labelIsRequiredMasculine")
            ->addRule(FormAlias::MAX_LENGTH, "app.baseForm.labelCanBeOnlyLongMasculine", 200);

        $roles = $this->roleRepository->fetchDataForSelect();
        bdump($roles);
        $roles = array_map(function ($role) { return $this->translator->translate($role);}, $roles);

        $form->addCheckboxList('user_role', 'app.user.role', $roles )
            ->setTranslator(null);

        $parentRow = $form->addRow();
        $parentRow->addCell(8)
            ->addButton('cancel', $this->translator->translate('app.baseForm.cancel'))
            ->setTranslator(null)
            ->setBtnClass('btn-danger')
            ->setHtmlAttribute("onclick", "window.location = '" . $this->presenter->link("default") . "';");;
        $submitCell = $parentRow->addCell(4)
            ->addHtmlClass('inline-buttons');
        $submitCell->addSubmit('submit', 'app.baseForm.save')
            ->setBtnClass('btn-success');


        //$form->onValidate[] = [$this, 'validateForm'];
        $form->onSuccess[] = [$this, 'saveForm'];
        return $form;
    }

    /**
     * Function that is triggered by a successful form submission
     * @param Form $form
     * @param ArrayHash $values
     */
    public function saveForm(Form $form, ArrayHash $values)
    {
        //TODO
        try {
            bdump($values);
            //vytvoreni uzivatele
            if($this->id === NULL)
            {
                $this->userFacade->createUser($values, $this->id);
            }
            else
            {
                $this->userFacade->editUser($values ,$this->id);
            }

            $this->presenter->flashMessage($this->translator->translate('app.baseForm.saveOK'), 'bg-success');
            $this->presenter->redirect("User:");
        } catch (ProcessException $e) {
            $form->addError($e->getMessage());
        }

    }
}

interface IUserFormFactory
{
    public function create(?int $id): UserForm;
}