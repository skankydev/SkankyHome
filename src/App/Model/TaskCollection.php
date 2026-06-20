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

namespace App\Model;

use SkankyDev\Utilities\Traits\Singleton;
use SkankyDev\Model\MasterCollection;
use App\Model\Document\Task;

class TaskCollection extends MasterCollection {

	use Singleton;

	protected string $collectionName = 'tasks';
	protected string $documentClass = Task::class;

	public function getDisplayField(): array {
		return [
			'project_id' => [
				'label'  => 'Projet',
				'sort'   => true,
				'render' => fn($task) => e($task->project?->name ?? '—'),
			],
			'name' => ['label' => 'Name', 'sort' => true],
			'status' => ['label' => 'Status', 'sort' => true],
			'created_at' => ['label' => 'Created', 'sort' => true],
			'updated_at' => ['label' => 'Updated', 'sort' => true],
		];
	}

}