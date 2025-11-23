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

namespace App\Controller;

use SkankyDev\Controller\MasterController;
use SkankyDev\Http\Request;

class HomeController extends MasterController {

	public function __construct(protected  Request $request){

	}

	public function index(){
		debug($this->request);
		debug($this->request->input());
		dd('Coucou');
	}

	public function show(string $slug){
		debug('coucou : '.$slug);
	}
}
