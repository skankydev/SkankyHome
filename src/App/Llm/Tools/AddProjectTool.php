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

use App\Model\Document\Project;
use App\Model\Enum\ProjectStatus;
use App\Model\ProjectCollection;

/**
 * Crée un nouveau projet (écriture).
 */
class AddProjectTool extends MasterTool {

	public function name(): string {
		return 'add_project';
	}

	public function description(): string {
		return "Crée un nouveau projet de gestion de projet.";
	}

	public function parameters(): array {
		return [
			'type'       => 'object',
			'properties' => [
				'name'        => ['type' => 'string', 'description' => "Nom du projet."],
				'description' => ['type' => 'string', 'description' => "Description optionnelle du projet."],
				'status'      => [
					'type'        => 'string',
					'enum'        => array_keys(ProjectStatus::options()),
					'description' => "Statut optionnel (défaut : active). Valeurs : active, paused, archived.",
				],
			],
			'required'   => ['name'],
		];
	}

	public function execute(array $args): mixed {
		$name = trim($args['name'] ?? '');
		if ($name === '') {
			return ['error' => 'name est requis.'];
		}

		$data = ['name' => $name, 'description' => trim($args['description'] ?? '')];

		if (!empty($args['status'])) {
			if (ProjectStatus::tryFrom($args['status']) === null) {
				return ['error' => 'Statut invalide. Valeurs acceptées : ' . implode(', ', array_keys(ProjectStatus::options()))];
			}
			$data['status'] = $args['status'];
		}

		$project = new Project($data);
		ProjectCollection::_save($project);

		return ['success' => true, 'id' => (string) $project->_id, 'name' => $project->name];
	}

}
