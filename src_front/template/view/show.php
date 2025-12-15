%?php 
$this->setLayout('layout.default');
?>

<header class="page-header">
	<div class="page-title">
		<h2 class="rainbow-icon">
			<i class="icon-zap"></i>
			%?= e($<?= $singularCamel ?>-><?= $this->fields[0]['name'] ?>) ?>
		</h2>
	</div>
	<div class="page-action">
		<a href="%?= $this->url(['action'=>'edit','params'=>[$<?= $singularCamel ?>->_id]]) ?>" class="btn btn-primary">
			<i class="icon icon-edit"></i>
			Edit
		</a>
		<a href="%?= $this->url(['action'=>'delete','params'=>[$<?= $singularCamel ?>->_id]]) ?>" class="btn btn-error">
			<i class="icon-delete"></i>
			delete
		</a>
	</div>
</header>

<section class="grid-layout">
	<div class="grid-half card p-s">
		<dl>
<?php foreach($this->fields as $field): ?>
			<dt><?= $this->toHuman($field['name']) ?></dt>
<?php if($field['type'] === 'bool'): ?>
			<dd><span class="text-%?= $<?= $singularCamel ?>-><?= $field['name'] ?> ? 'success' : 'error' ?>">%?= $<?= $singularCamel ?>-><?= $field['name'] ?> ? 'oui' : 'non' ?></span></dd>
<?php elseif($field['type'] === 'date'): ?>
			<dd>%?= $<?= $singularCamel ?>-><?= $field['name'] ?> ? $<?= $singularCamel ?>-><?= $field['name'] ?>->format('d/m/Y') : '-' ?></dd>
<?php elseif($field['type'] === 'datetime'): ?>
			<dd>%?= $<?= $singularCamel ?>-><?= $field['name'] ?> ? $<?= $singularCamel ?>-><?= $field['name'] ?>->format('d/m/Y H:i') : '-' ?></dd>
<?php elseif($field['type'] === 'array'): ?>
			<dd>%?= !empty($<?= $singularCamel ?>-><?= $field['name'] ?>) ? implode(', ', $<?= $singularCamel ?>-><?= $field['name'] ?>) : '-' ?></dd>
<?php else: ?>
			<dd>%?= e($<?= $singularCamel ?>-><?= $field['name'] ?>) ?></dd>
<?php endif; ?>
<?php endforeach; ?>
			<dt>created</dt>
			<dd>%?= $<?= $singularCamel ?>->created_at?->format('d/m/Y H:i') ?></dd>
			<dt>updated</dt>
			<dd>%?= $<?= $singularCamel ?>->updated_at?->format('d/m/Y H:i') ?></dd>
		</dl>
	</div>
</section>