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
	<?= $this->part('part.table', [
		'paginator' => $modules,
		'actions'   => fn($module) => $this->link('<i class="icon-refresh-cw"></i>',
			['action' => 'reboot', 'params' => ['module' => $module->_id]],
			['class' => 'btn-mini btn-success','data-tooltip'=>'Reboot']),
	]); ?>
</section>