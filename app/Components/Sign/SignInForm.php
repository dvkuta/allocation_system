<?php

namespace App\Components\Sign;

use App\Components\Base\BaseComponent;
use Contributte\FormsBootstrap\BootstrapForm;
use Contributte\FormsBootstrap\BootstrapRenderer;
use Contributte\FormsBootstrap\Enums\RenderMode;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Forms\Form as FormAlias;
use Nette\Localization\Translator;
use Nette\Security\AuthenticationException;
use Nette\Security\User;
use Nette\Utils\ArrayHash;

/**
 * Přihlašovací formulář
 */
class SignInForm extends BaseComponent {

	protected User $user;
    private Translator $translator;


	public function __construct(User $user, Translator $translator) {
		$this->user = $user;
        $this->translator = $translator;
    }


	/**
	 * Definice formuláře
	 */
	public function createComponentForm():Form {

		$form = new BootstrapForm();
        $form->setRenderer(new BootstrapRenderer(RenderMode::SIDE_BY_SIDE_MODE));
        $form->setTranslator($this->translator);
        $form->setAutoShowValidation(false);

        $form->addText('login', 'app.user.login')
            ->setPlaceholder("Orion login")
            ->addRule(FormAlias::REQUIRED, "app.baseForm.labelIsRequiredMasculine")
            ->addRule(FormAlias::MAX_LENGTH, "app.baseForm.labelCanBeOnlyLongMasculine",  100);

        $form->addPassword('password', 'app.user.password')
            ->addRule(FormAlias::REQUIRED, "app.baseForm.labelIsRequiredMasculine");

        $parentRow = $form->addRow();
        $parentRow->addCell(6);
        $submitCell = $parentRow->addCell(6)
            ->addHtmlClass('inline-buttons');
		$form->addSubmit('submit', 'app.user.loginAction');


		//$form->onValidate[] = [$this, 'validateForm'];
		$form->onSuccess[] = [$this, 'saveForm'];
		return $form;
	}

    /**
     * Funkce zavolaná po úspěšném odeslání formuláře
     * @param Form $form
     * @param ArrayHash $values
     * @throws AbortException
     */
	public function saveForm(Form $form, ArrayHash $values) {
		try {

			$this->user->login($values->login, $values->password);
            $this->presenter->redirect("Homepage:");

		} catch (AuthenticationException $e) {
			$form->addError($e->getMessage());
		}
	}
}

interface ISignInFormFactory
{
    public function create() :SignInForm;
}
