<?php

namespace App\Components\Project\ProjectUserAllocationForm;

use App\Components\Base\BaseComponent;
use App\Model\Exceptions\ProcessException;
use App\Model\Project\ProjectRepository;
use App\Model\Project\ProjectUser\EState;
use App\Model\Project\ProjectUser\ProjectUserFacade;
use App\Model\Project\ProjectUser\ProjectUserRepository;
use App\Model\Project\ProjectUserAllocation\ProjectUserAllocationFacade;
use App\Model\Project\ProjectUserAllocation\ProjectUserAllocationRepository;
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
class ProjectUserAllocationForm extends BaseComponent
{

    private ?int $id;
    private bool $editAllocation;
    private Translator $translator;
    private ProjectUserRepository $projectUserRepository;
    private ProjectRepository $projectRepository;
    private ProjectUserAllocationFacade $allocationFacade;
    private ProjectUserAllocationRepository $allocationRepository;


    public function __construct(
        ?int                  $id,
        bool $editAllocation,
        Translator            $translator,
        ProjectUserRepository $projectUserRepository,
        ProjectRepository $projectRepository,
        ProjectUserAllocationFacade $allocationFacade,
        ProjectUserAllocationRepository $allocationRepository,
    )
    {

        $this->id = $id;
        $this->editAllocation = $editAllocation;
        $this->translator = $translator;
        $this->projectUserRepository = $projectUserRepository;
        $this->projectRepository = $projectRepository;
        $this->allocationFacade = $allocationFacade;
        $this->allocationRepository = $allocationRepository;
    }

    /**
     * @throws BadRequestException
     */
    public function render()
    {
        $defaults = array();

        if (isset($this->id) && $this->editAllocation === false) {

            $project = $this->projectRepository->getProject($this->id);

            if(empty($project))
            {
                throw new BadRequestException();
            }

            $defaults['projectName'] = $project['name'];
        }

        if(isset($this->id) && $this->editAllocation === true)
        {
            $allocation = $this->allocationRepository->getAllocation($this->id);
            if(empty($allocation))
            {
                throw new BadRequestException();
            }

            $defaults = $allocation;
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
        $form->setAutoShowValidation(false);

        $form->addText('projectName', 'app.projectAllocation.name')
            ->addRule(FormAlias::REQUIRED, "app.baseForm.labelIsRequiredMasculine")
            ->addRule(FormAlias::MAX_LENGTH, "app.baseForm.labelCanBeOnlyLongMasculine",  255)
            ->getControlPrototype()->setAttribute('readonly','readonly');

        if($this->editAllocation)
        {
            $form->addText('userFullName', 'app.projectAllocation.user_id')
                ->addRule(FormAlias::REQUIRED, "app.baseForm.labelIsRequiredMasculine")
                ->getControlPrototype()->setAttribute('readonly','readonly');
        }
        else
        {
        $users = $this->projectUserRepository->getAllUsersInfoOnProject($this->id);
        bdump($users);
        $form->addSelect('user_id', 'app.projectAllocation.user_id', $users)
        ->setTranslator(null);
        }


       $form->addDate('from', 'app.projectAllocation.from')
            ->addRule(FormAlias::REQUIRED, "app.baseForm.labelIsRequiredMasculine");
;
        $form->addDate('to', 'app.projectAllocation.to')
            ->addRule(FormAlias::REQUIRED, "app.baseForm.labelIsRequiredMasculine");
;
        $form->addInteger('allocation', 'app.projectAllocation.allocation')
        ->addRule(FormAlias::MIN, 'app.projectAllocation.allocationMin',0)
        ->addRule(FormAlias::MAX, 'app.projectAllocation.allocationMax',40);

        $form->addTextArea('description', 'app.projectAllocation.description');

        $states = Utils::getEnumValuesAsArray(EState::cases());
        $states = array_map(function ($state) {return $this->translator->translate('app.projectAllocation.'.$state);}, $states);
        $form->addSelect('state', 'app.projectAllocation.state', $states)
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


        $form->onValidate[] = [$this, 'validateForm'];
        $form->onSuccess[] = [$this, 'saveForm'];
        return $form;
    }

    public function validateForm(Form $form, ArrayHash $values)
    {
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

        if(empty($values['from']))
        {
            $values['from'] = null;
        }

        try {

            if($this->editAllocation)
            {
                $this->allocationFacade->editAllocation($values, $this->id);
                $this->presenter->flashMessage($this->translator->translate('app.baseForm.saveOK'), 'bg-success');
                $this->presenter->redirect("Project:");
            }
            else
            {
                $this->allocationFacade->createAllocation($values, $this->id);
                $this->presenter->flashMessage($this->translator->translate('app.baseForm.saveOK'), 'bg-success');
                $this->presenter->redirect("Project:detail",$this->id);
            }

        } catch (ProcessException $e) {
            $form->addError($e->getMessage());
        }

    }
}

interface IProjectUserAllocationFormFactory
{
    public function create(?int $id, bool $editAllocation = false): ProjectUserAllocationForm;
}