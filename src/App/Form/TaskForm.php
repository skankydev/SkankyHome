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

use App\Model\Enum\TaskStatus;
use App\Model\ProjectCollection;
use SkankyDev\Form\FormBuilder;

class TaskForm extends FormBuilder {

	public function build() : void {

		$this->add('project_id','select',[
			'label' => 'Projet',
			'rules' => ['required'],
			'empty' => '--- Choisir ---',
			'options' => $this->projectOptions(),
		]);
		$this->add('name','text',[
			'label' => 'Name',
			'rules' => ['required']
		]);
		$this->add('icon','icon',[
			'label' => 'Icon',
			'rules' => []
		]);
		$this->add('description','textarea',[
			'label' => 'Description',
			'rules' => []
		]);
		$this->add('status','select',[
			'label' => 'Status',
			'rules' => ['required'],
			'options' => TaskStatus::options(),
		]);

		$this->submit('<i class="icon-save"></i> SAVE');
	}

	/**
	 * id (string) => nom, pour le select du projet parent.
	 */
	private function projectOptions(): array {
		$options = [];
		foreach (ProjectCollection::_find() as $project) {
			$options[(string) $project->_id] = $project->name;
		}
		return $options;
	}
}
