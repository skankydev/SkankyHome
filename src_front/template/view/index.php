%?php 
$this->setLayout('layout.default');
?>

<header class="page-header">
	<div class="page-title">
		<h2 class="rainbow-icon">
			<i class="icon-zap"></i>
			<?= $name ?> 
		</h2>
	</div>
	<div class="page-action">
		<a href="%?= $this->url(['action'=>'create']) ?>" class="btn btn-primary">
			<i class="icon icon-add"></i>
			Ajouter
		</a>
	</div>
</header>

<section class="page-content">
	<table>
		<thead>
			<tr>
<?php foreach($this->fields as $field): ?>
				<th>%?= $this->link('<?= $this->toHuman($field['name']) ?>',['get'=> $<?= $pluralCamel ?>->sortParams('<?= $field['name'] ?>')]) ?></th>
<?php endforeach; ?>
				<th>%?= $this->link('Updated',['get'=> $<?= $pluralCamel ?>->sortParams('updated_at')]) ?></th>
				<th>%?= $this->link('Created',['get'=> $<?= $pluralCamel ?>->sortParams('created_at')]) ?></th>
				<th class="action"></th>
			</tr>
		</thead>
		<tbody>
		%?php foreach ($<?= $pluralCamel ?> as $<?= $singularCamel ?>): ?>
		<tr class="clickable-row" data-url="%?= $this->url(['action' => 'show','params'=>['<?= $singularCamel ?>'=>$<?= $singularCamel ?>->_id]]) ?>">
<?php foreach($this->fields as $field): ?>
<?php if($field['type'] === 'bool'): ?>
			<td>%?= $<?= $singularCamel ?>-><?= $field['name'] ?> ? '<i class="icon-check text-success"></i>' : '<i class="icon-x text-danger"></i>' ?></td>
<?php elseif($field['type'] === 'date'): ?>
			<td>%?= $<?= $singularCamel ?>-><?= $field['name'] ?> ? $<?= $singularCamel ?>-><?= $field['name'] ?>->format('d/m/Y') : '-' ?></td>
<?php elseif($field['type'] === 'datetime'): ?>
			<td>%?= $<?= $singularCamel ?>-><?= $field['name'] ?> ? $<?= $singularCamel ?>-><?= $field['name'] ?>->format('d/m/Y H:i') : '-' ?></td>
<?php elseif($field['type'] === 'array'): ?>
			<td>%?= count($<?= $singularCamel ?>-><?= $field['name'] ?>) ?></td>
<?php else: ?>
			<td>%?= $<?= $singularCamel ?>-><?= $field['name'] ?> ?></td>
<?php endif; ?>
<?php endforeach; ?>
			<td>%?= $<?= $singularCamel ?>->updated_at?->format('d/m/Y H:i') ?></td>
			<td>%?= $<?= $singularCamel ?>->created_at?->format('d/m/Y H:i') ?></td>
			<td class="action">
				<a href="%?= $this->url(['action' => 'show','params'=>['<?= $singularCamel ?>'=>$<?= $singularCamel ?>->_id]]) ?>" class="btn-mini btn-info"><i class="icon-info"></i></a>
				<a href="%?= $this->url(['action' => 'edit','params'=>['<?= $singularCamel ?>'=>$<?= $singularCamel ?>->_id]]) ?>" class="btn-mini btn-warning"><i class="icon-edit"></i></a>
			</td>
		</tr>
		%?php endforeach ?>
		</tbody>
	</table>

	%?= $this->part('part.paginator', $<?= $pluralCamel ?>->getOption()); ?>
</section>