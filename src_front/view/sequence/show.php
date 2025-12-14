<?php 
$this->setLayout('layout.default');
?>

<header class="page-header">
	<div class="page-title">
		<h2 class="rainbow-icon">
			<i class="icon-zap"></i>
			<?= $sequence->name ?>
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
			<dt>name</dt><dd><?= e($sequence->name) ?></dd>
			<dt>color</dt><dd><?= e($sequence->color) ?></dd>
			<dt>duration</dt><dd><?= e($sequence->duration) ?></dd>
			<dt>active</dt><dd><span class="text-<?= $sequence->active ?'success':'error' ?>"><?= $sequence->active ?'oui':'non' ?></span></dd>
			<dt>effect</dt><dd><?= e($sequence->effect) ?></dd>
		</dl>
	</div>
	
</section>





