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

class SequenceForm extends FormBuilder{

	public function build() : void {

		$this->add('name', 'text', [
			'label' => 'Nom de la séquence',
			'rules' => ['required', 'min_length:3']
		]);
		$this->add('color', 'color', [
			'label' => 'Couleur',
			'default' => '#ffffff'
		]);
		$this->add('duration', 'number', [
			'label' => 'Durée (ms)',
			'rules' => ['required', 'numeric', 'min:100']
		]);

		$this->add('effect', 'select', [
			'label' => 'Effet',
			'options' => ['fade' => 'Fade', 'blink' => 'Blink'],
			'empty' => '',
			'rules' => ['required']
		]);

		$this->submit('<i class="icon-save"></i> SAVE');
	}
}
