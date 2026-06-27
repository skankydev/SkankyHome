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
use SkankyDev\Utilities\Log;

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

	/** Garde-fou : nombre max d'allers-retours tools avant d'abandonner la boucle. */
	private int $maxIterations = 5;

	public function __construct(?HttpClient $http = null, ?string $baseUrl = null) {
		if ($baseUrl === null) {
			$conf = Config::get('llama');
			$baseUrl = 'http://' . $conf['host'] . ':' . $conf['port'];
		}
		$this->endpoint = rtrim($baseUrl, '/') . '/v1/chat/completions';
		$this->http = $http ?? new HttpClient();
	}

	/**
	 * Boucle d'agent : interroge llama avec le contexte de la conversation et les
	 * tools disponibles, exécute les tools que le LLM demande, et reboucle jusqu'à
	 * obtenir une réponse texte. Seule cette réponse finale est ajoutée à la
	 * conversation (les messages tool intermédiaires restent éphémères, mais sont
	 * tracés dans le log de debug).
	 *
	 * @throws \RuntimeException si llama est injoignable, en erreur, ou si la
	 *                           boucle dépasse maxIterations.
	 */
	public function reply(Conversation $conversation, ?ToolSet $tools = null): Message {
		$tools ??= new ToolSet();             // pas de tools par défaut
		$messages = $conversation->toApiMessages();

		for ($i = 0; $i < $this->maxIterations; $i++) {
			$data = $this->callLlama($messages, $tools);
			$assistant = $data['choices'][0]['message'] ?? [];
			$toolCalls = $assistant['tool_calls'] ?? [];

			// Pas de tool demandé → c'est la réponse finale.
			if ($toolCalls === []) {
				$content = $assistant['content'] ?? '';
				return $conversation->addMessage(Message::assistant($content));
			}

			// Le LLM veut appeler des tools : on rejoue son message puis chaque résultat.
			$messages[] = $assistant;
			foreach ($toolCalls as $call) {
				$messages[] = $this->runToolCall($call, $tools);
			}
		}

		throw new \RuntimeException('Boucle de tools trop longue (> ' . $this->maxIterations . ' itérations)');
	}

	/**
	 * Envoie un tour à llama (messages + éventuels tools) et renvoie la réponse décodée.
	 */
	private function callLlama(array $messages, ToolSet $tools): array {
		$payload = [
			'model'       => 'local',
			'messages'    => $messages,
			'temperature' => $this->temperature,
			'max_tokens'  => $this->maxTokens,
			'stream'      => false,
		];
		if (!$tools->isEmpty()) {
			$payload['tools'] = $tools->definitions();
		}

		Log::debug('Pénélope → llama (requête)', $payload);

		// L'inférence peut être lente (Jetson) → timeout large.
		$res = $this->http->timeout(120)->post($this->endpoint, $payload);

		if ($res->failed()) {
			throw new \RuntimeException(
				'llama-server injoignable ou en erreur (' . $res->status() . ') ' . $res->error()
			);
		}

		$data = $res->json() ?? [];
		Log::debug('llama → Pénélope (réponse)', $data);

		return $data;
	}

	/**
	 * Exécute un tool_call demandé par le LLM et renvoie le message `tool` à
	 * réinjecter dans le contexte (résultat sérialisé en JSON).
	 */
	private function runToolCall(array $call, ToolSet $tools): array {
		$name = $call['function']['name'] ?? '';
		$args = json_decode($call['function']['arguments'] ?? '{}', true) ?: [];

		Log::debug('Pénélope → tool call', ['name' => $name, 'args' => $args]);

		try {
			$result = $tools->execute($name, $args);
		} catch (\Throwable $e) {
			$result = ['error' => $e->getMessage()];
		}

		Log::debug('tool → résultat', ['name' => $name, 'result' => $result]);

		return [
			'role'         => 'tool',
			'tool_call_id' => $call['id'] ?? '',
			'content'      => json_encode($result, JSON_UNESCAPED_UNICODE),
		];
	}

}
