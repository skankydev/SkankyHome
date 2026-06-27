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
 * Liste les tâches d'un projet donné (lecture seule).
 */
class ListTasksTool extends MasterTool {

	public function name(): string {
		return 'list_tasks';
	}

	public function description(): string {
		return "Liste les tâches d'un projet, identifié par son project_id (obtenu via list_projects).";
	}

	public function parameters(): array {
		return [
			'type'       => 'object',
			'properties' => [
				'project_id' => ['type' => 'string', 'description' => "Identifiant du projet."],
			],
			'required'   => ['project_id'],
		];
	}

	public function execute(array $args): mixed {
		$projectId = trim($args['project_id'] ?? '');
		if ($projectId === '') {
			return ['error' => 'project_id est requis.'];
		}

		$project = ProjectCollection::_findById($projectId);
		if ($project === null) {
			return ['error' => "Projet introuvable : {$projectId}"];
		}

		$tasks = TaskCollection::_find(['project_id' => $project->_id]);

		return array_map(fn(Task $t) => [
			'id'          => (string) $t->_id,
			'name'        => $t->name,
			'description' => $t->description,
			'status'      => $t->status->value,
		], $tasks);
	}

}
