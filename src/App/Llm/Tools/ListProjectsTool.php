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
use App\Model\ProjectCollection;

/**
 * Liste les projets de la gestion de projet (lecture seule).
 */
class ListProjectsTool extends MasterTool {

	public function name(): string {
		return 'list_projects';
	}

	public function description(): string {
		return 'Liste tous les projets de gestion de projet, avec leur identifiant, nom, description et statut.';
	}

	public function parameters(): array {
		return ['type' => 'object', 'properties' => (object) []];
	}

	public function execute(array $args): mixed {
		$projects = ProjectCollection::_find([]);

		return array_map(fn(Project $p) => [
			'id'          => (string) $p->_id,
			'name'        => $p->name,
			'description' => $p->description,
			'status'      => $p->status->value,
		], $projects);
	}

}
