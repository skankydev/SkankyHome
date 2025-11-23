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

namespace SkankyDev\Routing;

use SkankyDev\Utilities\Traits\Singleton;
use SkankyDev\Utilities\Traits\StringFacility;

class UrlBuilder
{
	use Singleton, StringFacility;
	
	public function build(array $link, $absolut = false){
		$link = $this->completLink($link);
		$collection = Router::_getRoutesCollection();
		$route = $this->matcheWithRoute($link);
		$url = '';
		if($route){
			$url = $this->createUrlFromRout($link,$route);
		}else{
			$url = $this->createUrlFromDefault($link);
		}
		if(isset($link['get'])){
			$url = $this->addGet($url,$link['get']);
		}
		if($absolut){
			$request = Request::getInstance();

			$url = $request->sheme.'://'.$request->host.$url;
		}
		return $url;
	}

	public function buildCurrent( $absolut = false){
		$current = Router::_getCurrentRoute()->getLink();
		return $this->build($current,$absolut);
	}

	public function completLink(array $link){
		$current = Router::_getCurrentRoute()->getLink();
		if(!isset($link['namespace'])){
			$link['namespace'] = $current['namespace'];
		}
		if(!isset($link['controller'])){
			$link['controller'] = $current['controller'];
		}
		if(!isset($link['action'])){
			$link['action'] = Config::getDefaultAction();
		}
		return $link;
	}

	public function matcheWithRoute($link){
		$collection = Router::_getRoutesCollection();
		if(!empty($collection)){
			//find
			foreach ($collection as $route) {
				$test = $route->getLink();
				if( $link['controller']===$test['controller'] && 
					$link['action']===$test['action'] && 
					$link['namespace']===$test['namespace']
				){
					return $route;
				}
			}
		}
		return null;
	}

	public function createUrlFromRout($link,$route){
		$shema = $route->getShema();
		$tmp = trim($shema,'/');
		$tmp = explode('/', $tmp);
		$k = 0;
		$url = '';
		foreach ($tmp as $key => $value) {
			if(substr($value,0,1)===":"){
				$v = substr($value,1);
				if(array_key_exists($v,$link['params'])){
					$url .= $link['params'][$v].'/';
				}else{
					$url .= $link['params'][$k].'/';
				}
				$k++;
			}else{
				$url .= $value.'/';
			}
		}
		$url = trim($url,'/');
		return '/'.$url;
	}

	public function createUrlFromDefault($link){
		$url = '';
		if($link['namespace']!==Config::getDefaultNamespace()){
			$url .= '/'.$this->toDash($link['namespace']);
		}
		$url .= '/'.$this->toDash($link['controller']);
		if($link['action'] !== Config::getDefaultAction()){
			$url .= '/'.$this->toDash($link['action']);
		}

		if(isset($link['params'])&&!empty($link['params'])){
			foreach ($link['params'] as $key => $params){
				$url .= '/'.$params;
			}
		}
		return $url;
	}

	public function addGet($url,$get){
		$url .= '?';
		foreach ($get as $key => $value) {
			$url .= urlencode($key).'='.urlencode($value).'&';
		}
		$url = trim($url,'&');
		return $url;
	}
}