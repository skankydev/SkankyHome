<?php $scenarios->setLink(['action' => 'show', 'params' => ['module' => $module->_id]]); ?>
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
					<th><?= $scenarios->sortLink('name', 'Name') ?></th>
					<th><?= $scenarios->sortLink('updated_at', 'Updated') ?></th>
					<th><?= $scenarios->sortLink('created_at', 'Created') ?></th>
					<th class="action"></th>
				</tr>
			</thead>
			<tbody>
			<?php foreach ($scenarios as $scenario): ?>
			<tr class="clickable-row" data-url="<?= $this->url(['controller'=>'scenario','action'=>'edit','params'=>['scenario'=>$scenario->_id]]) ?>">
				<td><i class="<?= $scenario->icon ?>"></i> <?= $scenario->name ?></td>
				<td><?= $scenario->updated_at?->format('d/m/Y H:i') ?></td>
				<td><?= $scenario->created_at?->format('d/m/Y H:i') ?></td>
				<td class="action">
					<a href="<?= $this->url(['controller'=>'scenario','action' => 'send','params'=>['module'=>$module->_id,'scenario'=>$scenario->_id]]) ?>" class="btn-mini btn-success">
						<i class="icon-upload"></i>
					</a>
					<a href="<?= $this->url(['controller'=>'scenario','action' => 'edit','params'=>['scenario'=>$scenario->_id]]) ?>" class="btn-mini btn-warning">
						<i class="icon-edit"></i>
					</a>
					<a href="<?= $this->url(['controller'=>'scenario','action' => 'delete','params'=>['scenario'=>$scenario->_id]]) ?>" class="btn-mini btn-error">
						<i class="icon-trash"></i>
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
