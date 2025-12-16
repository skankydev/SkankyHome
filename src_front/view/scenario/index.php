<?php 
$this->setLayout('layout.default');
?>

<header class="page-header">
	<div class="page-title">
		<h2 class="rainbow-icon">
			<i class="icon-zap"></i>
			Scenario
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
				<th><?= $this->link('Name',['get'=> $scenarios->sortParams('name')]) ?></th>
				<th><?= $this->link('Icon',['get'=> $scenarios->sortParams('icon')]) ?></th>
				<th><?= $this->link('Module Id',['get'=> $scenarios->sortParams('module_id')]) ?></th>
				<th><?= $this->link('Updated',['get'=> $scenarios->sortParams('updated_at')]) ?></th>
				<th><?= $this->link('Created',['get'=> $scenarios->sortParams('created_at')]) ?></th>
				<th class="action"></th>
			</tr>
		</thead>
		<tbody>
		<?php foreach ($scenarios as $scenario): ?>
		<tr class="clickable-row" href="<?= $this->url(['action' => 'show','params'=>['scenario'=>$scenario->_id]]) ?>">
			<td><?= $scenario->name ?></td>
			<td><?= $scenario->icon ?></td>
			<td><?= $scenario->module_id ?></td>
			<td><?= $scenario->updated_at?->format('d/m/Y H:i') ?></td>
			<td><?= $scenario->created_at?->format('d/m/Y H:i') ?></td>
			<td class="action">
				<a href="<?= $this->url(['action' => 'show','params'=>['scenario'=>$scenario->_id]]) ?>" class="btn-mini btn-info"><i class="icon-info"></i></a>
				<a href="<?= $this->url(['action' => 'edit','params'=>['scenario'=>$scenario->_id]]) ?>" class="btn-mini btn-warning"><i class="icon-edit"></i></a>
			</td>
		</tr>
		<?php endforeach ?>
		</tbody>
	</table>

	<?= $this->part('part.paginator', $scenarios->getOption()); ?>
</section>