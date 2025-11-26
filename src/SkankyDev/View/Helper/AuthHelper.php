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

namespace SkankyDev\View\Helper;

use SkankyDev\Auth;
use SkankyDev\View\Helper\MasterHelper;

class AuthHelper extends MasterHelper {
	
	public function getAuth(){
		return Auth::getInstance()->getAuth();
	}

}