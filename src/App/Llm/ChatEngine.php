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

namespace App\Llm;

use App\Model\Document\Conversation;
use App\Model\Document\Message;
use SkankyDev\Config\Config;
use SkankyDev\Utilities\HttpClient;

/**
 * Le « cerveau » : transforme une Conversation en une réponse de l'assistant.
 *
 * Volontairement découplé du transport : il ne sait pas qui l'appelle (chat HTTP
 * aujourd'hui, job MQTT demain) ni où part sa réponse. Il assemble le contexte,
 * interroge llama-server (sans stream) et ajoute la réponse à la conversation.
 * La persistance reste à l'appelant.
 */
class ChatEngine {

	private string $endpoint;
	private HttpClient $http;

	/** Paramètres d'inférence par défaut (à remonter dans le Persona plus tard). */
	private float $temperature = 0.7;
	private int $maxTokens = 1024;

	public function __construct(?HttpClient $http = null, ?string $baseUrl = null) {
		if ($baseUrl === null) {
			$conf = Config::get('llama');
			$baseUrl = 'http://' . $conf['host'] . ':' . $conf['port'];
		}
		$this->endpoint = rtrim($baseUrl, '/') . '/v1/chat/completions';
		$this->http = $http ?? new HttpClient();
	}

	/**
	 * Interroge llama avec le contexte de la conversation, ajoute la réponse de
	 * l'assistant à celle-ci et la renvoie.
	 *
	 * @throws \RuntimeException si llama est injoignable ou répond en erreur
	 */
	public function reply(Conversation $conversation): Message {
		$payload = [
			'model'       => 'local',
			'messages'    => $conversation->toApiMessages(),
			'temperature' => $this->temperature,
			'max_tokens'  => $this->maxTokens,
			'stream'      => false,
		];

		// L'inférence peut être lente (Jetson) → timeout large.
		$res = $this->http->timeout(120)->post($this->endpoint, $payload);

		if ($res->failed()) {
			throw new \RuntimeException(
				'llama-server injoignable ou en erreur (' . $res->status() . ') ' . $res->error()
			);
		}

		$content = $res->json()['choices'][0]['message']['content'] ?? '';

		return $conversation->addMessage(Message::assistant($content));
	}

}
