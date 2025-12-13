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

use SkankyDev\Model\MasterCollection;
use App\Model\Document\Sequence;

class SequenceCollection extends MasterCollection {

	protected string $collectionName = 'sequences';
	protected string $documentClass = Sequence::class;

	
	public function findActive(): array {
		return $this->find(['active' => true]);
	}

}