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
use SkankyDev\Http\Request;
use SkankyDev\Http\Routing\Router;


if ( !defined('DS') ){
	define('DS', DIRECTORY_SEPARATOR);
}

if ( !defined('APP_FOLDER') ){
	define('APP_FOLDER', dirname(dirname(__DIR__)));
}

if ( !defined('PUBLIC_FOLDER') ){
	define('PUBLIC_FOLDER',APP_FOLDER.DS.'public');
}

class Application {
	
	
	public function __construct() {
		
	}

	public function run(){
		try {
			include_once APP_FOLDER.DS.'config'.DS.'routes.php';
			$request = Request::getInstance();
			$current = Router::_findCurrentRoute($request->uri());
			
			$controller = MasterFactory::_make($current->getController());
			$result = MasterFactory::_call($controller,$current->getAction(),$current->getParams());

			/*$view = Dispatcher::_execute($current);
			$view->render();*/
		} catch (Exception $e) {
			var_dump($e);
			die();
			
		}
	}
}
