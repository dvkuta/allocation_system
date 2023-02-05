<?php

namespace App\Components\Project\ProjectUserAllocationForm;

use App\Components\Base\BaseComponent;
use App\Model\Exceptions\ProcessException;
use App\Model\Project\ProjectFacade;
use App\Model\Project\ProjectUser\ProjectUserFacade;
use App\Model\Project\ProjectUserAllocation\EState;
use App\Model\Project\ProjectUserAllocation\ProjectUserAllocationFacade;
use App\Model\Repository\Domain\Allocation;
use App\Model\Repository\Domain\Project;
use App\Tools\Utils;
use Contributte\FormsBootstrap\BootstrapForm;
use Contributte\FormsBootstrap\BootstrapRenderer;
use Contributte\FormsBootstrap\Enums\RenderMode;
use Nette\Application\AbortException;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Nette\Application\UI\InvalidLinkException;
use Nette\Forms\Form as FormAlias;
use Nette\Localization\Translator;
use Nette\Utils\ArrayHash;

/**
 * Komponenta pro vykreslení formuláře pro vytváření/editaci alokací
 */
class ProjectUserAllocationForm extends BaseComponent
{

    private ?int $id; //při editaci je to id alokace, a při vytváření je to id projektu
    private bool $editAllocation; //jde o editaci
    private Translator $translator;
    private ProjectUserAllocationFacade $allocationFacade;
    private ProjectFacade $projectFacade;
    private ProjectUserFacade $projectUserFacade;


    /**
     * @param int|null $id
     * @param bool $editAllocation
     * @param Translator $translator
     * @param ProjectUserAllocationFacade $allocationFacade
     * @param ProjectFacade $projectFacade
     * @param ProjectUserFacade $projectUserFacade
     */
    public function __construct(
        ?int                            $id,
        bool                            $editAllocation,
        Translator                      $translator,
        ProjectUserAllocationFacade     $allocationFacade,
        ProjectFacade $projectFacade,
        ProjectUserFacade $projectUserFacade
    )
    {

        $this->id = $id;
        $this->editAllocation = $editAllocation;
        $this->translator = $translator;
        $this->allocationFacade = $allocationFacade;
        $this->projectFacade = $projectFacade;
        $this->projectUserFacade = $projectUserFacade;
    }

    /**
     * @throws BadRequestException pokud při editaci alokace nebo při vytváření projekt neexistuje
     */
    public function render()
    {
        $defaults = array();

        if (isset($this->id) && $this->editAllocation === false) {

            /** @var Project $project */
            $project = $this->projectFacade->getProject($this->id);

            if($project === null)
            {
                throw new BadRequestException();
            }

            $defaults['projectName'] = $project->getName();
        }

        if(isset($this->id) && $this->editAllocation === true)
        {
            /** @var Allocation $allocation */
            $allocation = $this->allocationFacade->getAllocation($this->id);
            if($allocation === null)
            {
                throw new BadRequestException();
            }

            $defaults = [
                'projectName' => $allocation->getCurrentProjectName() ?? $this->translator->translate('app.baseForm.wantedRecordNotFound'),
                'userFullName' => $allocation->getCurrentWorkerFullName() ?? $this->translator->translate('app.baseForm.wantedRecordNotFound'),
                'from' => $allocation->getFrom(),
                'to' => $allocation->getTo(),
                'allocation' => $allocation->getAllocation(),
                'description' => $allocation->getDescription(),
                'state' => $allocation->getState()->value
            ];
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
     * @throws InvalidLinkException
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
        $users = $this->projectUserFacade->getAllUsersInfoOnProject($this->id);

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
            ->addRule(FormAlias::REQUIRED, "app.baseForm.labelIsRequiredMasculine")
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

    /**
     * validace formuláře
     * @param Form $form
     * @param ArrayHash $values
     * @return void
     */
    public function validateForm(Form $form, ArrayHash $values)
    {
        if(!empty($values['to']))
        {
            if($values['from'] > $values['to']) {
                $form->addError("app.projectAllocation.dateError");
            }
        }
    }


    /**
     * Funkce, která je zavolá, pokud jsou ve formuláři validní data
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
                $this->allocationFacade->editAllocation(
                    $this->id,
                    $values['allocation'],
                    $values['from'],
                    $values['to'],
                    $values['description'],
                    EState::from($values['state']));
                $this->presenter->flashMessage($this->translator->translate('app.baseForm.saveOK'), 'bg-success');
                $this->presenter->redirect("Project:");
            }
            else
            {
                $this->allocationFacade->createAllocation(
                    $values['allocation'],
                    $values['from'],
                    $values['to'],
                    $values['description'],
                    EState::from($values['state']), $this->id, $values['user_id']);
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