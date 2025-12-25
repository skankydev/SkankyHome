<?php 
$this->setLayout('layout.default');
$this->addCrumb('Module',['action'=>'index'],'icon-zap');
?>

<header class="page-header">
	<div class="page-title">
		<h2 class="rainbow-icon">
			<i class="icon-zap"></i>
			Module
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
				<th><?= $this->link('Name',['get'=>$modules->sortParams('name')]) ?></th>
				<th><?= $this->link('Topic message',['get'=>$modules->sortParams('topic_message')]) ?></th>
				<th><?= $this->link('Topic cmd',['get'=>$modules->sortParams('topic_cmd')]) ?></th>
				<th><?= $this->link('Nb line',['get'=>$modules->sortParams('nb_line')]) ?></th>
				<th><?= $this->link('Nb led',['get'=>$modules->sortParams('nb_led')]) ?></th>
				<th><?= $this->link('created',['get'=>$modules->sortParams('created_at')]) ?></th>
				<th><?= $this->link('updated',['get'=>$modules->sortParams('updated_at')]) ?></th>
				<th class="action"></th>
			</tr>
		</thead>
		<tbody>
		<?php foreach ($modules as $module): ?>
		<tr class="clickable-row" data-url="<?= $this->url(['action' => 'show','params'=>['module'=>$module->_id]]) ?>">
			<td><?= $module->name ?></td>
			<td><?= $module->topic_message ?></td>
			<td><?= $module->topic_cmd ?></td>
			<td><?= $module->nb_line ?></td>
			<td><?= $module->nb_led ?></td>
			<td><?= $module->created_at?->format('d/m/Y H:i') ?></td>
			<td><?= $module->updated_at?->format('d/m/Y H:i') ?></td>
			<td class="action">
				<a href="<?= $this->url(['action' => 'show','params'=>['module'=>$module->_id]]) ?>" class="btn-mini btn-info"><i class="icon-info"></i></a>
				<a href="<?= $this->url(['action' => 'edit','params'=>['module'=>$module->_id]]) ?>" class="btn-mini btn-warning"><i class="icon-edit"></i></a>
			</td>
		</tr>
		<?php endforeach ?>
		</tbody>
	</table>

	<?= $this->part('part.paginator', $modules->getOption()); ?>
</section>