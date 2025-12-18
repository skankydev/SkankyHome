<?php 
$this->setLayout('layout.default');
?>

<header class="page-header">
	<div class="page-title">
		<h1 class="rainbow-icon">
			<i class="<?= e($module->icon) ?>"></i>
			Module <?= e($module->name) ?>
		</h1>
	</div>
	<div class="page-action">
		<a href="<?= $this->url(['action'=>'edit','params'=>[$module->_id]]) ?>" class="btn btn-primary">
			<i class="icon icon-edit"></i>
			Edit
		</a>
		<a href="<?= $this->url(['action'=>'delete','params'=>[$module->_id]]) ?>" class="btn btn-error">
			<i class="icon-delete"></i>
			Delete
		</a>
	</div>
</header>

<section class="page-content grid-layout">
	<div class="grid-half card">
		<header class="card-header">
			<h2 class="corner-accent-info">
				<i class="icon-info color-info"></i>
				Info
			</h2>
		</header>
		<div class="card-body">
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
	</div>
	<div class="grid-full card">
		<header class="card-header card-header-action">
			<div><h2 class="corner-accent-warning"><i class="icon-film color-warning"></i> Scenario</h2></div>
			<div>
				<a href="<?= $this->url(['controller'=>'scenario' ,'action'=>'create','params'=>[$module->_id]]) ?>" class="btn btn-warning">
					<i class="icon icon-add"></i>Ajouter
				</a>
			</div>
		</header>
		<div class="card-body">
			<table>
				<thead>
					<tr>
						<th><?= $this->link('Name',['get'=> $scenarios->sortParams('name')]) ?></th>
						<th><?= $this->link('Updated',['get'=> $scenarios->sortParams('updated_at')]) ?></th>
						<th><?= $this->link('Created',['get'=> $scenarios->sortParams('created_at')]) ?></th>
						<th class="action"></th>
					</tr>
				</thead>
				<tbody>
				<?php foreach ($scenarios as $scenario): ?>
				<tr class="clickable-row" data-url="<?= $this->url(['controller'=>'scenario','action'=>'show','params'=>['scenario'=>$scenario->_id]]) ?>">
					<td><i class="<?= $scenario->icon ?>"></i> <?= $scenario->name ?></td>
					<td><?= $scenario->updated_at?->format('d/m/Y H:i') ?></td>
					<td><?= $scenario->created_at?->format('d/m/Y H:i') ?></td>
					<td class="action">
						<a href="<?= $this->url(['controller'=>'scenario','action' => 'show','params'=>['scenario'=>$scenario->_id]]) ?>" class="btn-mini btn-info">
							<i class="icon-info"></i>
						</a>
						<a href="<?= $this->url(['controller'=>'scenario','action' => 'edit','params'=>['scenario'=>$scenario->_id]]) ?>" class="btn-mini btn-warning">
							<i class="icon-edit"></i>
						</a>
					</td>
				</tr>
				<?php endforeach ?>
				</tbody>
			</table>
		</div>
		<div class="card-footer">
			<?= $this->part('part.paginator', $scenarios->getOption()); ?>
		</div>
	</div>
</section>