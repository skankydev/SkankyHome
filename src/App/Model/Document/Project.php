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

use App\Model\Enum\ProjectStatus;
use SkankyDev\Model\Document\MasterDocument;
use SkankyDev\Model\Document\Traits\TimedTrait;

class Project extends MasterDocument {

	use TimedTrait;

	public string $icon = '';
	public string $name = '';
	public string $description = '';
	public ProjectStatus $status = ProjectStatus::ACTIVE;

}