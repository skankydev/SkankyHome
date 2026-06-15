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
use App\Model\Document\Widget;

class WidgetCollection extends MasterCollection {

	use Singleton;

	protected string $collectionName = 'widgets';
	protected string $documentClass = Widget::class;

	public function getDisplayField(): array {
		return [
			'target_collection' => ['label' => 'Target Collection', 'sort' => true],
			'target_id' => ['label' => 'Target Id', 'sort' => true],
			'position' => ['label' => 'Position', 'sort' => true],
			'created_at' => ['label' => 'Created', 'sort' => true],
			'updated_at' => ['label' => 'Updated', 'sort' => true],
		];
	}

}