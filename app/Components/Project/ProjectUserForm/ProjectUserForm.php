<?php

namespace App\Components\Project\ProjectUserForm;

use App\Components\Base\BaseComponent;
use App\Model\Exceptions\ProcessException;
use App\Model\Project\ProjectRepository;
use App\Model\Project\ProjectUser\EState;
use App\Model\Project\ProjectUser\ProjectUserFacade;
use App\Model\Project\ProjectUser\ProjectUserRepository;
use App\Tools\Utils;
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
 * Form component class for user CRUD
 * @package App\Components
 */
class ProjectUserForm extends BaseComponent
{

    private ?int $id;
    private bool $editAllocation;
    private Translator $translator;

    private ProjectRepository $projectRepository;
    private ProjectUserRepository $projectUserRepository;
    private ProjectUserFacade $projectUserFacade;


    public function __construct(
        ?int                  $id,
        bool $editAllocation,
        Translator            $translator,
        ProjectRepository $projectRepository,
        ProjectUserRepository $projectUserRepository,
        ProjectUserFacade $projectUserFacade
    )
    {

        $this->id = $id;
        $this->editAllocation = $editAllocation;
        $this->translator = $translator;
        $this->projectRepository = $projectRepository;
        $this->projectUserRepository = $projectUserRepository;
        $this->projectUserFacade = $projectUserFacade;
    }

    public function render()
    {
        $defaults = array();

        if (isset($this->id)) {
            if ($this->editAllocation === false)
            {
            $row = $this->projectRepository->findRow($this->id);
                if ($row) {
                    $defaults[ProjectRepository::COL_NAME] = $row[ProjectRepository::COL_NAME];
                } else {
                    throw new BadRequestException();
                }
            }
            else{
                $row = $this->projectUserRepository->findRow($this->id);

                if ($row) {
                    //TODO REPOSITORY
                    $defaults = $row->toArray();
                    $defaults['user'] = $row->user->firstname . ' ' . $row->user->lastname;
                    $defaults['name'] = $row->project->name;
                } else {
                    throw new BadRequestException();
                }
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

        $form->addText('name', 'app.projectAllocation.name')
            ->addRule(FormAlias::REQUIRED, "app.baseForm.labelIsRequiredMasculine")
            ->addRule(FormAlias::MAX_LENGTH, "app.baseForm.labelCanBeOnlyLongMasculine",  255)
            ->getControlPrototype()->setAttribute('readonly','readonly');

        if($this->editAllocation)
        {
            $form->addText('user', 'app.projectAllocation.user_id')
                ->addRule(FormAlias::REQUIRED, "app.baseForm.labelIsRequiredMasculine")
                ->getControlPrototype()->setAttribute('readonly','readonly');
        }
        else
        {
        $users = $this->projectUserRepository->getAllUsersThatDoesNotWorkOnProject($this->id);
        $form->addSelect('user_id', 'app.projectAllocation.user_id', $users)
        ->setTranslator(null);
        }
        $form->addDate('from', 'app.projectAllocation.from')
//            ->addRule(FormAlias::REQUIRED, "app.baseForm.labelIsRequiredMasculine")
        ;

        $form->addDate('to', 'app.projectAllocation.to')
//            ->addRule(FormAlias::REQUIRED, "app.baseForm.labelIsRequiredMasculine")
        ;

        $form->addInteger('allocation', 'app.projectAllocation.allocation');

        $form->addTextArea('description', 'app.projectAllocation.description');

        $states = Utils::getEnumValuesAsArray(EState::cases());

        $form->addSelect('state', 'app.projectAllocation.state', $states);

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


        $form->onValidate[] = [$this, 'validateForm'];
        $form->onSuccess[] = [$this, 'saveForm'];
        return $form;
    }

    public function validateForm(Form $form, ArrayHash $values)
    {
        bdump($values);
        if(!empty($values['to']))
        {
            if($values['from'] >= $values['to']) {
                $form->addError("app.project.dateError");
            }
        }
    }


    /**
     * Function that is triggered by a successful form submission
     * @param Form $form
     * @param ArrayHash $values
     * @throws AbortException
     */
    public function saveForm(Form $form, ArrayHash $values)
    {
        if(empty($values['to']))
        {
            $values['to'] = null;
        }

        try {

            //akce sekretarky
            if($this->editAllocation)
            {

            }
            else
            {
                $this->projectUserFacade->saveUserToProject($values, $this->id);
            }

            $this->presenter->flashMessage($this->translator->translate('app.baseForm.saveOK'), 'bg-success');
            $this->presenter->redirect("Project:");
        } catch (ProcessException $e) {
            $form->addError($e->getMessage());
        }

    }
}

interface IProjectUserFormFactory
{
    public function create(?int $id, bool $editAllocation = false): ProjectUserForm;
}