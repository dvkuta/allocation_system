<?php

namespace App\Components\FormComponents\Sign;

use App\Components\FormComponents\BootstrapForm;
use Nette\Application\UI\Form;
use Nette\Security\AuthenticationException;
use Nette\Security\User;
use Nette\Utils\ArrayHash;

/**
 * Form component class for sign in proccess
 * @package App\Components
 */
class SignInForm extends Form {
	use BootstrapForm;

	protected User $user;

	/**
	 * Component constructor
	 * @param User $user
	 */
	public function __construct(User $user) {
		$this->user = $user;
		parent::__construct();
	}


	/**
	 * Factory function for creating sign in form
	 * @return Form
	 */
	public function create():Form {

		$form = new Form();
		$form->addText('username', 'Uživatelské jméno:')->setRequired('Prosím vyplňte své uživatelské jméno.');
		$form->addPassword('password', 'Heslo:')->setRequired('Prosím vyplňte své heslo.');
		$form->addSubmit('send', 'Přihlásit');


		//$form->onValidate[] = [$this, 'validateForm'];
		$form->onSuccess[] = [$this, 'saveForm'];
		$this->makeBootstrap4($form);
		return $form;
	}

	/**
	 * Function that is triggered by a successful form submission
	 * @param Form $form
	 * @param ArrayHash $values
	 */
	public function saveForm(Form $form, ArrayHash $values) {
		try {

			$form->addError("Error");
			//TODO base logger manager pro komponenty
			// $this->loggerManager->get('default')->info("Login try", ["login" => $values->username]);
			//$this->user->login($values->username, $values->password);
		} catch (AuthenticationException $e) {
			//$this->loggerManager->get('default')->notice("Login fail, wrong password", ["login" => $values->username]);
			//$form->addError('Nesprávné přihlašovací jméno nebo heslo.');
		}
	}
}
