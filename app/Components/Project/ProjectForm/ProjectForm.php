<?php

namespace App\Components\Project\ProjectForm;

use App\Components\Base\BaseComponent;
use App\Model\Domain\Project;
use App\Model\DTO\ProjectDTO;
use App\Model\Exceptions\ProcessException;
use App\Model\Project\ProjectFacade;
use App\Model\Repository\Base\IProjectRepository;
use App\Model\Repository\Base\IUserRoleRepository;
use App\Model\User\Role\ERole;
use App\Model\User\UserFacade;
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
 * Komponenta formuláře pro editaci a vytváření projektů
 *
 */
class ProjectForm extends BaseComponent
{
    /**
     * @var int|null Id projektu - je vyplnene pri editaci
     */
    private ?int $id;

    private Translator $translator;

    private ProjectFacade $projectFacade;
    private UserFacade $userFacade;


    public function __construct(
        ?int               $id,
        Translator         $translator,
        ProjectFacade      $projectFacade,
        UserFacade $userFacade
    )
    {

        $this->id = $id;
        $this->translator = $translator;
        $this->projectFacade = $projectFacade;
        $this->userFacade = $userFacade;
    }

    /**
     * Vykreslení komponenty a naplnení defaultními daty při editaci
     * @return void
     * @throws BadRequestException pokud projekt neexistuje
     */
    public function render()
    {
        $defaults = array();

        if (isset($this->id)) {
            /** @var Project $project */
            $project = $this->projectFacade->getProject($this->id);

            if ($project) {
                $defaults = [
                    'name' => $project->getName(),
                    'user_id' => $project->getProjectManagerId(),
                    'from' => $project->getFrom(),
                    'to' => $project->getTo(),
                    'description' => $project->getDescription()
                ];

            } else {
                $this->error("Project not exists",404);
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
     * @throws InvalidLinkException
     */
    public function createComponentForm(): Form
    {
        $form = new BootstrapForm();
        $form->setTranslator($this->translator);
        $form->setRenderer(new BootstrapRenderer(RenderMode::SIDE_BY_SIDE_MODE));
        $form->setAutoShowValidation(false);


        $form->addText('name', 'app.project.name')
            ->addRule(FormAlias::REQUIRED, "app.baseForm.labelIsRequiredMasculine")
            ->addRule(FormAlias::MAX_LENGTH, "app.baseForm.labelCanBeOnlyLongMasculine",  255);

        $projectManagers = $this->userFacade->getAllUsersInRole(ERole::project_manager);
        $form->addSelect('user_id', 'app.project.user_id', $projectManagers)
        ->setTranslator(null);

        $form->addDate('from', 'app.project.from')
            ->addRule(FormAlias::REQUIRED, "app.baseForm.labelIsRequiredMasculine");

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


        $form->onValidate[] = [$this, 'validateForm'];
        $form->onSuccess[] = [$this, 'saveForm'];
        return $form;
    }

    /** Validace zadaných dat
     * @param Form $form
     * @param ArrayHash $values
     * @return void
     */
    public function validateForm(Form $form, ArrayHash $values)
    {

        if(!empty($values['to']))
        {
            if($values['from'] > $values['to']) {
                $form->addError("app.project.dateError");
            }
        }
    }


    /**
     * Uložení dat z formuláře
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

            $project = new Project($this->id, $values['name'],
                $values['user_id'],"", $values['from'],
                $values['to'], $values['description']);


            //vytvoreni uzivatele
            $this->projectFacade->saveProject($project);
            $this->presenter->flashMessage($this->translator->translate('app.baseForm.saveOK'), 'bg-success');
            $this->presenter->redirect("Project:");
        } catch (ProcessException $e) {
            $form->addError($e->getMessage());
        }

    }
}

interface IProjectFormFactory
{
    public function create(?int $id): ProjectForm;
}