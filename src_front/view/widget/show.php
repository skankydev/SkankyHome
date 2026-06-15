<?php 
$this->setLayout('layout.default');
?>

<header class="page-header">
	<div class="page-title">
		<h2 class="rainbow-icon">
			<i class="icon-zap"></i>
			<?= e($widget->target_collection) ?>  
		</h2>
	</div>
	<div class="page-action">
		<a href="<?= $this->url(['action'=>'edit','params'=>[$widget->_id]]) ?>" class="btn btn-primary">
			<i class="icon icon-edit"></i>
			Edit
		</a>
		<a href="<?= $this->url(['action'=>'delete','params'=>[$widget->_id]]) ?>" class="btn btn-error">
			<i class="icon-delete"></i>
			Delete
		</a>
	</div>
</header>

<section class="page-content grid-layout">
	<div class="grid-half card p-s">
		<dl>
			<dt>Target Collection</dt>
			<dd><?= e($widget->target_collection) ?></dd>
			<dt>Target Id</dt>
			<dd><?= e($widget->target_id) ?></dd>
			<dt>Position</dt>
			<dd><?= e($widget->position) ?></dd>
			<dt>created</dt>
			<dd><?= $widget->created_at?->format('d/m/Y H:i') ?></dd>
			<dt>updated</dt>
			<dd><?= $widget->updated_at?->format('d/m/Y H:i') ?></dd>
		</dl>
	</div>
</section>