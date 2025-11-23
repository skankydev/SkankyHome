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

use SkankyDev\Config\Config;
use SkankyDev\Request;
use SkankyDev\Routing\Router;
use SkankyDev\Utilities\Session;

/**
 * 
 */
class Historique
{
	private $limit;

	/**
	 * get the historique and the limit 
	 */
	function __construct(){}

	/**
	 * add the 
	 * @param  Request $request the Request
	 * @return void
	 */
	function updateHistorique(){
		$request = Request::getInstance();
		$current = Router::_getCurrentRoute();
		$history = Session::get('skankydev.historique');
		if(!$history){
			$history = [];
		}
		$hData['url']    = $request->sheme.'://'.$request->host.$request->uri;
		$hData['direct'] = true;
		$hData['sheme']  = $request->sheme;
		$hData['method'] = $request->method;
		$hData['uri']    = $request->uri;
		$hData['link']   = $current->getLink() ;
		$count = array_unshift($history, $hData);
		$limit   = Config::get('historique.limit');
		if($count>$limit){
			unset($history[$limit]);
		}
		Session::set('skankydev.historique',$history);
	}

	/**
	 * get historique
	 * @return array the historique
	 */
	function getHistorique(){
		return Session::get('skankydev.historique');
	}

	/**
	 * get current link
	 * @return array the historique
	 */
	function getCurrent(){
		return Session::get('skankydev.historique.0');
	}
	/**
	 * get previe page
	 * @return array the historique
	 */
	function comeFrom(){
		$come = Session::get('skankydev.historique.1'); 
		return Session::get('skankydev.historique.1');
	}
	/**
	 * return the last direct request
	 * @return array the last request
	 */
	function getLastDirect(){
		$history = Session::get('skankydev.historique');
		$retour = false;
		$i = 1;
		$c = count($history);
		while ( ($i<$c) && ($history[$i]['direct'] == false) ){$i++;}
		if($i<$c){
			$retour = $history[$i];
		}
		return $retour;
	}

	/**
	 * set the last
	 */
	function notDirect(){
		//$this->history[0]['direct'] = false;
		Session::set('skankydev.historique.0.direct',false);
	}

	function pageCount(){
		$history = Session::get('skankydev.historique');
		return count($history);
	}
}
