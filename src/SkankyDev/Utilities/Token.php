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


class Token{
	
	public $value;
	public $time;

	public function __construct(){
		$this->value = bin2hex(openssl_random_pseudo_bytes(16));
		$this->time  = time();
	}

	public function checkTime($life = TIME_HOUR){
		$current = time();
		$tokenExpir = $this->time + $life;
		return ($tokenExpir > $current);
	}

	public function checkValue($value){
		return ($this->value === $value);
	}

	public function getToken(){
		return $this->value;
	}


}

