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

class ModuleForm extends FormBuilder {
	
	public function build() : void {
		
		$this->add('icon','icon',[
			'label' => 'Icon',
			'rules' => ['required']
		]);

		$this->add('name','text',[
			'label' => 'Name',
			'rules' => ['required']
		]);
		$this->add('topic_message','text',[
			'label' => 'Topic message',
			'rules' => ['required']
		]);
		$this->add('topic_cmd','text',[
			'label' => 'Topic cmd',
			'rules' => ['required']
		]);
		$this->add('nb_line','number',[
			'label' => 'Nb line',
			'rules' => ['required','max:5']
		]);
		$this->add('nb_led','number',[
			'label' => 'Nb led',
			'rules' => ['required']
		]);

		$this->submit('<i class="icon-save"></i> SAVE');
	}
}
