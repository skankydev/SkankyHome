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
use App\Model\Document\Persona;

class PersonaCollection extends MasterCollection {

	use Singleton;

	protected string $collectionName = 'personas';
	protected string $documentClass = Persona::class;

	public function getDisplayField(): array {
		return [
			'name'       => ['label' => 'Name',    'sort' => true],
			'created_at' => ['label' => 'created', 'sort' => true],
			'updated_at' => ['label' => 'updated', 'sort' => true],
		];
	}

	public function widgetLink(object $document): array {
		return [
			'controller' => 'persona',
			'action'     => 'show',
			'params'     => ['persona' => $document->_id],
		];
	}

}