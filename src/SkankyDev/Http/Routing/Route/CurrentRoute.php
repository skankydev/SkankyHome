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

namespace SkankyDev\Http\Routing\Route;

use SkankyDev\Config\Config;
use SkankyDev\Http\Routing\Route\Route;
use SkankyDev\Utilities\Traits\StringFacility;

class CurrentRoute
{

	use StringFacility;

	private $controller;
	private $action;
	private $link;
	private $uri;
	private $middlewares = [];
	
	/**
	 * get the route matche with uri and parse the params
	 *
	 * if $rout is null the uri will be parse lick /:controller/:action/:param/:param ... or with /:namespace....
	 * 
	 * @param string     $uri   the uri
	 * @param Route|null $route the route have matche in the router or null
	 */	
	function __construct(string $uri ,Route $route = null){
		$this->uri = $uri;
		if($route){
			$this->initFromRoute($uri,$route);
		}else{
			$this->initFromUri($uri);
		}
	}


	/**
	 * pars the uri with the rulse defin in route
	 * @param  string $uri   the uri
	 * @param  Route  $route the route 
	 */
	public function initFromRoute(string $uri, Route $route){
		$this->link = $route->getLink();
		$rules = $route->getRules();
		if(!empty($rules)){

			$shema = $route->getShema();
			$shema = trim($shema,'/');
			$shema = explode('/',$shema);
			$uri = trim($uri,'/');
			$uri = explode('/',$uri);
			$params = [];
			foreach ($shema as $key => $value) {
				if(substr($value,0,1)===":"){
					$k = substr($value,1);
					$params[$k] = $uri[$key];
				}
			}
			$this->link['params'] = $params;
		}
		$this->middlewares = $route->getMiddlewares();
		$this->setControllerAction();
	}

	/**
	 * pars the uri with the default rules
	 * @param  string $uri the uri
	 * @return void        
	 */
	public function initFromUri(string $uri){

		$uri = trim($uri,'/');
		$tmp = explode('/', $uri);
		//$modules = Config::getModuleList();
		$modules = ['App'];
		$namespace = $this->toCap($tmp[0]);
		if(in_array($namespace, $modules)){
			$this->link['namespace'] = $namespace;
			array_shift($tmp);
		}else{
			$this->link['namespace']  = 'App';
		}
		$this->link['controller'] = $this->toCap($tmp[0]);
		$this->link['action']     = isset($tmp[1]) ? lcfirst($this->toCap(trim($tmp[1],'_'))) : 'index';

		if(isset($tmp[2])&&!empty($tmp[2])){
			$this->link['params'] = array_slice($tmp,2);
		}

		$this->setControllerAction();

	}

	/**
	 * define the controller name and the action for the dispatcher
	 */
	private function setControllerAction(){
		$this->controller = $this->link['namespace'].'\\Controller\\'.$this->link['controller'].'Controller';
		$this->action = $this->link['action'];
	}

	/**
	 * get the controller name
	 * @return string controller name
	 */
	public function getController(){
		return $this->controller;
	}

	/**
	 * get the action name
	 * @return string action name
	 */
	public function getAction(){
		return $this->action;
	}

	/**
	 * get parametres
	 * @return array the params array
	 */
	public function getParams(){
		return isset($this->link['params'])?$this->link['params']:[];
	}

	/**
	 * retrun the link array
	 * @return array the link
	 */
	public function getLink(){
		return $this->link;
	}
	/**
	 * get the namespance
	 * @return string the current
	 */
	public function getNamespace(){
		return $this->link['namespace'];
	}

	public function setMiddlewares(array $middlewares){
		$this->middlewares = $middlewares;
	}

	public function getMiddlewares(){
		return $this->middlewares;
	}
}