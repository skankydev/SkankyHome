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

namespace SkankyDev\Model\Document;

use SkankyDev\Model\Document\MasterDocument;
use DateTime;

class JobDoc extends MasterDocument {
	
	public object $payload;
	public string $status = 'pending';
	public string $error;
	public int $attempts = 0;
	public int $max_attempts = 3;
	public ?DateTime $started_at = null;
	public ?DateTime $completed_at = null;

}