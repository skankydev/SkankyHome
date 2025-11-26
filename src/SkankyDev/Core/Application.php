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

namespace SkankyDev\Core;

use Exception;
use SkankyDev\Config\Config;
use SkankyDev\Http\Middleware\MiddlewareManager;
use SkankyDev\Http\Request;
use SkankyDev\Http\Routing\Router;


class Application {
	
	
	public function __construct() {
		
	}

	public function run(){
		try {
			Config::initConf();
			include_once APP_FOLDER.DS.'config'.DS.'routes.php';
			$request = Request::getInstance();
			$current = Router::_findCurrentRoute($request->uri());

			$manager = new MiddlewareManager();
        
			$response = $manager->run($request, $current, function($request) use ($current) {
				$controller = MasterFactory::_make($current->getController());
				return MasterFactory::_call($controller, $current->getAction(), $current->getParams());
			});
			if($response){
				$response->send();
			}
			/*$view = Dispatcher::_execute($current);
			$view->render();*/
		} catch (Exception $e) {
			var_dump($e);
			die();
			
		}
	}
}
