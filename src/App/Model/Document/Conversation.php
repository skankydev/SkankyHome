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

use App\Model\PersonaCollection;
use DateTime;
use MongoDB\BSON\ObjectId;
use SkankyDev\Model\Document\MasterDocument;
use SkankyDev\Model\Document\Traits\TimedTrait;

class Conversation extends MasterDocument {

	use TimedTrait;

	public string $name = '';
	public ObjectId $persona_id;

	/** @var Message[] messages embarqués (role/content), hors system prompt */
	public array $messages = [];

	/**
	 * Persona lié, résolu à la volée via le magic __get ($conversation->persona).
	 */
	public function getPersona(): ?Persona {
		if (!isset($this->persona_id)) {
			return null;
		}
		return PersonaCollection::_findById((string) $this->persona_id);
	}

	/**
	 * Ajoute un message au fil et le renvoie (chaînable côté appelant).
	 */
	public function addMessage(Message $message): Message {
		$this->messages[] = $message;
		return $message;
	}

	/**
	 * Construit le tableau de messages envoyé à llama : le system prompt du persona
	 * en tête (assemblé à la volée, jamais persisté), suivi des échanges.
	 *
	 * @return array<int, array{role: string, content: string}>
	 */
	public function toApiMessages(): array {
		$messages = [];
		$persona = $this->getPersona();
		if ($persona !== null && $persona->content !== '') {
			$messages[] = ['role' => 'system', 'content' => $persona->content];
		}
		foreach ($this->messages as $message) {
			$messages[] = $message->toApi();
		}
		return $messages;
	}

}
