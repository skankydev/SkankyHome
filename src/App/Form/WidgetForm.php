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

use App\Model\ModuleCollection;
use App\Model\PersonaCollection;
use SkankyDev\Form\FormBuilder;

class WidgetForm extends FormBuilder {
	
	public function build() : void {
		
		$this->add('target_collection','select',[
			'label' => 'Target Collection',
			'rules' => ['required'],
			'options' => [
				'' => '--- Empty ---',
				ModuleCollection::class => 'Module',
				PersonaCollection::class => 'Persona',
			],
		]);
		$this->add('target_id','text',[
			'label' => 'Target Id',
			'rules' => ['required']
		]);
		$this->add('position','number',[
			'label' => 'Position',
			'rules' => ['required']
		]);

		$this->submit('<i class="icon-save"></i> SAVE');
	}
}
