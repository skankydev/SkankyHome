<?php 
$this->setLayout('layout.default');
?>

<header class="page-header">
	<div class="page-title">
		<h2 class="rainbow-icon">
			<i class="<?= e($sequence->icon) ?>"></i>
			<?= e($sequence->name) ?>
		</h2>
	</div>
	<div class="page-action">
		<a href="<?= $this->url(['action'=>'edit','params'=>[$sequence->_id]]) ?>" class="btn btn-primary">
			<i class="icon icon-edit"></i>
			Edit
		</a>
		<a href="<?= $this->url(['action'=>'delete','params'=>[$sequence->_id]]) ?>" class="btn btn-error">
			<i class="icon-delete"></i>
			delete
		</a>
	</div>
</header>

<section class="grid-layout">
	<div class="grid-half card p-s">
		<dl>
			<dt>Name</dt>
			<dd><?= e($sequence->name) ?></dd>
			<dt>Created</dt>
			<dd><?= $sequence->created_at?->format('d/m/Y H:i') ?></dd>
			<dt>Updated</dt>
			<dd><?= $sequence->updated_at?->format('d/m/Y H:i') ?></dd>
		</dl>
	</div>
</section>