<?php

namespace App\Components\FormComponents;

use Nette;
use Nette\Application\UI\Form;

trait BootstrapForm {
	function makeBootstrap4(Form $form): void
	{
		$renderer = $form->getRenderer();
		$renderer->wrappers['controls']['container'] = null;
		$renderer->wrappers['pair']['container'] = 'div class="form-group row"';
		$renderer->wrappers['pair']['.error'] = 'has-danger';
		$renderer->wrappers['control']['container'] = 'div class=col-sm-9';
		$renderer->wrappers['label']['container'] = 'div class="col-sm-3 font-weight-bold col-form-label"';
		$renderer->wrappers['control']['description'] = 'span class=form-text';
		$renderer->wrappers['control']['errorcontainer'] = 'span class=form-control-feedback';
		$renderer->wrappers['control']['.error'] = 'is-invalid';

		foreach ($form->getControls() as $control) {
			$type = $control->getOption('type');
			if ($type === 'button') {
				$control->getControlPrototype()->addClass(empty($usedPrimary) ? 'btn btn-primary' : 'btn btn-secondary');
				$usedPrimary = true;

			} elseif (in_array($type, ['text', 'textarea', 'select'], true)) {
				$control->getControlPrototype()->addClass('form-control');

			} elseif ($type === 'file') {
				$control->getControlPrototype()->addClass('form-control-file');

			} elseif (in_array($type, ['checkbox', 'radio'], true)) {
				if ($control instanceof Nette\Forms\Controls\Checkbox) {
					$control->getLabelPrototype()->addClass('form-check-label');
				} else {
					$control->getItemLabelPrototype()->addClass('form-check-label');
				}
				$control->getControlPrototype()->addClass('form-check-input');
				$control->getSeparatorPrototype()->setName('div')->addClass('form-check');
			}
		}
	}
}
