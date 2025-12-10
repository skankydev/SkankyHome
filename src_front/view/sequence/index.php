<?php 
$this->setLayout('layout.default');
?>

<header class="page-header">
	<div class="page-title">
		<h2 class="rainbow-icon">
			<i class="icon-zap"></i>
			Config Led
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
				<th> name </th>
				<th> color </th>
				<th> duration </th>
				<th> active </th>
				<th class="action"></th>
			</tr>
		</thead>
		<tbody>
		<?php foreach ($sequences as $sequence): ?>
		<tr>
			<td><?= $sequence->name ?></td>
			<td><?= $sequence->color ?></td>
			<td><?= $sequence->duration ?></td>
			<td><?= $sequence->active ? 'Oui': 'Non' ?></td>
			<td class="action">
				<a href="<?= $this->url(['action' => 'show','params'=>['sequence'=>$sequence->_id]]) ?>" class="btn-mini btn-info"><i class="icon-info"></i></a>
				<a href="<?= $this->url(['action' => 'show','params'=>['sequence'=>$sequence->_id]]) ?>" class="btn-mini btn-warning"><i class="icon-edit"></i></a>
			</td>
		</tr>
		<?php endforeach ?>
		</tbody>
	</table>

	<?= $this->part('part.paginator',$sequences->getOption()); ?>
</section>

