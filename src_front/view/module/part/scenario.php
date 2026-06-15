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
		<?= $this->part('part.table', [
			'paginator' => $scenarios,
			'paginBtn' => false,
			'btnShow' => false,
			'btnEdit' => false,
			'trAction' => 'edit',
			'actions'   => function($scenario) {
				$html = '<a href="'.$this->url(['controller'=>'scenario','action' => 'send','params'=>['module'=>$scenario->module_id,'scenario'=>$scenario->_id]]).'" class="btn-mini btn-success"><i class="icon-upload"></i></a>';
				$html .= ' <a href="'.$this->url(['controller'=>'scenario','action' => 'edit','params'=>['scenario'=>$scenario->_id]]).'" class="btn-mini btn-warning"><i class="icon-edit"></i></a>';
				$html .= ' <a href="'.$this->url(['controller'=>'scenario','action' => 'delete','params'=>['scenario'=>$scenario->_id]]).'" class="btn-mini btn-error"><i class="icon-trash"></i></a>';
				return $html;
			},
		]); ?>
	</div>
	<div class="card-footer align-center">
		<?= $this->part('part.paginator', $scenarios->getOption()); ?>
	</div>
</div>
