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
use SkankyDev\Exception\ExceptionHandler;
use SkankyDev\Http\Middleware\MiddlewareManager;
use SkankyDev\Http\Request;
use SkankyDev\Http\Routing\Router;
use SkankyDev\Utilities\Session;


class Application {
	
	
	protected ExceptionHandler $exceptionHandler;
	
	public function __construct() {
		// Initialiser le gestionnaire d'exceptions
		$debug = Config::get('debug') ?? false;
		$this->exceptionHandler = new ExceptionHandler($debug);
		
		// Enregistrer le gestionnaire global
		set_exception_handler([$this->exceptionHandler, 'handle']);
		
		// Gérer aussi les erreurs fatales
		register_shutdown_function(function() {
			$error = error_get_last();
			if ($error && in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR])) {
				$exception = new \ErrorException(
					$error['message'],
					0,
					$error['type'],
					$error['file'],
					$error['line']
				);
				$this->exceptionHandler->handle($exception);
			}
		});
	}

	public function run(){
		try {
			Session::start();
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
		} catch (Exception $e) {
			$this->exceptionHandler->handle($e);
		}
	}
}
