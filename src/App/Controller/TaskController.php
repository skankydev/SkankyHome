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
use App\Model\Document\Project;
use App\Model\Document\Task;
use App\Model\Enum\TaskStatus;
use App\Model\TaskCollection;
use SkankyDev\Controller\MasterController;
use SkankyDev\Http\Request;

class TaskController extends MasterController {

	public function index(TaskCollection $collection){
		$tasks = $collection->paginate([], Request::_paginateInfo());
		return view('task.index', ['tasks' => $tasks]);
	}


	public function store(Request $request, Project $project){
		$input = $request->input();
		$input['project_id'] = (string) $project->_id;

		$form = new TaskForm(['controller'=>'project','action' => 'store', 'params' => [$project->_id]]);
		if(!$form->validate($input)){
			return redirect(['controller'=>'project','action' => 'show', 'params' => [$project->_id]])
				->withErrors($form->getErrors())->withInput($input);
		}

		$task = new Task($input);
		TaskCollection::_save($task);
		return redirect(['controller'=>'project','action' => 'show', 'params' => [$project->_id]])
			->withFlash('success', 'Tâche ajoutée');
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
			return redirect(['action' => 'edit', 'params' => [$task->_id]])->withErrors($form->getErrors())->withInput($input);
		}
		$task->fill($input);
		TaskCollection::_save($task);
		return redirect(['controller'=>'project','action' => 'show', 'params' => [$task->project_id]])->withFlash('success', 'Modification réussie');
	}


	public function setStatus(Request $request, Task $task){
		$status = TaskStatus::tryFrom((string) $request->input('status'));
		if($status === null){
			return response(['ok' => false, 'message' => 'Statut invalide']);
		}
		$task->status = $status;
		TaskCollection::_save($task);
		return response([
			'ok'    => true,
			'value' => $status->value,
			'label' => $status->label(),
			'class' => $status->class(),
		]);
	}

	public function remove(Request $request, Task $task){
		TaskCollection::_deleteOne($task);
		return response(['ok' => true]);
	}
}