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

namespace SkankyDev\Http;

use SkankyDev\Utilities\Traits\Singleton;

class Request {

	use Singleton;

	protected array $query;      // $_GET
	protected array $post;       // $_POST
	protected array $files;      // $_FILES (ton joli tableau)
	protected array $cookies;    // $_COOKIE
	protected array $server;     // $_SERVER
	protected array $headers;    // Headers HTTP
	protected ?string $body;     // Body brut
	protected ?array $json;      // Body parsé si JSON

	public function __construct(){
		$this->query = $_GET;
		$this->post = $_POST;
		$this->files = $this->normalizeFiles();
		$this->cookies = $_COOKIE;
		$this->server = $_SERVER;
		$this->headers = $this->parseHeaders();
		$this->body = file_get_contents('php://input');
		$this->json = $this->parseJson();
	}

	// Méthode HTTP
	public function method(): string {
		return strtoupper($this->server['REQUEST_METHOD'] ?? 'GET');
	}

	// Http ou Https
	public function sheme(): string {
		return $this->server['REQUEST_SCHEME'] ?? 'http';
	}

	// Host name
	public function host(): string {
		return $this->server['HTTP_HOST'] ?? '';
	}

	

	// URI demandée
	public function uri(): string {
		return strtok($this->server['REQUEST_URI'] ?? '/', '?');
	}

	// Full URL avec query string
	public function fullUri(): string {
		return $this->server['REQUEST_URI'] ?? '/';
	}

	// Récupérer un paramètre GET
	public function query(?string $key = null, $default = null) {
		if ($key === null) {
			return $this->query;
		}
		return $this->query[$key] ?? $default;
	}

	// Récupérer un paramètre POST
	public function post(?string $key = null, $default = null) {
		if ($key === null) {
			return $this->post;
		}
		return $this->post[$key] ?? $default;
	}

	// Récupérer dans POST ou query string ou JSON
	public function input(?string $key = null, $default = null) {
		if ($key === null) {
			return array_merge($this->query, $this->post, $this->json ?? []);
		}

		// Priorité : POST > JSON > GET
		return $this->post[$key] 
			?? ($this->json[$key] ?? null)
			?? $this->query[$key] 
			?? $default;
	}

	// Récupérer un fichier uploadé
	public function file(?string $key = null)
	{
		if ($key === null) {
			return $this->files;
		}
		return $this->files[$key] ?? null;
	}

	// Récupérer un cookie
	public function cookie(?string $key = null, $default = null)
	{
		if ($key === null) {
			return $this->cookies;
		}
		return $this->cookies[$key] ?? $default;
	}

	// Récupérer un header
	public function header(?string $key = null, $default = null)
	{
		if ($key === null) {
			return $this->headers;
		}
		$key = strtolower($key);
		return $this->headers[$key] ?? $default;
	}

	// Body brut
	public function body(): ?string
	{
		return $this->body;
	}

	// Body JSON parsé
	public function json(?string $key = null, $default = null)
	{
		if ($this->json === null) {
			return $default;
		}
		if ($key === null) {
			return $this->json;
		}
		return $this->json[$key] ?? $default;
	}

	// IP du client
	public function ip(): string {
		// Gérer les proxies
		if (!empty($this->server['HTTP_X_FORWARDED_FOR'])) {
			$ips = explode(',', $this->server['HTTP_X_FORWARDED_FOR']);
			return trim($ips[0]);
		}
		return $this->server['REMOTE_ADDR'] ?? '0.0.0.0';
	}

	// User agent
	public function userAgent(): ?string {
		return $this->server['HTTP_USER_AGENT'] ?? null;
	}

	// Est-ce une requête AJAX ?
	public function isAjax(): bool {
		return strtolower($this->header('x-requested-with', '')) === 'xmlhttprequest';
	}

	// Est-ce une requête JSON ?
	public function isJson(): bool
	{
		return str_contains(
			strtolower($this->header('content-type', '')), 
			'application/json'
		);
	}

	// Attend du JSON en retour ?
	public function wantsJson(): bool {
		$accept = strtolower($this->header('accept', ''));
		return str_contains($accept, 'application/json');
	}

	// Parser les headers HTTP
	protected function parseHeaders(): array {
		$headers = [];
		foreach ($this->server as $key => $value) {
			if (str_starts_with($key, 'HTTP_')) {
				$header = str_replace('_', '-', substr($key, 5));
				$headers[strtolower($header)] = $value;
			}
			// Headers spéciaux
			if (in_array($key, ['CONTENT_TYPE', 'CONTENT_LENGTH'])) {
				$header = str_replace('_', '-', $key);
				$headers[strtolower($header)] = $value;
			}
		}
		return $headers;
	}

	// Parser le JSON du body
	protected function parseJson(): ?array {
		if ($this->isJson() && !empty($this->body)) {
			$decoded = json_decode($this->body, true);
			return json_last_error() === JSON_ERROR_NONE ? $decoded : null;
		}
		return null;
	}

	// Normaliser $_FILES pour avoir un joli tableau
	protected function normalizeFiles(): array {

		$normalized = [];
		foreach ($_FILES as $field => $files) { //pour tout les input file
			$test = current($files);
			if(is_array($test)){ //si multiple
				foreach ($files as $index => $value) { //convertion du tableau
					foreach ($value as $key => $v) {
						$normalized[$field][$key][$index] = $v;
					}
				}
			}else{ //sinon tout va bien
				$normalized[$key] = $file;
			}
		}

		return $normalized;
	}

	// Accès magique aux propriétés
	public function __get(string $name)
	{
		return $this->input($name);
	}

	public function paginateInfo(int $defaultLimit = 25,array $defaultSort = []){
		return [
			'page' => (int)($this->query('page') ?? 1),
			'limit' => (int) ($this->query('limit') ?? $defaultLimit),
			'sort' => $this->getSort($defaultSort),
		];
	}

	protected function getSort(array $default = []): array {
		$field = $this->query('field');
		$order = $this->query('order');

		if ($field && $order) {
			return [$field => (int) $order];
		}

		return $default;
	}
}
