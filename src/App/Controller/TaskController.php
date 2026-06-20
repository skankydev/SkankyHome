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

use App\Form\TaskForm;
use App\Model\Document\Task;
use App\Model\TaskCollection;
use SkankyDev\Controller\MasterController;
use SkankyDev\Http\Request;

class TaskController extends MasterController {

	public function index(TaskCollection $collection){
		$tasks = $collection->paginate([], Request::_paginateInfo());
		return view('task.index', ['tasks' => $tasks]);
	}

	public function create(){
		$form = new TaskForm(['action' => 'store']);
		return view('task.create', ['form' => $form]);
	}

	public function store(Request $request){
		$input = $request->input();
		$form = new TaskForm(['action' => 'store']);
		if(!$form->validate($input)){
			return redirect(['action' => 'create'])->withErrors($form->getErrors())->withInput($input);
		}
		$task = new Task($input);
		TaskCollection::_save($task);
		return redirect(['action' => 'show', 'params' => [$task->_id]])->withFlash('success', 'Enregistrement réussi');
	}

	public function show(Request $request, Task $task){
		return view('task.show', ['task' => $task]);
	}

	public function edit(Task $task){
		$form = new TaskForm(['action' => 'update', 'params' => [$task->_id]]);
		$form->setData($task);
		return view('task.edit', ['form' => $form, 'task' => $task]);
	}

	public function update(Request $request, Task $task){
		$input = $request->input();
		$form = new TaskForm(['action' => 'update', 'params' => [$task->_id]]);
		if(!$form->validate($input)){
			return redirect(['action' => 'update', 'params' => [$task->_id]])->withErrors($form->getErrors())->withInput($input);
		}
		$task->fill($input);
		TaskCollection::_save($task);
		return redirect(['action' => 'show', 'params' => [$task->_id]])->withFlash('success', 'Modification réussie');
	}

	public function delete(Task $task){
		TaskCollection::_deleteOne($task);
		return redirect(['action' => 'index'])->withFlash('success', 'Suppression réussie');
	}
}