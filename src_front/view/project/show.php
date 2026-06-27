<?php 
use App\Model\Enum\TaskStatus;

$this->setLayout('layout.default');
$this->addCrumb('Project',['action'=>'index'],'icon-sunrise');
$this->addCrumb($project->name,['action'=>'show','params'=>['project'=>$project->_id]],$project->icon);
?>


<header class="page-header">
	<div class="page-title">
		<h2 class="rainbow-icon">
			<i class="<?= e($project->icon) ?>"></i>
			<?= e($project->name) ?>  
		</h2>
	</div>
	<div class="page-action">
		<a href="<?= $this->url(['action'=>'edit','params'=>[$project->_id]]) ?>" class="btn btn-primary">
			<i class="icon icon-edit"></i>
			Edit
		</a>
		<a href="<?= $this->url(['action'=>'delete','params'=>[$project->_id]]) ?>" class="btn btn-error"
			data-method="post" data-confirm="Supprimer ce projet ?">
			<i class="icon-delete"></i>
			Delete
		</a>
	</div>
</header>

<section class="page-content grid-layout">
	<div class="grid-half card p-s">
		<dl>
			<dt>Name</dt>
			<dd><?= e($project->name) ?></dd>
			<dt>Status</dt>
			<dd><?= $project->status->pretty() ?></dd>
			<dt>created</dt>
			<dd><?= $project->created_at?->format('d/m/Y H:i') ?></dd>
			<dt>updated</dt>
			<dd><?= $project->updated_at?->format('d/m/Y H:i') ?></dd>
		</dl>
	</div>
	<div class="grid-half card p-s">
		<?= $this->part('part.markdown', ['content' => $project->description]) ?>
	</div>
	<div class="grid-full card p-s">
		<div class="card-header">
			<h3 class="corner-accent-success"><i class="icon-check-square icon-success"></i> Tâches</h3>
		</div>
		<?php foreach ($tasks as $task): ?>
		<details class="table-row">
			<summary class="card-header-action p-s">
				<div>
					<h3 class="">
						<?php if ($task->icon): ?><i class="<?= e($task->icon) ?>"></i> <?php endif; ?> <?= e($task->name) ?>
					</h3>
				</div>
				<div class="js-task-badge">
					<?= $task->status->pretty() ?>
				</div>
			</summary>
			<div class="task-content" data-task-id="<?= $task->_id ?>">

				<div class="p-m">
					<?= $this->part('part.markdown', ['content' => $task->description]) ?>
				</div>
				<div class="card-header-action p-m">
					
					<div class="form-group">
						<label for="Status" class="form-label required">Statut</label>
						<select class="js-task-status" data-url="<?= $this->url(['controller' => 'task', 'action' => 'setStatus', 'params' => [$task->_id]]) ?>">
							<?php foreach (TaskStatus::cases() as $st): ?>
							<option value="<?= $st->value ?>" <?= $task->status === $st ? 'selected' : '' ?>><?= e($st->label()) ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<div>
						<a href="<?= $this->url(['controller' => 'task', 'action' => 'edit', 'params' => [$task->_id]]) ?>" 
							class="btn-mini btn-success"
							data-tooltip="Edit"
							>
							<i class="icon-edit"></i>
						</a>
						<button type="button" 
							data-tooltip="Delete"
							class="btn-mini btn-error js-task-delete" 
							data-url="<?= $this->url(['controller' => 'task', 'action' => 'delete', 'params' => [$task->_id]]) ?>">
							<i class="icon-delete"></i>
						</button>
					</div>
				</div>
			</div>
		</details>
		<?php endforeach; ?>
		<?php if (empty($tasks)): ?>
			<div class="task-empty text-center ">Aucune tâche pour l'instant.</div>
		<?php endif; ?>
		<div class="card-footer">
			<details class="task-add" <?= $taskForm->getErrors() ? 'open' : '' ?>>
				<summary class="card-header-action">
					<h3 class="corner-accent-warning mb-m">
						<i class="icon-add"></i> 
						Ajouter une tâche
					</h3>
				</summary>
				<?= $taskForm->render() ?>
			</details>
		</div>
	</div>
</section>

<script>
(() => {
	// Changement de statut en direct (sans reload).
	document.querySelectorAll('.js-task-status').forEach(sel => {
		sel.addEventListener('change', async () => {
			const res = await fetch(sel.dataset.url, {
				method: 'POST',
				headers: { 
					'Content-Type': 'application/x-www-form-urlencoded',
					'Accept':'application/json'
				},
				body: 'status=' + encodeURIComponent(sel.value),
			});
			const data = await res.json();
			if (data.ok) {
				const badge = sel.closest('details').querySelector('.js-task-badge');
				// On reconstruit le rendu de pretty() : <div class="status-x">Label</div>
				badge.innerHTML = '<div class="' + data.class + '">' + data.label + '</div>';
			}
		});
	});

	// Suppression d'une tâche (sans reload).
	document.querySelectorAll('.js-task-delete').forEach(btn => {
		btn.addEventListener('click', async () => {
			if (!confirm('Supprimer cette tâche ?')) return;
			const res = await fetch(btn.dataset.url, { 
				method: 'POST',
				headers: {'Accept':'application/json'},
			});
			const data = await res.json();
			if (data.ok) btn.closest('details').remove();
		});
	});
})();
</script>