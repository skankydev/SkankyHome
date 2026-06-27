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

namespace App\Llm\Tools;

use App\Model\Document\Task;
use App\Model\ProjectCollection;
use App\Model\TaskCollection;

/**
 * Ajoute une tâche à un projet (écriture).
 */
class AddTaskTool extends MasterTool {

	public function name(): string {
		return 'add_task';
	}

	public function description(): string {
		return "Crée une nouvelle tâche dans un projet, identifié par son project_id (obtenu via list_projects).";
	}

	public function parameters(): array {
		return [
			'type'       => 'object',
			'properties' => [
				'project_id'  => ['type' => 'string', 'description' => "Identifiant du projet parent."],
				'name'        => ['type' => 'string', 'description' => "Nom de la tâche."],
				'description' => ['type' => 'string', 'description' => "Description optionnelle de la tâche."],
			],
			'required'   => ['project_id', 'name'],
		];
	}

	public function execute(array $args): mixed {
		$projectId = trim($args['project_id'] ?? '');
		$name      = trim($args['name'] ?? '');

		if ($projectId === '') {
			return ['error' => 'project_id est requis.'];
		}
		if ($name === '') {
			return ['error' => 'name est requis.'];
		}

		$project = ProjectCollection::_findById($projectId);
		if ($project === null) {
			return ['error' => "Projet introuvable : {$projectId}"];
		}

		$task = new Task([
			'project_id'  => (string) $project->_id,
			'name'        => $name,
			'description' => trim($args['description'] ?? ''),
		]);
		TaskCollection::_save($task);

		return ['success' => true, 'id' => (string) $task->_id, 'name' => $task->name];
	}

}
