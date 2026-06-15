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

use App\Model\Document\Module;
use SkankyDev\Model\MasterCollection;
use SkankyDev\Utilities\Traits\Singleton;

class ModuleCollection extends MasterCollection {

	use Singleton;

	protected string $collectionName = 'modules';
	protected string $documentClass = Module::class;

	public function getDisplayField(): array {
		return [
			'name'          => ['label' => 'Name',          'sort' => true],
			'topic_message' => ['label' => 'Topic message', 'sort' => true],
			'topic_cmd'     => ['label' => 'Topic cmd',     'sort' => true],
			'nb_line'       => ['label' => 'Nb line',       'sort' => true],
			'nb_led'        => ['label' => 'Nb led',        'sort' => true],
			'created_at'    => ['label' => 'created',       'sort' => true],
			'updated_at'    => ['label' => 'updated',       'sort' => true],
		];
	}

}