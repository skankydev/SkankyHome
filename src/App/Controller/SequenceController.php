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

use App\Form\SequenceForm;
use App\Model\Document\Sequence;
use App\Model\SequenceCollection;
use SkankyDev\Controller\MasterController;
use SkankyDev\Http\Request;
use SkankyDev\Http\Routing\Router;

class SequenceController extends MasterController {

	public function index(SequenceCollection $collection){

		$sequences = $collection->paginate([],Request::_paginateInfo(1));
		return view('sequence.index',['sequences'=>$sequences]);
	
	}

	public function create(){
		$form = new SequenceForm(['action'=>'store']);

		return view('sequence.create',['form'=>$form]);
	}

	public function store(Request $request){
		$input = $request->input();
		$form = new SequenceForm(['action'=>'store']);
		if(!$form->validate($input)){
			return redirect(['action' => 'create'])->withErrors($form->getErrors())->withInput($input);
		}
		$sequence = new Sequence($input);
		SequenceCollection::_save($sequence);
		return redirect(['action' => 'show','params'=>['sequence'=>$sequence->_id]])->withFlash('success','ca marche');
	}

	public function show(Request $request, Sequence $sequence){
		return view('sequence.show',['sequence'=>$sequence]);
	}
}
