<?php 
/**
 * Copyright (c) 2015 SCHENCK Simon
 * 
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) SCHENCK Simon
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace SkankyDev\View\Helper;

use SkankyDev\View\Helper\MasterHelper;
use SkankyDev\Config\Config;
use DateTime;
use DateTimeZone;

class TimeHelper extends MasterHelper {

	private $timezone;
	private $format;

	function __construct($conf = []){
		if(empty($conf)){
			$conf = Config::get('timehelper');;
		}
		$this->timezone = new DateTimeZone($conf['timezone']);
		$this->format = $conf['format'];
	}

	/**
	 * converte DateTime to a string for display
	 * @param  DateTime $date the date
	 * @return string         the date on configured format
	 */
	public function toHuman(DateTime $date){
		$date->setTimezone($this->timezone);
		return $date->format($this->format);
	}

	/**
	 * get the time since the date
	 * @param  DateTime $date the date
	 * @param  boolean  $full if you want display all detail
	 * @return string         the time since the date
	 */
	public function since(DateTime $date, $full = false){
		//TO DO ca fait le taf mais c'est pas tip top 
		$now = new DateTime();
		$diff = $now->diff($date);

		$diff->w = floor($diff->d / 7);
		$diff->d -= $diff->w * 7;

		$string = [
			'y' => _('year'),
			'm' => _('month'),
			'w' => _('week'),
			'd' => _('day'),
			'h' => _('hour'),
			'i' => _('minute'),
			's' => _('second'),
		];
		
		foreach ($string as $k => &$v) {
			if ($diff->$k) {
				$v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
			} else {
				unset($string[$k]);
			}
		}

		if(!$full){
			$string = array_slice($string, 0, 1);
		}
		return $string ? implode(', ', $string) : _('just now');	
	}

}
