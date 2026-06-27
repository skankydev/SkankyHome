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

use App\Llm\Tools\AddProjectTool;
use App\Llm\Tools\AddTaskTool;
use App\Llm\Tools\ListProjectsTool;
use App\Llm\Tools\ListTasksTool;
use App\Llm\Tools\MasterTool;
use App\Llm\Tools\UpdateTaskStatusTool;

/**
 * Un jeu de tools mis à disposition du LLM pour un contexte donné.
 *
 * Registre explicite (pas d'auto-découverte) : on déclare les tools dans des
 * fabriques par profil. Le scoping est volontaire — selon le déclencheur,
 * Pénélope n'a pas accès aux mêmes capacités (chat humain vs événement ESP).
 */
class ToolSet {

	/** @var array<string, MasterTool> tools indexés par nom */
	private array $tools = [];

	public function __construct(MasterTool ...$tools) {
		foreach ($tools as $tool) {
			$this->tools[$tool->name()] = $tool;
		}
	}

	/** Le tableau `tools` envoyé à llama (vide = on n'envoie rien). */
	public function definitions(): array {
		return array_map(fn(MasterTool $t) => $t->definition(), array_values($this->tools));
	}

	public function has(string $name): bool {
		return isset($this->tools[$name]);
	}

	public function isEmpty(): bool {
		return $this->tools === [];
	}

	/**
	 * Exécute le tool nommé avec ses arguments.
	 * @throws \RuntimeException si le tool n'existe pas dans ce set
	 */
	public function execute(string $name, array $args): mixed {
		if (!$this->has($name)) {
			throw new \RuntimeException("Tool inconnu dans ce ToolSet : {$name}");
		}
		return $this->tools[$name]->execute($args);
	}

	/**
	 * Profil « chat humain » : gestion de projet (lecture + écriture).
	 */
	public static function forChat(): self {
		return new self(
			new ListProjectsTool(),
			new ListTasksTool(),
			new AddTaskTool(),
			new UpdateTaskStatusTool(),
			new AddProjectTool(),
		);
	}

}
