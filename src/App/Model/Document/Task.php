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

namespace App\Model\Document;

use App\Model\Enum\TaskStatus;
use App\Model\ProjectCollection;
use MongoDB\BSON\ObjectId;
use SkankyDev\Model\Document\MasterDocument;
use SkankyDev\Model\Document\Traits\TimedTrait;

class Task extends MasterDocument {

	use TimedTrait;

	public ObjectId $project_id;
	public string $name = '';
	public string $icon = '';
	public string $description = '';
	public TaskStatus $status = TaskStatus::TODO;

	/**
	 * Projet parent, résolu à la volée via le magic __get ($task->project).
	 */
	public function getProject(): ?Project {
		if (!isset($this->project_id)) {
			return null;
		}
		return ProjectCollection::_findById((string) $this->project_id);
	}

}