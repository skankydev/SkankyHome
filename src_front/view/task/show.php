<?php 
$this->setLayout('layout.default');
?>

<header class="page-header">
	<div class="page-title">
		<h2 class="rainbow-icon">
			<i class="icon-zap"></i>
			<?= e($task->name) ?>
		</h2>
	</div>
	<div class="page-action">
		<a href="<?= $this->url(['action'=>'edit','params'=>[$task->_id]]) ?>" class="btn btn-primary">
			<i class="icon icon-edit"></i>
			Edit
		</a>
		<a href="<?= $this->url(['action'=>'delete','params'=>[$task->_id]]) ?>" class="btn btn-error">
			<i class="icon-delete"></i>
			Delete
		</a>
	</div>
</header>

<section class="page-content grid-layout">
	<div class="grid-half card p-s">
		<dl>
			<dt>Projet</dt>
			<dd><?= e($task->project?->name ?? '—') ?></dd>
			<dt>Name</dt>
			<dd><?= e($task->name) ?></dd>
			<dt>Description</dt>
			<dd><?= e($task->description) ?></dd>
			<dt>Status</dt>
			<dd><?= e($task->status->label()) ?></dd>
			<dt>created</dt>
			<dd><?= $task->created_at?->format('d/m/Y H:i') ?></dd>
			<dt>updated</dt>
			<dd><?= $task->updated_at?->format('d/m/Y H:i') ?></dd>
		</dl>
	</div>
</section>