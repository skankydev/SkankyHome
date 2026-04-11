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

namespace SkankyDev\Utilities;

use SkankyDev\Utilities\Traits\IterableData;
use SkankyDev\Config\Config;
use Iterator;

/**
 * Wraps a MongoDB cursor result set with pagination metadata.
 * Implements Iterator so it can be used directly in foreach loops.
 */
class Paginator implements Iterator {

	use IterableData;

	public array $data   = [];
	public array $option = ['sort' => ['_id' => -1]];

	/**
	 * @param iterable $data   the result set (MongoDB cursor or array)
	 * @param array    $option pagination options: page, limit, total, range, sort
	 */
	public function __construct(iterable $data, array $option) {
		$this->data   = $data;
		$this->option = array_merge($this->option, $option);
	}

	/**
	 * Finalises pagination info and attaches the link/get arrays used by the view helper.
	 * @param array $link  route array passed to UrlBuilder (controller, action, params…)
	 * @param array $get   extra GET parameters to append to pagination links
	 * @return array       the complete option array ready for the view
	 */
	public function getOption(array $link = [], array $get = []): array {
		$this->initInfo();
		$this->option['link'] = $link;
		$this->option['get']  = $get;
		return $this->option;
	}

	/**
	 * Computes derived pagination values (pages, first, last, next, prev, start, stop).
	 * @param array $option optional overrides merged before computing
	 */
	public function initInfo(array $option = []): void {
		$this->option = array_merge($this->option, $option);
		$this->option['pages'] = (int) floor($this->option['total'] / $this->option['limit'])
			+ (($this->option['total'] % $this->option['limit']) ? 1 : 0);
		$this->option['first'] = 1;
		$this->option['last']  = $this->option['pages'];
		$next = $this->option['page'] + 1;
		$this->option['next'] = ($next > $this->option['last']) ? $this->option['last'] : $next;
		$prev = $this->option['page'] - 1;
		$this->option['prev'] = ($prev < $this->option['first']) ? $this->option['first'] : $prev;
		$start = $this->option['page'] - (int) floor($this->option['range'] / 2);
		$this->option['start'] = ($start < $this->option['first']) ? $this->option['first'] : $start;
		$stop = (int) floor($this->option['range'] / 2) + $this->option['page'] + ($this->option['range'] % 2);
		$this->option['stop'] = ($stop > $this->option['last']) ? ($this->option['last'] + 1) : $stop;
	}

	/**
	 * get param array for sort link
	 * @param  string $field the field
	 * @return array         the params for sort link
	 */
	public function sortParams(string $field){
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
