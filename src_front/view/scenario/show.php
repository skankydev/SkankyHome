<?php 
$this->setLayout('layout.default');
?>

<header class="page-header">
	<div class="page-title">
		<h2 class="rainbow-icon">
			<i class="icon-zap"></i>
			<?= e($scenario->name) ?>
		</h2>
	</div>
	<div class="page-action">
		<a href="<?= $this->url(['action'=>'edit','params'=>[$scenario->_id]]) ?>" class="btn btn-primary">
			<i class="icon icon-edit"></i>
			Edit
		</a>
		<a href="<?= $this->url(['action'=>'delete','params'=>[$scenario->_id]]) ?>" class="btn btn-error">
			<i class="icon-delete"></i>
			delete
		</a>
	</div>
</header>

<section class="grid-layout">
	<div class="grid-half card p-s">
		<dl>
			<dt>Name</dt>
			<dd><?= e($scenario->name) ?></dd>
			<dt>Icon</dt>
			<dd><?= e($scenario->icon) ?></dd>
			<dt>Module Id</dt>
			<dd><?= e($scenario->module_id) ?></dd>
			<dt>created</dt>
			<dd><?= $scenario->created_at?->format('d/m/Y H:i') ?></dd>
			<dt>updated</dt>
			<dd><?= $scenario->updated_at?->format('d/m/Y H:i') ?></dd>
		</dl>
	</div>
</section>