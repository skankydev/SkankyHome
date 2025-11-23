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

/**
 * 	
 */
class Route 
{

	/**
     * Valid HTTP methods.
     *
     * @var array
     */
    const VALID_METHODS = ['GET', 'PUT', 'POST', 'PATCH', 'DELETE', 'OPTIONS', 'HEAD'];

	private $shema;
	private $link;
	private $rules;
	private $regex = false;


	/**
	 * create a new route
	 * @param string $schema the route shema ex /article/:slug
	 * @param array  $link   ['controller'=>'','action'=>'','params'=>['name'=>'value','name'=>'value'...]]
	 * @param array  $rules  the rules for match with uri ex ['slug'=>'[a-zA-Z0-9-]*']
	 */
	public function __construct(string $shema, array $link,$rules = []){
		$this->shema = $shema;
		$this->link = $link;
		$this->initLink();

		$this->rules = $rules;

	}

	/**
	 * init the link with the default value
	 */
	private function initLink(){
		if(!isset($this->link['action'])){
			$this->link['action'] = 'index';
		}
		if(!isset($this->link['namespace'])){
			$this->link['namespace'] = 'App';	
		}		
	}

	/**
	 * get the link array
	 * @return array the link
	 */
	public function getLink(){
		return $this->link;
	}

	/**
	 * get the rules
	 * @return array the rules
	 */
	public function getRules(){
		return $this->rules;
	}

	/**
	 * get the shema
	 * @return string the shema
	 */
	public function getShema(){
		return $this->shema;
	}

	/**
	 * create te regex for matchin route
	 */
	private function makeRegex(){
		$tmp = str_replace('/','\/',$this->shema);
		$tmp = preg_replace_callback('/:[a-z0-9]*/',[$this,'pregCallback'],$tmp);
		$this->regex = '/^'.$tmp.'$/';
	}


	/**
	 *	/!\ this methode MUST NOT be called /!\
	 * 
	 * replace the param expected by the rules
	 * @param  string $params the string form preg_replace_callback ex: :slug
	 * @return string         the rulse 
	 */
	private function pregCallback($params){
		$key = $params[0];
		$key = substr($key,1);
		return $this->rules[$key];
	}

	/**
	 * get the regex for matche with uri
	 * @return string the regex
	 */
	public function getMatcheRules(){
		if(!$this->regex){
			$this->makeRegex();
		}
		return $this->regex;
	}
}