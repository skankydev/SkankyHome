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

namespace SkankyDev\Http\Middleware;

use ReflectionClass;
use ReflectionMethod;
use SkankyDev\Config\Config;
use SkankyDev\Core\MasterFactory;
use SkankyDev\Http\Middleware\Attribute\Middleware;
use SkankyDev\Http\Request;
use SkankyDev\Http\Routing\Route\CurrentRoute;


class MiddlewareManager {

	private array $default = [];
	private array $asso = [];

	/**
	 * Loads default middlewares and the middleware alias map from config.
	 */
	public function __construct(){
		$this->default = Config::get('middlewares');
		$this->asso  = Config::get('class.middlewares');
	}

	/**
	 * Builds and executes the middleware pipeline for the current request.
	 * Merges default middlewares with any route-specific ones, deduplicates,
	 * then runs through the pipeline ending with the controller callback.
	 * @return mixed the response returned by the pipeline
	 */
	public function run(Request $request, CurrentRoute $route, callable $callback): mixed {
		$all = [
			...$this->default,
			...$this->attributeMiddlewares($route->getController(), $route->getAction()),
			...$route->getMiddlewares(),
		];
		$all = array_unique($all);
		$pipeline = $this->getPipeline($all, $callback);
		return $pipeline($request);
	}

	/**
	 * Collects the middlewares declared via #[Middleware] attributes on the
	 * controller class and, more specifically, on the action method.
	 * Class-level middlewares come first (they guard the whole controller),
	 * then the action-specific ones.
	 * @return string[] middleware class names or config aliases
	 */
	public function attributeMiddlewares(string $controller, string $action): array {
		if (!class_exists($controller)) {
			return [];
		}

		$reflection = new ReflectionClass($controller);
		$targets = [$reflection];
		if ($reflection->hasMethod($action)) {
			$targets[] = $reflection->getMethod($action);
		}

		$middlewares = [];
		foreach ($targets as $target) {
			/** @var ReflectionClass|ReflectionMethod $target */
			foreach ($target->getAttributes(Middleware::class) as $attribute) {
				array_push($middlewares, ...$attribute->newInstance()->middlewares);
			}
		}

		return $middlewares;
	}

	/**
	 * Wraps middlewares around the destination callable in reverse order,
	 * producing a single callable that represents the full pipeline.
	 * Resolves middleware class names via the alias map if needed.
	 */
	protected function getPipeline(array $middlewares, callable $destination): callable {
		$pipeline = $destination;
		foreach (array_reverse($middlewares) as $name) {
			$className = $this->asso[$name] ?? $name;
			$pipeline = function($request) use ($className, $pipeline) {
				$middleware = MasterFactory::_make($className);
				return $middleware->handle($request, $pipeline);
			};
		}
		return $pipeline;
	}

}