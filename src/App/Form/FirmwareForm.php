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

class FirmwareForm extends FormBuilder {
	
	public function build() : void {
		
		$this->add('name','text',[
			'label' => 'Name',
			'rules' => ['required']
		]);
		$this->add('version','number',[
			'label' => 'Version',
			'attributes' => ['step'=>'0.01'],
			'rules' => ['required']
		]);
		$this->add('firmware','file',[
			'label' => 'Firmware',
			'attributes' => ['accept'=>'.bin']
		]);

		$this->add('module_id','hidden',[]);

		$this->submit('<i class="icon-save"></i> SAVE');
	}
}
