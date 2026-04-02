<?php 
$this->setLayout('layout.default');
$this->addCrumb('Module',['action'=>'index'],'icon-zap');
$this->addCrumb($module->name,['action'=>'show','params'=>['module'=>$module->_id]],$module->icon);
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
				<dt>Type</dt>
				<dd><?= e($module->type) ?></dd>
				<dt>Name</dt>
				<dd><?= e($module->name) ?></dd>
				<dt>Slug</dt>
				<dd><?= e($module->slug) ?></dd>
				<dt>Version</dt>
				<dd><?= e($module->version ?? '') ?></dd>
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
	<div class="grid-half card">
		<header class="card-header card-header-action">
			<div>
				<h2 class="corner-accent-success">
					<i class="icon-cpu color-success"></i>
					Firmware
				</h2>
			</div>
			<div>
				<a href="<?= $this->url(['controller'=>'firmware' ,'action'=>'create','params'=>[$module->_id]]) ?>" class="btn btn-warning">
					<i class="icon icon-add"></i>Ajouter
				</a>
			</div>
		</header>
		<div class="card-body">
			<table>
				<thead>
					<tr>
						<th>Name</th>
						<th>Version</th>
						<th class="action"></th>
					</tr>
				</thead>
				<tbody>
					<tr>
					<?php foreach ($firmwares as $key => $firmware): ?>
						<td><?= e($firmware->name) ?></td>
						<td><?= e($firmware->version) ?></td>
						<td class="action">
							<a href="<?= $this->url(['controller'=>'firmware','action' => 'send','params'=>['module'=>$module->_id,'firmware'=>$firmware->_id]]) ?>" class="btn-mini btn-success">
								<i class="icon-upload"></i>
							</a>
							<a href="<?= $this->url(['controller'=>'firmware','action' => 'delete','params'=>['firmware'=>$firmware->_id]]) ?>" class="btn-mini btn-error">
								<i class="icon-trash"></i>
							</a>
						</td>
					<?php endforeach ?>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<?= $this->part('module.part.' . $module->type, ['module' => $module]) ?>
</section>