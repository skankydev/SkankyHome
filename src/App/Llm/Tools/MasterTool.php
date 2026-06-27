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

namespace App\Llm\Tools;

/**
 * Contrat d'un tool exposé à Pénélope (function calling).
 *
 * Un tool = une capacité que le LLM peut *demander* d'exécuter ; c'est notre code
 * qui l'exécute réellement (execute) et renvoie le résultat au modèle.
 * Les tools sont regroupés en ToolSet selon le contexte (chat, ambient ESP…).
 */
abstract class MasterTool {

	/** Nom appelé par le LLM (snake_case, unique dans un ToolSet). */
	abstract public function name(): string;

	/** Description en langage naturel : c'est ce qui guide le LLM. */
	abstract public function description(): string;

	/**
	 * JSON Schema des paramètres (type object).
	 * Sans paramètre : ['type' => 'object', 'properties' => (object)[]]
	 * — properties doit rester un objet JSON `{}`, pas un tableau `[]`.
	 */
	abstract public function parameters(): array;

	/**
	 * Exécute le tool avec les arguments fournis par le LLM.
	 * Le retour est sérialisé en JSON et renvoyé au modèle comme message `tool`.
	 */
	abstract public function execute(array $args): mixed;

	/**
	 * Définition au format API (compatible OpenAI/llama-server), pour le champ `tools`.
	 */
	public function definition(): array {
		return [
			'type'     => 'function',
			'function' => [
				'name'        => $this->name(),
				'description' => $this->description(),
				'parameters'  => $this->parameters(),
			],
		];
	}

}
