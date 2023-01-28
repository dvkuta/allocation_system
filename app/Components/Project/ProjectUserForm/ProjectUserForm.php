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
    private Translator $translator;

    private ProjectRepository $projectRepository;
    private ProjectUserRepository $projectUserRepository;
    private ProjectUserFacade $projectUserFacade;


    public function __construct(
        ?int                  $id,
        Translator            $translator,
        ProjectRepository $projectRepository,
        ProjectUserRepository $projectUserRepository,
        ProjectUserFacade $projectUserFacade
    )
    {

        $this->id = $id;
        $this->translator = $translator;
        $this->projectRepository = $projectRepository;
        $this->projectUserRepository = $projectUserRepository;
        $this->projectUserFacade = $projectUserFacade;
    }

    public function render()
    {
        $defaults = array();

        if (isset($this->id)) {
            $row = $this->projectRepository->findRow($this->id);
            if ($row) {
                //TODO REPOSITORY
                $defaults['name'] = $row->name;
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

        $form->addText('name', 'app.projectAllocation.name')
            ->addRule(FormAlias::REQUIRED, "app.baseForm.labelIsRequiredMasculine")
            ->addRule(FormAlias::MAX_LENGTH, "app.baseForm.labelCanBeOnlyLongMasculine",  255)
            ->getControlPrototype()->setAttribute('readonly','readonly');

        $users = $this->projectUserRepository->getAllUsersThatDoesNotWorkOnProject($this->id);
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

            $this->projectUserFacade->saveUserToProject($values, $this->id);

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