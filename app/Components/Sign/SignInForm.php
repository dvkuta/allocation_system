<?php

namespace App\Components\Sign;

use App\Components\Base\BaseComponent;
use Contributte\FormsBootstrap\BootstrapForm;
use Contributte\FormsBootstrap\BootstrapRenderer;
use Contributte\FormsBootstrap\Enums\RenderMode;
use Nette\Application\UI\Form;
use Nette\Security\AuthenticationException;
use Nette\Security\User;
use Nette\Utils\ArrayHash;

/**
 * Form component class for sign in proccess
 * @package App\Components
 */
class SignInForm extends BaseComponent {

	protected User $user;

	/**
	 * Component constructor
	 * @param User $user
	 */
	public function __construct(User $user) {
		$this->user = $user;
	}


	/**
	 * Factory function for creating sign in form
	 * @return Form
	 */
	public function createComponentForm():Form {

		$form = new BootstrapForm();
        $form->setRenderer(new BootstrapRenderer(RenderMode::SIDE_BY_SIDE_MODE));
		$form->addText('login', 'Login:')
            ->setPlaceholder("Orion login")
            ->setRequired('Prosím vyplňte své uživatelské jméno.');
		$form->addPassword('password', 'Heslo:')
            ->setRequired('Prosím vyplňte své heslo.');
        $parentRow = $form->addRow();
        $parentRow->addCell(6);
        $submitCell = $parentRow->addCell(6)
            ->addHtmlClass('inline-buttons');
		$form->addSubmit('submit', 'Přihlásit');


		//$form->onValidate[] = [$this, 'validateForm'];
		$form->onSuccess[] = [$this, 'saveForm'];
		return $form;
	}

	/**
	 * Function that is triggered by a successful form submission
	 * @param Form $form
	 * @param ArrayHash $values
	 */
	public function saveForm(Form $form, ArrayHash $values) {
		try {
//            $form->addError("error voe");
			//TODO base logger manager pro komponenty
			// $this->loggerManager->get('default')->info("Login try", ["login" => $values->username]);
			//$this->user->login($values->username, $values->password);
		} catch (AuthenticationException $e) {
			//$this->loggerManager->get('default')->notice("Login fail, wrong password", ["login" => $values->username]);
			//$form->addError('Nesprávné přihlašovací jméno nebo heslo.');
		}
	}
}

interface ISignInFormFactory
{
    public function create() :SignInForm;
}
