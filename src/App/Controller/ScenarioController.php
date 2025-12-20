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

use App\Form\ScenarioForm;
use App\Model\Document\Module;
use App\Model\Document\Scenario;
use App\Model\ModuleCollection;
use App\Model\ScenarioCollection;
use SkankyDev\Config\Config;
use SkankyDev\Controller\MasterController;
use SkankyDev\Http\Request;
use SkankyDev\Http\UrlBuilder;

class ScenarioController extends MasterController {

	public function index(ScenarioCollection $collection){
		$scenarios = $collection->paginate([], Request::_paginateInfo());
		return view('scenario.index', ['scenarios' => $scenarios]);
	}

	public function create(Module $module){
		
		$scenario = new Scenario;
		$scenario->module_id = $module->id;
		$icons = Config::get('icons');
		$effects = Config::get('leds.effects');
		return view('scenario.create', [
			'scenario' => $scenario,
			'module' => $module,
			'icons' => $icons,
			'effects' => $effects,
		]);
	}

	public function edit(Scenario $scenario){
		$module = ModuleCollection::_findById($scenario->module_id);
		$icons = Config::get('icons');
		$effects = Config::get('leds.effects');
		return view('scenario.create', [
			'scenario' => $scenario,
			'module' => $module,
			'icons' => $icons,
			'effects' => $effects,
		]);
	}

	public function show(Request $request, Scenario $scenario){
		return view('scenario.show', ['scenario' => $scenario]);
	}

	public function save(Request $request){
		$input = $request->input();
		$isNew = (bool)!$input['_id'];
		if($isNew){
			$scenario = new Scenario;
		}else{
			$scenario = ScenarioCollection::_findById($input['_id']);
		}

		$scenario->fill($input);
		ScenarioCollection::_save($scenario);

		return view('scenario.create', [
			'scenario' => $scenario,
			'result' => [
				'status' => 'success',
				'message' => 'Enregistrement réussi',
				'url' => UrlBuilder::_build(['action'=>'edit','parmas'=>['scenario'=>$scenario]]),
				'isNew' => $isNew,
				'display' => true,
			],
		]);
	}

	public function delete(Scenario $scenario){
		ScenarioCollection::_deleteOne($scenario);
		return redirect(['action' => 'index'])->withFlash('success', 'Suppression réussie');
	}
}