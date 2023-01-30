<?php

namespace App\Components\User\SubordinateForm;

use App\Components\Base\BaseComponent;
use App\Model\DTO\UserDTO;
use App\Model\Exceptions\ProcessException;
use App\Model\Repository\Base\IUserRepository;
use App\Model\Repository\Base\IUserRoleRepository;
use App\Model\User\Role\ERole;
use App\Model\User\Superior\SuperiorUserFacade;
use Contributte\FormsBootstrap\BootstrapForm;
use Contributte\FormsBootstrap\BootstrapRenderer;
use Contributte\FormsBootstrap\Enums\RenderMode;
use Nette\Application\AbortException;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Nette\Forms\Form as FormAlias;
use Nette\Localization\Translator;
use Nette\Utils\ArrayHash;

/**
 * Formulář sloužící k přiřazení pracovníků k nadřízeným
 */
class SubordinateForm extends BaseComponent
{
    // id nadřízeného
    private ?int $id;
    private Translator $translator;
    private IUserRepository $userRepository;
    private IUserRoleRepository $userRoleRepository;
    private SuperiorUserFacade $superiorUserFacade;


    /**
     * @param int|null $id
     * @param Translator $translator
     * @param iUserRepository $userRepository
     * @param iUserRoleRepository $userRoleRepository
     * @param SuperiorUserFacade $superiorUserFacade
     */
    public function __construct(
        ?int               $id,
        Translator         $translator,
        IUserRepository     $userRepository,
        IUserRoleRepository $userRoleRepository,
        SuperiorUserFacade $superiorUserFacade,
    )
    {

        $this->id = $id;
        $this->translator = $translator;

        $this->userRepository = $userRepository;
        $this->userRoleRepository = $userRoleRepository;
        $this->superiorUserFacade = $superiorUserFacade;
    }

    public function render()
    {
        $defaults = array();

        if (isset($this->id)) {
            /** @var UserDTO $user */
            $user = $this->userRepository->getUser($this->id);
            if ($user) {
                $defaults['name'] = $user->getFullName();
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
     * @return Form
     */
    public function createComponentForm(): Form
    {
        $form = new BootstrapForm();
        $form->setTranslator($this->translator);
        $form->setRenderer(new BootstrapRenderer(RenderMode::SIDE_BY_SIDE_MODE));
        $form->setAutoShowValidation(false);

        $form->addText('name', 'app.subordinate.superior')
            ->addRule(FormAlias::REQUIRED, "app.baseForm.labelIsRequiredMasculine")
            ->addRule(FormAlias::MAX_LENGTH, "app.baseForm.labelCanBeOnlyLongMasculine",  255)
            ->getControlPrototype()->setAttribute('readonly','readonly');

        $users = $this->userRoleRepository->getAllUsersInRole(ERole::worker);
        $form->addSelect('user_id', 'app.subordinate.worker', $users)
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

        $form->onSuccess[] = [$this, 'saveForm'];
        return $form;
    }


    /**
     * Funkce zavolaná po úspěšném odeslání formuláře
     * @param Form $form
     * @param ArrayHash $values
     * @throws AbortException
     */
    public function saveForm(Form $form, ArrayHash $values)
    {

        try {

            $this->superiorUserFacade->save($this->id, $values['user_id']);

            $this->presenter->flashMessage($this->translator->translate('app.baseForm.saveOK'), 'bg-success');
            $this->presenter->redirect("User:");
        } catch (ProcessException $e) {
            $form->addError($e->getMessage());
        }

    }
}

interface ISubordinateFormFactory
{
    public function create(?int $id): SubordinateForm;
}