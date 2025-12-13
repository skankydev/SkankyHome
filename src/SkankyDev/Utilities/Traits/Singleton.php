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


namespace SkankyDev\Utilities\Traits;

use SkankyDev\Exception\UnknownMethodException;


trait Singleton {

	private static $_instance = null;

	public static function getInstance() {
		if(is_null(self::$_instance)) {
			$name = get_called_class();
			self::$_instance = new $name();
		}
		return self::$_instance;
	}


	public static function __callStatic($name, $arguments){
		if(is_null(self::$_instance)) {
			self::getInstance();
		}

		if(substr( $name, 0, 1 ) === '_'){
			$name = substr($name,1);
		}
		
		if(method_exists(self::$_instance, $name)){
			return call_user_func_array([self::$_instance,$name], $arguments);
		}else{
			throw new UnknownMethodException('Unknown method : '.$name.' in Class : '.get_called_class(),101);
		}

	}
}
