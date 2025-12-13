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

use SkankyDev\Config\Config;
use SkankyDev\Core\MasterFactory;
use SkankyDev\Http\Request;
use SkankyDev\Http\Routing\Route\CurrentRoute;


class MiddlewareManager {

	private array $default = [];
	private array $asso = [];

	public function __construct(){
		$this->default = Config::get('middlewares');
		$this->asso  = Config::get('class.middleware');
	}

	public function run(Request $request, CurrentRoute $route, callable $callback){
		$all =[...$this->default, ...$route->getMiddlewares()];
		$all = array_unique($all);
		$pipeline = $this->getPipeline($all, $callback);
		return $pipeline($request);
	}

	protected function getPipeline(array $middlewares, callable $destination){
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