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

use SkankyDev\Model\Document\EmbeddedDocument;

/**
 * Un message d'une conversation, embarqué dans Conversation.messages.
 * Stocke le strict nécessaire au format de l'API chat (role + content) ;
 * le system prompt n'est PAS persisté ici (il vient du Persona à l'envoi).
 */
class Message extends EmbeddedDocument {

	public string $role = '';
	public string $content = '';

	public static function user(string $content): static {
		return new static(['role' => 'user', 'content' => $content]);
	}

	public static function assistant(string $content): static {
		return new static(['role' => 'assistant', 'content' => $content]);
	}

	public static function system(string $content): static {
		return new static(['role' => 'system', 'content' => $content]);
	}

	/**
	 * Représentation pour l'API chat (compatible OpenAI/llama-server).
	 */
	public function toApi(): array {
		return ['role' => $this->role, 'content' => $this->content];
	}

}
