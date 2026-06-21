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

use App\Form\ProjectForm;
use App\Form\TaskForm;
use App\Model\Document\Project;
use App\Model\Document\Task;
use App\Model\ProjectCollection;
use App\Model\TaskCollection;
use SkankyDev\Controller\MasterController;
use SkankyDev\Http\Request;

class ProjectController extends MasterController {

	public function index(ProjectCollection $collection){
		$projects = $collection->paginate([], Request::_paginateInfo());
		return view('project.index', ['projects' => $projects]);
	}

	public function create(){
		$form = new ProjectForm(['action' => 'store']);
		return view('project.create', ['form' => $form]);
	}

	public function store(Request $request){
		$input = $request->input();
		$form = new ProjectForm(['action' => 'store']);
		if(!$form->validate($input)){
			return redirect(['action' => 'create'])->withErrors($form->getErrors())->withInput($input);
		}
		$project = new Project($input);
		ProjectCollection::_save($project);
		return redirect(['action' => 'show', 'params' => [$project->_id]])->withFlash('success', 'Enregistrement réussi');
	}

	public function show(Request $request, Project $project){
		$tasks = TaskCollection::_find(
			['project_id' => $project->_id],
			['sort' => ['created_at' => 1]]
		);
		$taskForm = new TaskForm(['controller'=>'task','action' => 'store', 'params' => [$project->_id]]);
		return view('project.show', [
			'project'  => $project,
			'tasks'    => $tasks,
			'taskForm' => $taskForm,
		]);
	}

	/**
	 * Ajout rapide d'une tâche au projet (le project_id vient de l'URL, pas du form).
	 */
	public function addTask(Request $request, Project $project){
		$input = $request->input();
		$input['project_id'] = (string) $project->_id;

		$form = new TaskQuickForm(['action' => 'addTask', 'params' => [$project->_id]]);
		if(!$form->validate($input)){
			return redirect(['action' => 'show', 'params' => [$project->_id]])
				->withErrors($form->getErrors())->withInput($input);
		}

		$task = new Task($input);
		TaskCollection::_save($task);
		return redirect(['action' => 'show', 'params' => [$project->_id]])
			->withFlash('success', 'Tâche ajoutée');
	}

	public function edit(Project $project){
		$form = new ProjectForm(['action' => 'update', 'params' => [$project->_id]]);
		$form->setData($project);
		return view('project.edit', ['form' => $form, 'project' => $project]);
	}

	public function update(Request $request, Project $project){
		$input = $request->input();
		$form = new ProjectForm(['action' => 'update', 'params' => [$project->_id]]);
		if(!$form->validate($input)){
			return redirect(['action' => 'update', 'params' => [$project->_id]])->withErrors($form->getErrors())->withInput($input);
		}
		$project->fill($input);
		ProjectCollection::_save($project);
		return redirect(['action' => 'show', 'params' => [$project->_id]])->withFlash('success', 'Modification réussie');
	}

	public function delete(Project $project){
		ProjectCollection::_deleteOne($project);
		return redirect(['action' => 'index'])->withFlash('success', 'Suppression réussie');
	}
}