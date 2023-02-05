<?php

namespace App\Components\Project\ProjectUserForm;

use App\Components\Base\BaseComponent;
use App\Model\Exceptions\ProcessException;
use App\Model\Project\ProjectFacade;
use App\Model\Project\ProjectUser\ProjectUserFacade;
use App\Model\Repository\Domain\Project;
use App\Model\Repository\Domain\ProjectUser;
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
 * Formulář pro přiřazení uživatelů k projektu
 */
class ProjectUserForm extends BaseComponent
{
    //id projektu
    private ?int $id;
    private Translator $translator;
    private ProjectUserFacade $projectUserFacade;
    private ProjectFacade $projectFacade;


    public function __construct(
        ?int                  $id,
        Translator            $translator,
        ProjectUserFacade     $projectUserFacade,
        ProjectFacade $projectFacade
    )
    {

        $this->id = $id;
        $this->translator = $translator;
        $this->projectUserFacade = $projectUserFacade;
        $this->projectFacade = $projectFacade;
    }

    public function render()
    {
        $defaults = array();

        if (isset($this->id)) {

            /** @var Project $project */
            $project = $this->projectFacade->getProject($this->id);
            if ($project) {
                $defaults['name'] = $project->getName();
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

        $form->addText('name', 'app.projectAllocation.name')
            ->addRule(FormAlias::REQUIRED, "app.baseForm.labelIsRequiredMasculine")
            ->addRule(FormAlias::MAX_LENGTH, "app.baseForm.labelCanBeOnlyLongMasculine",  255)
            ->getControlPrototype()->setAttribute('readonly','readonly');

        $users = $this->projectUserFacade->getAllUsersThatDoesNotWorkOnProject($this->id);

        $form->addSelect('user_id', 'app.projectAllocation.user_id', $users)
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
     * Funkce, která se vykoná pokud je odeslání formuláře validní
     * @param Form $form
     * @param ArrayHash $values
     * @throws AbortException
     */
    public function saveForm(Form $form, ArrayHash $values)
    {

        try {
            $this->projectUserFacade->saveUserToProject($values['user_id'], $this->id);

            $this->presenter->flashMessage($this->translator->translate('app.baseForm.saveOK'), 'bg-success');
            $this->presenter->redirect("Project:");
        } catch (ProcessException $e) {
            $form->addError($e->getMessage());
        }

    }
}

interface IProjectUserFormFactory
{
    public function create(?int $id): ProjectUserForm;
}