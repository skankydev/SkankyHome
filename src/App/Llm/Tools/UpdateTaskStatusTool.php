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

use App\Model\Enum\TaskStatus;
use App\Model\TaskCollection;

/**
 * Change le statut d'une tâche (écriture).
 */
class UpdateTaskStatusTool extends MasterTool {

	public function name(): string {
		return 'update_task_status';
	}

	public function description(): string {
		return "Change le statut d'une tâche, identifiée par son task_id (obtenu via list_tasks).";
	}

	public function parameters(): array {
		return [
			'type'       => 'object',
			'properties' => [
				'task_id' => ['type' => 'string', 'description' => "Identifiant de la tâche."],
				'status'  => [
					'type'        => 'string',
					'enum'        => array_keys(TaskStatus::options()),
					'description' => "Nouveau statut : todo, doing, done ou blocked.",
				],
			],
			'required'   => ['task_id', 'status'],
		];
	}

	public function execute(array $args): mixed {
		$taskId = trim($args['task_id'] ?? '');
		$status = TaskStatus::tryFrom($args['status'] ?? '');

		if ($taskId === '') {
			return ['error' => 'task_id est requis.'];
		}
		if ($status === null) {
			return ['error' => 'Statut invalide. Valeurs acceptées : ' . implode(', ', array_keys(TaskStatus::options()))];
		}

		$task = TaskCollection::_findById($taskId);
		if ($task === null) {
			return ['error' => "Tâche introuvable : {$taskId}"];
		}

		$task->status = $status;
		TaskCollection::_save($task);

		return ['success' => true, 'id' => (string) $task->_id, 'status' => $status->value];
	}

}
