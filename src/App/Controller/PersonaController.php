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

use App\Form\PersonaForm;
use App\Model\Document\Persona;
use App\Model\PersonaCollection;
use App\Utilities\Upload;
use SkankyDev\Config\Config;
use SkankyDev\Controller\MasterController;
use SkankyDev\Http\Request;

class PersonaController extends MasterController {

	public function index(PersonaCollection $collection){
		$personas = $collection->paginate([], Request::_paginateInfo());
		return view('persona.index', ['personas' => $personas]);
	}

	public function create(){
		$form = new PersonaForm(['action' => 'store'],'POST',['enctype'=>'multipart/form-data']);
		return view('persona.create', ['form' => $form]);
	}

	public function store(Request $request){
		$input = $request->input();
		$form = new PersonaForm(['action' => 'store']);
		if(!$form->validate($input)){
			return redirect(['action' => 'create'])->withErrors($form->getErrors())->withInput($input);
		}
		$persona = new Persona($input);
		$file = $request->file();

		if (isset($file['img'])) {
			$upload = new Upload($file['img'], UPLOAD_FOLDER .DS. 'img'.DS);
			if (!$upload->upload()) {
				return redirect(['action' => 'create'])->withFlash('error', implode(', ', $upload->getErrors()));
			}
			$persona->img_info = $upload->getFileInfo();
		}

		PersonaCollection::_save($persona);
		return redirect(['action' => 'show', 'params' => [$persona->_id]])->withFlash('success', 'Enregistrement réussi');
	}

	public function show(Request $request, Persona $persona){
		return view('persona.show', ['persona' => $persona]);
	}

	public function chat(Persona $persona){
		$conf = Config::get('llama');
		$llamaUrl = 'http://'.$conf['host'].':'.$conf['port'];
		return view('persona.chat', ['persona' => $persona, 'llamaUrl' => $llamaUrl]);
	}

	public function edit(Persona $persona){
		$form = new PersonaForm(['action' => 'update', 'params' => [$persona->_id]],'POST',['enctype'=>'multipart/form-data']);


		$form->setData($persona);
		return view('persona.edit', ['form' => $form, 'persona' => $persona]);
	}

	public function update(Request $request, Persona $persona){
		$input = $request->input();
		$form = new PersonaForm(['action' => 'update', 'params' => [$persona->_id]]);
		if(!$form->validate($input)){
			return redirect(['action' => 'update', 'params' => [$persona->_id]])->withErrors($form->getErrors())->withInput($input);
		}
		$persona->fill($input);

		$file = $request->file();
		//dd($file);
		if (isset($file['img'])) {
			$upload = new Upload($file['img'], UPLOAD_FOLDER .DS. 'img'.DS);
			if (!$upload->upload()) {
				return redirect(['action' => 'edit', 'params' => ['persona', $module->_id]])->withFlash('error', implode(', ', $upload->getErrors()));
			}
			$persona->img_info = $upload->getFileInfo();
		}

		PersonaCollection::_save($persona);
		return redirect(['action' => 'show', 'params' => [$persona->_id]])->withFlash('success', 'Modification réussie');
	}

	public function delete(Persona $persona){
		PersonaCollection::_deleteOne($persona);
		return redirect(['action' => 'index'])->withFlash('success', 'Suppression réussie');
	}
}