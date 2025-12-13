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

use SkankyDev\Utilities\Traits\Singleton;


/**
* UserAgent
*
* aide à déterminer quel est le client 
* 
* http://www.useragentstring.com
*/

class UserAgent {
	
	use Singleton;

	public $os      = 'unknow';
	public $browser = 'unknow';
	public $mobile  = false;
	public $agent   = '';

	function __construct(){
		if(isset($_SERVER['HTTP_USER_AGENT'])){
			$this->agent = $_SERVER['HTTP_USER_AGENT'];
			//debug($this->agent);
		}
		$this->parse();
	}

	public function parse($agent = ''){
		if(!empty($agent)){
			$this->agent = $agent;
		}

		if(preg_match('/iphone/i', $this->agent) || preg_match('/ipad/i',$this->agent) || preg_match('/ipod/i',$this->agent)){
			$this->os = 'iOS';
			$this->mobile = true;
		}else if(preg_match('/android/i',$this->agent)){
			$this->os = 'Android';
			$this->mobile = true;
		}else if(preg_match('/Mac OS/i',$this->agent)){
			$this->os = 'MacOS';
			$this->mobile = false;
		}else if(preg_match('/Windows/i',$this->agent)){
			$this->os = 'Windows';
			$this->mobile = false;
		}else if(preg_match('/Ubuntu/i',$this->agent)){
			$this->os = 'Ubuntu';
			$this->mobile = false;
		}else if(preg_match('/Linux/i',$this->agent)){
			$this->os = 'Linux';
			$this->mobile = false;
		}

		if(preg_match('/Safari/i', $this->agent)&&!preg_match('/Chrome/i', $this->agent)){
			$this->browser = 'Safari';
		}else if (preg_match('/Firefox/i', $this->agent)) {
			$this->browser = 'Firefox';
		}else if (preg_match('/Opera/i', $this->agent) || preg_match('/OPR/i', $this->agent)) {
			$this->browser = 'Opera';
		}else if (preg_match('/Chrome/i', $this->agent)) {
			$this->browser = 'Chrome';
		}

		return [
			'os' => $this->os,
			'browser' => $this->browser,
			'mobile' => $this->mobile,
		];
	}

	public function getDevice(){
		/*if($this->info->ismobiledevice){
			return 'Mobile';
		}
		if($this->info->istablet){
			return 'Tablet';
		}*/
		return $this->mobile?'Mobile':'Desktop';
	}
}
