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
use App\Model\Document\Conversation;

class ConversationCollection extends MasterCollection {

	use Singleton;

	protected string $collectionName = 'conversations';
	protected string $documentClass = Conversation::class;

	public function getDisplayField(): array {
		return [
			'name' => ['label' => 'Name', 'sort' => true],
			'persona_id' => [
				'label'  => 'Persona',
				'sort'   => true,
				'render' => fn($conversation) => e($conversation->persona?->name ?? '—'),
			],
			'created_at' => ['label' => 'Created', 'sort' => true],
			'updated_at' => ['label' => 'Updated', 'sort' => true],
		];
	}

}