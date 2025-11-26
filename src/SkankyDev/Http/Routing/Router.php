<?php 
/**
 * Copyright (c) 2015 SCHENCK Simon
 * 
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 * @copyright     Copyright (c) SCHENCK Simon
 *
 */

namespace SkankyDev\Http\Routing;



use SkankyDev\Http\Routing\Route\CurrentRoute;
use SkankyDev\Http\Routing\Route\Route;
use SkankyDev\Utilities\Traits\Singleton;


class Router
{
	use Singleton;

	//private $request;
	private $routesCollection;
	private $routes;
	private $uri;
	private $current = false;

	public function __construct(){
		$this->routes = [];
	}

	/**
	 * Add a new route in route Collection
	 * @param string $schema the route shema ex /article/:slug
	 * @param array  $link   ['controller'=>'','action'=>'','params'=>['name'=>'value','name'=>'value'...]]
	 * @param array  $rules  the rules for match with uri ex ['slug'=>'[a-zA-Z0-9-]*']
	 */
	public function add(string $schema,array $link,$rules = []){
		$route = new Route($schema,$link,$rules);
		$this->routesCollection[] = $route;
		return $route;
	}

	/**
	 * get the routes collection
	 * @return arra a array with all routes
	 */
	public function getRoutesCollection(){
		return $this->routesCollection;
	}

	/**
	 * get the current route
	 * @return SkankyDev\Routing\Route\CurrenteRoute the currente route object
	 */
	public function getCurrentRoute(){
		return $this->current;
	}

	/**
	 * find the current route 
	 * @param  string $uri the uri form user request 
	 * @return SkankyDev\Routing\Route\CurrenteRoute   the currente route
	 */
	public function findCurrentRoute(string $uri){
		$tmp = explode('?', $uri);
		$uri = $tmp[0];
		$route = $this->matchRouteUri($uri);
		$this->current = new CurrentRoute($uri,$route);
		return $this->current;
	}

	/**
	 * try to matche $uri with a route in Collection
	 * @param  string $uri                         the uri form user request 
	 * @return SkankyDev\Routing\Route\Route|null  if a route matche return route or null
	 */
	public function matchRouteUri(string $uri){
		if(!empty($this->routesCollection)){
			foreach ($this->routesCollection as $route) {
				$regex = $route->getMatcheRules();
				if(preg_match($regex,$uri)){
					return $route;
				}
			}
		}
		return null;
	}
}