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

use App\Form\ModuleForm;
use App\Job\SendCommandJob;
use App\Model\Document\Module;
use App\Model\FirmwareCollection;
use App\Model\ModuleCollection;
use SkankyDev\Controller\MasterController;
use SkankyDev\Http\Request;
use SkankyDev\Queue\Queue;

class ModuleController extends MasterController {

	public function index(ModuleCollection $collection){
		$modules = $collection->paginate([], Request::_paginateInfo());
		return view('module.index', ['modules' => $modules]);
	}

	public function create(){
		$form = new ModuleForm(['action' => 'store']);
		return view('module.create', ['form' => $form]);
	}

	public function store(Request $request){
		$input = $request->input();
		$form = new ModuleForm(['action' => 'store']);
		if(!$form->validate($input)){
			return redirect(['action' => 'create'])->withErrors($form->getErrors())->withInput($input);
		}
		$module = new Module($input);
		ModuleCollection::_save($module);
		return redirect(['action' => 'show', 'params' => [$module->_id]])->withFlash('success', 'Enregistrement réussi');
	}

	public function show(Module $module){
		$firmwares = FirmwareCollection::_find(['module_id'=>$module->_id],['limit' =>10]);

		return view('module.show', [
			'module'    => $module,
			'firmwares' => $firmwares,
		]);
	}

	public function edit(Module $module){
		$form = new ModuleForm(['action' => 'update', 'params' => [$module->_id]]);
		$form->setData($module);
		return view('module.edit', ['form' => $form, 'module' => $module]);
	}

	public function update(Request $request, Module $module){
		$input = $request->input();
		$form = new ModuleForm(['action' => 'update', 'params' => [$module->_id]]);
		if(!$form->validate($input)){
			return redirect(['action' => 'edit', 'params' => [$module->_id]])->withErrors($form->getErrors())->withInput($input);
		}
		$module->fill($input);
		ModuleCollection::_save($module);
		return redirect(['action' => 'show', 'params' => [$module->_id]])->withFlash('success', 'Modification réussie');
	}

	public function delete(Module $module){
		ModuleCollection::_deleteOne($module);
		return redirect(['action' => 'index'])->withFlash('success', 'Suppression réussie');
	}

	public function reboot(Module $module){
		Queue::push(new SendCommandJob($module, 'reboot'));
		return redirect(['action' => 'show', 'params' => [$module->_id]])->withFlash('success', 'Commande reboot envoyée');
	}
}