<?php 
$this->setLayout('layout.default');
?>

<header class="page-header">
	<div class="page-title">
		<h2 class="rainbow-icon">
			<i class="icon-zap"></i>
			Sequence
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
				<th><?= $this->link('Icon',['get'=> $sequences->sortParams('icon')]) ?></th>
				<th><?= $this->link('Name',['get'=> $sequences->sortParams('name')]) ?></th>
				<th><?= $this->link('Config',['get'=> $sequences->sortParams('config')]) ?></th>
				<th><?= $this->link('Updated',['get'=> $sequences->sortParams('updated_at')]) ?></th>
				<th><?= $this->link('Created',['get'=> $sequences->sortParams('created_at')]) ?></th>
				<th class="action"></th>
			</tr>
		</thead>
		<tbody>
		<?php foreach ($sequences as $sequence): ?>
		<tr class="clickable-row" href="<?= $this->url(['action' => 'show','params'=>['sequence'=>$sequence->_id]]) ?>">
			<td><?= $sequence->icon ?></td>
			<td><?= $sequence->name ?></td>
			<td><?= count($sequence->config) ?></td>
			<td><?= $sequence->updated_at?->format('d/m/Y H:i') ?></td>
			<td><?= $sequence->created_at?->format('d/m/Y H:i') ?></td>
			<td class="action">
				<a href="<?= $this->url(['action' => 'show','params'=>['sequence'=>$sequence->_id]]) ?>" class="btn-mini btn-info"><i class="icon-info"></i></a>
				<a href="<?= $this->url(['action' => 'edit','params'=>['sequence'=>$sequence->_id]]) ?>" class="btn-mini btn-warning"><i class="icon-edit"></i></a>
			</td>
		</tr>
		<?php endforeach ?>
		</tbody>
	</table>

	<?= $this->part('part.paginator', $sequences->getOption()); ?>
</section>