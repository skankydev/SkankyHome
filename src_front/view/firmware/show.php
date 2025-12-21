<?php 
$this->setLayout('layout.default');
?>

<header class="page-header">
	<div class="page-title">
		<h2 class="rainbow-icon">
			<i class="icon-zap"></i>
			<?= e($firmware->name) ?> 
		</h2>
	</div>
	<div class="page-action">
		<a href="<?= $this->url(['action'=>'edit','params'=>[$firmware->_id]]) ?>" class="btn btn-primary">
			<i class="icon icon-edit"></i>
			Edit
		</a>
		<a href="<?= $this->url(['action'=>'delete','params'=>[$firmware->_id]]) ?>" class="btn btn-error">
			<i class="icon-delete"></i>
			Delete
		</a>
	</div>
</header>

<section class="page-content grid-layout">
	<div class="grid-half card p-s">
		<dl>
			<dt>Name</dt>
			<dd><?= e($firmware->name) ?></dd>
			<dt>Version</dt>
			<dd><?= e($firmware->version) ?></dd>
			<dt>created</dt>
			<dd><?= $firmware->created_at?->format('d/m/Y H:i') ?></dd>
			<dt>updated</dt>
			<dd><?= $firmware->updated_at?->format('d/m/Y H:i') ?></dd>
		</dl>
	</div>
</section>