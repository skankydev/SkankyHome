<?php 

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