<?php 
$this->setLayout('layout.default');
?>

<header class="page-header">
	<div class="page-title">
		<h2 class="rainbow-icon">
			<i class="<?= e($module->icon) ?>"></i>
			<?= e($module->name) ?>
		</h2>
	</div>
	<div class="page-action">
		<a href="<?= $this->url(['action'=>'edit','params'=>[$module->_id]]) ?>" class="btn btn-primary">
			<i class="icon icon-edit"></i>
			Edit
		</a>
		<a href="<?= $this->url(['action'=>'delete','params'=>[$module->_id]]) ?>" class="btn btn-error">
			<i class="icon-delete"></i>
			delete
		</a>
	</div>
</header>

<section class="grid-layout">
	<div class="grid-half card p-s">
		<dl>
			<dt>Nom</dt>
			<dd><?= e($module->name) ?></dd>
			<dt>Topic de Message</dt>
			<dd><?= e($module->topic_message) ?></dd>
			<dt>Topic de Commende</dt>
			<dd><?= e($module->topic_cmd) ?></dd>
			<dt>Nb line</dt>
			<dd><?= e($module->nb_line) ?></dd>
			<dt>Nb led</dt>
			<dd><?= e($module->nb_led) ?></dd>
			<dt>created</dt>
			<dd><?= $module->created_at?->format('d/m/Y H:i') ?></dd>
			<dt>updated</dt>
			<dd><?= $module->updated_at?->format('d/m/Y H:i') ?></dd>
		</dl>
	</div>
</section>