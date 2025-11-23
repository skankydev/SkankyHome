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

use SkankyDev\Model\Document\DocumentInterface;

/**
 * method return false if not valid
 */


class Validator {

	private $shema = [];
	private $trimList = [];

	/**
	 * add validation rules for fields
	 * @param array|string $fields  the fields list
	 * @param array        $rules   the rules with params (ex: ['regex'=>['preg'=>'/^[a-zA-Z0-9]*$/']])
	 * @param string       $message the message to user when the data is not valid 
	 */
	public function addRules($fields,$rules,$message){
		if(is_array($fields)){
			foreach ($fields as $key => $value) {
				if (isset($this->shema[$value])) {
					$this->shema[$value]['rules'] += $rules;
				}else{
					$this->shema[$value]['rules'] = $rules;
				}
				$this->shema[$value]['message'] = $message;
				
			}
		}elseif(is_string($fields)){
			if(isset($this->shema[$fields])){
				$this->shema[$fields]['rules'] += $rules;
			}else{
				$this->shema[$fields]['rules'] = $rules;
			}
			$this->shema[$fields]['message'] = $message;
		}
		//return $this->shema;
	}

	public function getRules(){
		return $this->shema;
	}

	public function trimTag($fields){
		$this->trimList = $fields;
	}

	public function valid(DocumentInterface &$data){
		if(!empty($this->trimList)){
			$this->doTrim($data);
		}
		$retour = true;
		foreach ($this->shema as $field => $config) {
			foreach ($config['rules'] as $name => $params) {
				$rName = '';
				if(is_array($params)){
					$rName = $name; 
					array_unshift($params, $data->{$field});

					$result = call_user_func_array([$this,$rName],$params);
				}else{
					$rName = $params;
					$result = $this->{$rName}($data->{$field});
				}
				if(!$result){
					$retour = false;
					$data->setValidateMessage($field,$config['message']);
				}
			}
		}
		return $retour;
	}

	public function doTrim($data){
		foreach ($this->trimList as $field) {
			$data->{$field} = trim($data->{$field});
			$data->{$field} = strip_tags($data->{$field});
			$data->{$field} = htmlentities($data->{$field});
		}
	}

	public function notEmpty($value){
		return !empty($value);
	}

	public function regex($value,$regex){
		return preg_match($regex,$value);
	}

	public function minLength($value,$min){
		return (strlen($value)>=$min);
	}

	public function maxLength($value,$max){
		return (strlen($value)<=$max);
	}

	public function betweenLength($value,$min,$max){
		return ((strlen($value)>=$min)&&(strlen($value)<=$max));
	}

	public function isEmail($value){
		return filter_var($value, FILTER_VALIDATE_EMAIL)?true:false;
	}

	public function isNum($value){
		return is_numeric($value);
	}

	public function isString($value){
		return is_string($value);
	}

	public function isBool($value){
		return is_bool($value);
	}
}
