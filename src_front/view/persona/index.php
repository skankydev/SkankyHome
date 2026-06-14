<?php 
$this->setLayout('layout.default');
?>

<header class="page-header">
	<div class="page-title">
		<h2 class="rainbow-icon">
			<i class="icon-zap"></i>
			Persona 
		</h2>
	</div>
	<div class="page-action">
		<a href="<?= $this->url(['action'=>'create']) ?>" class="btn btn-primary">
			<i class="icon icon-add"></i>
			Ajouter
		</a>
	</div>
</header>

<section class="page-content">
	<table>
		<thead>
			<tr>
				<th><?= $personas->sortLink('name', 'Name') ?></th>
				<th><?= $personas->sortLink('updated_at', 'Updated') ?></th>
				<th><?= $personas->sortLink('created_at', 'Created') ?></th>
				<th class="action"></th>
			</tr>
		</thead>
		<tbody>
		<?php foreach ($personas as $persona): ?>
		<tr class="clickable-row" data-url="<?= $this->url(['action' => 'show','params'=>['persona'=>$persona->_id]]) ?>">
			<td><?= $persona->name ?></td>
			<td><?= $persona->updated_at?->format('d/m/Y H:i') ?></td>
			<td><?= $persona->created_at?->format('d/m/Y H:i') ?></td>
			<td class="action">
				<a href="<?= $this->url(['action' => 'show','params'=>['persona'=>$persona->_id]]) ?>" class="btn-mini btn-info"><i class="icon-info"></i></a>
				<a href="<?= $this->url(['action' => 'edit','params'=>['persona'=>$persona->_id]]) ?>" class="btn-mini btn-warning"><i class="icon-edit"></i></a>
			</td>
		</tr>
		<?php endforeach ?>
		</tbody>
	</table>

	<?= $this->part('part.paginator', $personas->getOption()); ?>
</section>