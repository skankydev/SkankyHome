<?php /**
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

namespace SkankyDev\View\Helper;

use SkankyDev\View\Helper\MasterHelper;
/**
* 
*/
class MasterHelper {
	
	protected $script = '';
	protected $scriptFile = [];
	protected $cssFile = [];
	protected $header = '';

	/**
	 * add script for htmlView
	 * @param string $script some script
	 */
	public function addScript($script){
		$this->script .= $script;
	}

	/**
	 * get script added
	 * @return string the script
	 */	
	public function getScript(){
		return $this->script;
	}

	/**
	 * add script file
	 * @param string $file url of script
	 */
	public function addScriptFile($file){
		$this->scriptFile[]=$file;
	}

	/**
	 * get liste of script file
	 * @return array a liste of script file
	 */
	public function getScriptFile(){
		return $this->scriptFile;
	}

	/**
	 * add css file
	 * @param string $file url of css
	 */
	public function addCssFile($file){
		$this->cssFile[]=$file;
	}

	/**
	 * get liste of css file
	 * @return array a liste of css file
	 */
	public function getCssFile(){
		return $this->cssFile;
	}
	
	/**
	 * add some text to header
	 * @param string $head the text
	 */
	public function addHeader($head){
		$this->header .= $head;
	}
	/**
	 * get text for header
	 * @return string some header info
	 */
	public function getHeader(){
		return $this->header;
	}
}