<?php

namespace App\Components\Project\ProjectForm;

use App\Components\Base\BaseComponent;
use App\Components\Project\ProjectGrid\ProjectGrid;
use App\Model\Exceptions\ProcessException;
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
class ProjectForm extends BaseComponent
{

    private ?int $id;
    private UserRepository $userRepository;
    private Translator $translator;
    private UserRoleRepository $userRoleRepository;
    private UserFacade $userFacade;

    /**
     * @param UserRepository $userRepository
     */
    public function __construct(
        ?int               $id,
        UserRepository     $userRepository,
        Translator         $translator,
        UserRoleRepository $userRoleRepository,
        UserFacade $userFacade,
    )
    {

        $this->id = $id;
        $this->userRepository = $userRepository;
        $this->translator = $translator;
        $this->userRoleRepository = $userRoleRepository;
        $this->userFacade = $userFacade;
    }

    public function render()
    {
        $defaults = array();

        if (isset($this->id)) {
            $row = $this->userRepository->findRow($this->id);

            if ($row) {
                $defaults = $row->toArray();
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

        $form->addText('name', 'app.project.name')
            ->addRule(FormAlias::REQUIRED, "app.baseForm.labelIsRequiredMasculine")
            ->addRule(FormAlias::MAX_LENGTH, "app.baseForm.labelCanBeOnlyLongMasculine",  255);

        $form->addSelect('user_id', 'app.project.user_id');

        $form->addDate('from', 'app.project.from')
            ->addRule(FormAlias::REQUIRED, "app.baseForm.labelIsRequiredMasculine")
            ->addRule(FormAlias::MAX_LENGTH, "app.baseForm.labelCanBeOnlyLongMasculine" ,255);

        $form->addDate('to', 'app.project.to');

        $form->addTextArea('description', 'app.project.description');

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

        try {

            //vytvoreni uzivatele
            if($this->id === NULL)
            {
//                $this->userFacade->createUser($values, $this->id);
            }
            else
            {
//                $this->userFacade->editUser($values ,$this->id);
            }

            $this->presenter->flashMessage($this->translator->translate('app.baseForm.saveOK'), 'bg-success');
            $this->presenter->redirect("User:");
        } catch (ProcessException $e) {
            $form->addError($e->getMessage());
        }

    }
}

interface IProjectFormFactory
{
    public function create(?int $id): ProjectForm;
}