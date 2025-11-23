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

use SkankyDev\Config\Config;
use SkankyDev\EventManager;
use SkankyDev\Factory;
use SkankyDev\Request;
use SkankyDev\Routing\Route\CurrentRoute;
use SkankyDev\Utilities\Interfaces\PermissionInterface;
use SkankyDev\Utilities\Traits\Singleton;

/**
 * 
 */
class Dispatcher {
	
	use Singleton;

	private $permission;

	public function execute(CurrentRoute $current){
		$request = Request::getInstance();
		$link = $current->getLink();
		// ca va surement disparaitre tout ca 
		$request->namespace = $link['namespace'];
		$request->controller = $link['controller'];
		$request->action = $link['action'];
		if(isset($link['params'])){
			$request->params = $link['params'];
		}
		Config::setCurentNamespace($link['namespace']);
		//c'est juste pour pas tout casser
		
		EventManager::getInstance()->event('router.execute.before',$this);
		$controller = Factory::load($current->getController());

		$method = new \ReflectionMethod($controller,$current->getAction());
		//debug($this->permission);
		$method->invokeArgs($controller,$current->getParams());
		/*if(!isset($this->permission)){
		}else{
			if($method->isPublic()){
				if($this->permission->checkPublicAccess($link)){
					$method->invokeArgs($controller, $current->getParams());
				}else{
					$request->redirect(Config::get('Auth.redirectAction'));
				}
			}elseif($method->isProtected()){
				if($this->permission->checkProtectedAccess($link)){
					$method->setAccessible(true);
					$method->invokeArgs($controller,$current->getParams());
				}else{
					$request->redirect(Config::get('Auth.redirectAction'));
				}
			}elseif($method->isPrivate()){
				if($this->permission->checkPrivateAccess($link)){
					$method->setAccessible(true);
					$method->invokeArgs($controller,$current->getParams());
				}else{
					$request->redirect(Config::get('Auth.redirectAction'));
				}
			}
		}*/
		EventManager::getInstance()->event('router.execute.after',$this);
		return $controller->_getView();
	}

	public function setPermissionManager(PermissionInterface $interface){
		//debug($interface);
		$this->permission = $interface;
	}
}