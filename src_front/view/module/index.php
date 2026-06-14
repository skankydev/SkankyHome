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
				<th><?= $modules->sortLink('name', 'Name') ?></th>
				<th><?= $modules->sortLink('topic_message', 'Topic message') ?></th>
				<th><?= $modules->sortLink('topic_cmd', 'Topic cmd') ?></th>
				<th><?= $modules->sortLink('nb_line', 'Nb line') ?></th>
				<th><?= $modules->sortLink('nb_led', 'Nb led') ?></th>
				<th><?= $modules->sortLink('created_at', 'created') ?></th>
				<th><?= $modules->sortLink('updated_at', 'updated') ?></th>
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
				<a href="<?= $this->url(['action' => 'reboot','params'=>['module'=>$module->_id]]) ?>" class="btn-mini btn-success"><i class="icon-refresh-cw"></i></a>
				<a href="<?= $this->url(['action' => 'edit','params'=>['module'=>$module->_id]]) ?>" class="btn-mini btn-warning"><i class="icon-edit"></i></a>
			</td>
		</tr>
		<?php endforeach ?>
		</tbody>
	</table>

	<?= $this->part('part.paginator', $modules->getOption()); ?>
</section>