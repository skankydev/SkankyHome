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
namespace App\Controller;

use App\Form\BaseForm;
use App\Job\HelloJob;
use SkankyDev\Controller\MasterController;
use SkankyDev\Http\Request;
use SkankyDev\Http\Routing\Router;

class HomeController extends MasterController {

	public function index(){
		return view('home.index');
	}

	public function base(){
		$form = new BaseForm(['action' => 'base']);
		flash('info','ceci est le message');
		flash('success','ceci est le message');
		flash('warning','ceci est le message');
		flash('error','ceci est le message');
		return view('home.base',['form'=>$form]);
	}

	public function test(){
		return redirect(['action'=>'index','get'=>['is_redirect'=>'oui']]);
	}
}
