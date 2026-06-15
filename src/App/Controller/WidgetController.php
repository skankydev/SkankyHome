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

use App\Form\WidgetForm;
use App\Model\Document\Widget;
use App\Model\WidgetCollection;
use SkankyDev\Controller\MasterController;
use SkankyDev\Http\Request;

class WidgetController extends MasterController {

	public function index(WidgetCollection $collection){
		$widgets = $collection->paginate([], Request::_paginateInfo());
		return view('widget.index', ['widgets' => $widgets]);
	}

	public function create(){
		$form = new WidgetForm(['action' => 'store']);
		return view('widget.create', ['form' => $form]);
	}

	public function store(Request $request){
		$input = $request->input();
		$form = new WidgetForm(['action' => 'store']);
		if(!$form->validate($input)){
			return redirect(['action' => 'create'])->withErrors($form->getErrors())->withInput($input);
		}
		$widget = new Widget($input);
		WidgetCollection::_save($widget);
		return redirect(['action' => 'show', 'params' => [$widget->_id]])->withFlash('success', 'Enregistrement réussi');
	}

	public function show(Request $request, Widget $widget){
		return view('widget.show', ['widget' => $widget]);
	}

	public function edit(Widget $widget){
		$form = new WidgetForm(['action' => 'update', 'params' => [$widget->_id]]);
		$form->setData($widget);
		return view('widget.edit', ['form' => $form, 'widget' => $widget]);
	}

	public function update(Request $request, Widget $widget){
		$input = $request->input();
		$form = new WidgetForm(['action' => 'update', 'params' => [$widget->_id]]);
		if(!$form->validate($input)){
			return redirect(['action' => 'update', 'params' => [$widget->_id]])->withErrors($form->getErrors())->withInput($input);
		}
		$widget->fill($input);
		WidgetCollection::_save($widget);
		return redirect(['action' => 'show', 'params' => [$widget->_id]])->withFlash('success', 'Modification réussie');
	}

	public function delete(Widget $widget){
		WidgetCollection::_deleteOne($widget);
		return redirect(['action' => 'index'])->withFlash('success', 'Suppression réussie');
	}
}