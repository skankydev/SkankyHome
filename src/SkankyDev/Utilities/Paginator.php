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

use SkankyDev\Utilities\Traits\IterableData;
use SkankyDev\Config\Config;
use Iterator;

/**
* Paginator
*/
class Paginator implements Iterator {
	
	use IterableData;

	var $data = [];
	var $option = ['sort' => ['_id'=> -1],];
	
	/**
	 * construct
	 * @param array $data the iterable data 
	 */
	function __construct(iterable $data,array $option){
		$this->data = $data;
		$this->option = array_merge($this->option,$option);
		$this->initInfo();
	}

	/**
	 * get option for pagination
	 * @return array the option
	 */
	function getOption(){
		return $this->option;
	}

	/**
	 * set option for pagination
	 * @param array $option new option
	 */
	function initInfo($option = []){
		$this->option = array_merge($this->option,$option);
		$this->option['pages'] = (int)floor($this->option['count']/$this->option['limit'])+(($this->option['count']%$this->option['limit'])?1:0);
		$this->option['first'] = 1;
		$this->option['last'] = $this->option['pages'];
		$next = $this->option['page']+1;
		$this->option['next'] = ($next>$this->option['last'])? $this->option['last'] : $next;
		$prev = $this->option['page']-1;
		$this->option['prev'] = ($prev<$this->option['first'])? $this->option['first'] : $prev;
		$start = $this->option['page'] - (int)floor($this->option['range']/2);
		$this->option['start'] = ($start<$this->option['first'])?$this->option['first'] : $start;
		$stop = (int)floor($this->option['range']/2) + $this->option['page'] + ($this->option['range']%2);
		$this->option['stop'] = ($stop>$this->option['last'])?($this->option['last']+1):$stop;
	}

	/**
	 * get param array for sort link
	 * @param  string $field the field
	 * @return array         the params for sort link
	 */
	public function sortParams($field){
		$sort = $this->option['sort'];
		$key = array_keys($sort);
		$params['page'] = 1;
		$params['field'] = $field;
		if(in_array($field, $key)){
			$params['order'] = $sort[$field]*-1;
		}else{
			$params['order'] = 1;
		}
		return $params;
	}
}
