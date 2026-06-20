<?php 
$this->setLayout('layout.default');
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
		<a href="<?= $this->url(['action'=>'delete','params'=>[$project->_id]]) ?>" class="btn btn-error">
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
			<dt>Description</dt>
			<dd><?= e($project->description) ?></dd>
			<dt>Status</dt>
			<dd><?= e($project->status->label()) ?></dd>
			<dt>created</dt>
			<dd><?= $project->created_at?->format('d/m/Y H:i') ?></dd>
			<dt>updated</dt>
			<dd><?= $project->updated_at?->format('d/m/Y H:i') ?></dd>
		</dl>
	</div>
</section>