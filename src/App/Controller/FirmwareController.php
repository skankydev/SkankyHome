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

use App\Form\FirmwareForm;
use App\Job\SendFirmwareJob;
use App\Model\Document\Firmware;
use App\Model\Document\Module;
use App\Model\FirmwareCollection;
use App\Utilities\Upload;
use SkankyDev\Controller\MasterController;
use SkankyDev\Http\Request;
use SkankyDev\Queue\Queue;

class FirmwareController extends MasterController {

	public function create(Module $module){
		$form = new FirmwareForm(['action' => 'store','params'=>['module'=>$module->_id]],'POST',['enctype'=>'multipart/form-data']);
		$firmware = new Firmware();
		$firmware->module_id = $module->_id;
		$form->setData($firmware);
		return view('firmware.create', [
			'form' => $form,
			'module' => $module,
		]);
	}

	public function store(Request $request,Module $module){
		$input = $request->input();
		$file = $request->file();
		$form = new FirmwareForm(['action' => 'store']);
		if(!$form->validate($input)){
			return redirect(['action' => 'create','params' => ['module',$module->_id]])->withErrors($form->getErrors())->withInput($input);
		}
		if (!isset($file['firmware'])) {
			return redirect(['action' => 'create', 'params' => ['module', $module->_id]])->withFlash('error', 'Aucun fichier uploadé')->withInput($input);
		}
		
		$upload = new Upload($file['firmware'], UPLOAD_FOLDER .DS. 'firmware'.DS);
		$upload->setFilename('firmware_' . $module->_id . '_' . date('YmdHis') . '.bin');

		if (!$upload->upload()) {
			return redirect(['action' => 'create', 'params' => ['module', $module->_id]])->withFlash('error', implode(', ', $upload->getErrors()));
		}

		$input['file'] = $upload->getFileInfo();
		$firmware = new Firmware($input);

		FirmwareCollection::_save($firmware);
		return redirect(['controller'=>'module', 'action' => 'show', 'params' => ['module'=>$module->_id]])->withFlash('success', 'Firmware uploadé avec succès');
	}

	public function delete(Firmware $firmware){
		$filepath = PUBLIC_FOLDER.$firmware->file['path'];
		unlink($filepath);
		$module_id = $firmware->module_id;
		FirmwareCollection::_deleteOne($firmware);
		return redirect(['controller'=>'module', 'action' => 'show', 'params' => ['module'=>$module_id]])->withFlash('success', 'Suppression réussie');
	}

	public function send(Module $module, Firmware $firmware){
		Queue::push(new SendFirmwareJob($module,$firmware));
		return redirect(['controller'=>'module', 'action' => 'show', 'params' => ['module'=>$module->_id]])->withFlash('success', 'Firmware Envoyer avec succès');
	}
}
