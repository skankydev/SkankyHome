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
		debug(new \DateTime);
		$sequences = $collection->paginate([],Request::_paginateInfo());
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
		return redirect(['action' => 'show','params'=>[$sequence->_id]])->withFlash('success','ca marche');
	}

	public function show(Request $request, Sequence $sequence){
		return view('sequence.show',['sequence'=>$sequence]);
	}

	public function edit(Sequence $sequence){
		$form = new SequenceForm(['action'=>'update','params'=>[$sequence->_id]]);
		$form->setData($sequence);
		return view('sequence.edit',['form'=>$form,'sequence'=>$sequence]);
	}

	public function update(Request $request, Sequence $sequence){
		$input = $request->input();
		$form = new SequenceForm(['action'=>'update','params'=>[$sequence->_id]]);
		//$form->setData($sequence);
		if(!$form->validate($input)){
			return redirect(['action'=>'update','params'=>[$sequence->_id]])->withErrors($form->getErrors())->withInput($input);
		}

		$sequence->name = $input['name'];
		$sequence->color = $input['color'];
		$sequence->duration = $input['duration'];
		$sequence->effect = $input['effect'];
		SequenceCollection::_save($sequence);

		return redirect(['action' => 'show','params'=>[$sequence->_id]])->withFlash('success','ca marche');
	}

	public function delete(Sequence $sequence){
		SequenceCollection::_deleteOne($sequence->_id);
		return redirect(['action' => 'index',])->withFlash('success','ca marche');
	}
}
