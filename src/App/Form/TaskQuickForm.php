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
use SkankyDev\Form\FormBuilder;

/**
 * Formulaire d'ajout rapide d'une tâche depuis la page d'un projet.
 * Pas de champ project_id : le projet vient de l'URL (ProjectController::addTask).
 */
class TaskQuickForm extends FormBuilder {

	public function build() : void {

		$this->add('name','text',[
			'label' => 'Nom de la tâche',
			'rules' => ['required'],
		]);
		$this->add('status','select',[
			'label' => 'Statut',
			'rules' => ['required'],
			'options' => TaskStatus::options(),
		]);
		$this->add('description','textarea',[
			'label' => 'Description',
			'rules' => [],
		]);

		$this->submit('<i class="icon-add"></i> Ajouter la tâche');
	}
}
