<?php 
$this->setLayout('layout.default');
?>

<header class="page-header">
	<div class="page-title">
		<h2 class="rainbow-icon">
			<i class="icon-zap"></i>
			Firmware
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
				<th><?= $this->link('Name',['get'=> $firmwares->sortParams('name')]) ?></th>
				<th><?= $this->link('Version',['get'=> $firmwares->sortParams('version')]) ?></th>
				<th><?= $this->link('Updated',['get'=> $firmwares->sortParams('updated_at')]) ?></th>
				<th><?= $this->link('Created',['get'=> $firmwares->sortParams('created_at')]) ?></th>
				<th class="action"></th>
			</tr>
		</thead>
		<tbody>
		<?php foreach ($firmwares as $firmware): ?>
		<tr class="clickable-row" data-url="<?= $this->url(['action' => 'show','params'=>['firmware'=>$firmware->_id]]) ?>">
			<td><?= $firmware->name ?></td>
			<td><?= $firmware->version ?></td>
			<td><?= $firmware->updated_at?->format('d/m/Y H:i') ?></td>
			<td><?= $firmware->created_at?->format('d/m/Y H:i') ?></td>
			<td class="action">
				<a href="<?= $this->url(['action' => 'show','params'=>['firmware'=>$firmware->_id]]) ?>" class="btn-mini btn-info"><i class="icon-info"></i></a>
				<a href="<?= $this->url(['action' => 'edit','params'=>['firmware'=>$firmware->_id]]) ?>" class="btn-mini btn-warning"><i class="icon-edit"></i></a>
			</td>
		</tr>
		<?php endforeach ?>
		</tbody>
	</table>

	<?= $this->part('part.paginator', $firmwares->getOption()); ?>
</section>