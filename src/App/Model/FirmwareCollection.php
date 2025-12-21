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
use App\Model\Document\Firmware;

class FirmwareCollection extends MasterCollection {

	use Singleton;

	protected string $collectionName = 'firmwares';
	protected string $documentClass = Firmware::class;
	
}