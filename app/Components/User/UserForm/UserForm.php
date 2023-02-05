<?php

namespace App\Components\User\UserForm;

use App\Components\Base\BaseComponent;
use App\Model\Exceptions\ProcessException;
use App\Model\User\Role\RoleFacade;
use App\Model\User\UserFacade;
use Contributte\FormsBootstrap\BootstrapForm;
use Contributte\FormsBootstrap\BootstrapRenderer;
use Contributte\FormsBootstrap\Enums\RenderMode;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Nette\Forms\Form as FormAlias;
use Nette\Localization\Translator;
use Nette\Utils\ArrayHash;


/**
 * Formulář pro úpravu a přidávání uživatelů
 */
class UserForm extends BaseComponent
{

    private ?int $id; //id uživatele, pouze při editaci
    private Translator $translator;
    private UserFacade $userFacade;
    private RoleFacade $roleFacade;

    /**
     * @param int|null $id
     * @param Translator $translator
     * @param UserFacade $userFacade
     * @param RoleFacade $roleFacade
     */
    public function __construct(
        ?int               $id,
        Translator         $translator,
        UserFacade         $userFacade,
        RoleFacade         $roleFacade
    )
    {

        $this->id = $id;
        $this->translator = $translator;
        $this->userFacade = $userFacade;
        $this->roleFacade = $roleFacade;
    }

    public function render()
    {
        $defaults = array();

        if (isset($this->id)) {

            $user = $this->userFacade->getUser($this->id);
            $roles =  $this->userFacade->findRolesForUser($this->id);
            if ($user) {

                $defaults = [
                    'login' => $user->getLogin(),
                    'firstname' => $user->getFirstname(),
                    'lastname' => $user->getLastname(),
                    'workplace' => $user->getWorkplace(),
                    'email' => $user->getEmail(),

                ];
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
     * Definice formuláře
     */
    public function createComponentForm(): Form
    {

        $form = new BootstrapForm();
        $form->setAutoShowValidation(false);
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

        $roles = $this->roleFacade->fetchDataForSelect();

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
     * Funkce zavolaná po úspěšném odeslání formuláře
     * @param Form $form
     * @param ArrayHash $values
     */
    public function saveForm(Form $form, ArrayHash $values)
    {

        try {
            //vytvoreni uzivatele
            if($this->id === NULL)
            {
                $this->userFacade->createUser(
                    $values['firstname'],
                    $values['lastname'],
                    $values['email'],
                    $values['login'],
                    $values['workplace'],
                    $values['password'],
                    $values['user_role']);
            }
            else
            {
                $this->userFacade->editUser(
                    $this->id,
                    $values['firstname'],
                    $values['lastname'],
                    $values['email'],
                    $values['login'],
                    $values['workplace'],
                    $values['password'],
                    $values['user_role']);
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