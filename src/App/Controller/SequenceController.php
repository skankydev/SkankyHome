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
use SkankyDev\Http\Routing\Router;
use App\Form\SequenceForm;

class SequenceController extends MasterController {

	public function index(){
		
		return view('sequence.index');
	}

	public function create(){
		$form = new SequenceForm(['action'=>'store']);

		return view('sequence.create',['form'=>$form]);
	}

	public function store(Request $request){
		$input = $request->input();
		dd($input);
	}
}
