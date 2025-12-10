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

use App\Form\TestForm;
use SkankyDev\Controller\MasterController;
use SkankyDev\Http\Request;
use SkankyDev\Http\Routing\Router;

class HomeController extends MasterController {

	public function __construct(protected Request $request){

	}

	public function index(){
		
		flash('info','ceci est le message');
		flash('success','ceci est le message');
		flash('warning','ceci est le message');
		flash('error','ceci est le message');
		return view('home.index');
	}

	public function base(){
		return view('home.base');
	}

	public function pasla(){
		return redirect(['action'=>'index','get'=>['is_redirect'=>'oui']]);
	}
}
