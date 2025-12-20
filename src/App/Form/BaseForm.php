<?php
/**
 * Copyright (c) 2025 SCHENCK Simon
 * 
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 * @copyright     Copyright (c) SCHENCK Simon
 *
 */

namespace App\Form;

use SkankyDev\Form\FormBuilder;

class BaseForm extends FormBuilder {
	
	public function build() : void {
		
		$this->add('text','text',[
			'label' => 'text',
		]);

		$this->add('textarea','textarea',[
			'label' => 'textarea',
		]);

		$this->add('number','number',[
			'label' => 'number',
		]);

		$this->add('select','select',[
			'label' => 'select',
			'options' => ['fade' => 'Fade', 'blink' => 'Blink'],
			'empty' => '',
		]);

		$this->add('checkbox','checkbox',[
			'label' => 'checkbox',
		]);

		$this->add('email','email',[
			'label' => 'email',
		]);

		$this->add('radio','radio',[
			'label' => 'radio',
			'options' => ['fade' => 'Fade', 'blink' => 'Blink'],
		]);

		$this->add('password','password',[
			'label' => 'password',
		]);


		$this->submit('<i class="icon-save"></i> SAVE');
	}
}

