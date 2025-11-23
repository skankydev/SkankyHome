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
namespace SkankyDev\Utilities;

use SkankyDev\Utilities\Traits\ArrayPathable;

class Cookie {

	use ArrayPathable;

	private $data = [];
	private $name;
	private $time;
	private $secure;
	private $httponly;

	public function __construct($name,$time = 0,$secure=false,$httponly=true){
		$this->name = $name;
		$this->time = ($time)?time()+$time:0;
		$this->secure = $secure;
		$this->httponly = $httponly;
		if(isset($_COOKIE[$this->name])){
			$this->data = unserialize($_COOKIE[$this->name]);
		}
	}

	/**
	 * set cookie
	 */
	public function setCookie(){
		//voir pour $path = uri mais je sais pas si ca peux etre utile
		//voir pour $domaine = server mais je sais pas si ca peux etre utile
		setcookie($this->name, serialize($this->data), $this->time,'/',$_SERVER['HTTP_HOST'],$this->secure,$this->httponly);
	}

	/**
	 * set value in cookie path
	 * @param string  $path     the path to the data separated by dot
	 * @param mixed   $value    the value
	 * @return  void
	 */
	public function get($path = ''){
		return $this->arrayGet($path,$this->data);
	}

	/**
	 * set value in cookie path
	 * @param string  $path     the path to the data separated by dot
	 * @param mixed   $value    the value
	 * @return  void
	 */
	public function set($path,$value){
		$result = $this->arraySet($path,$value,$this->data);
		return $this->setCookie();
	}

	/**
	 * delete value in cookie path
	 * @param  string  $path     the path to the data separated by dot
	 * @return void
	 */
	public function delete($path = ''){
		$this->arrayDelete($path,$this->data);
		return $this->setCookie();
	}
}