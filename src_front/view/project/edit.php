<?php 
$this->setLayout('layout.default');

$this->addCrumb('Project',['action'=>'index'],'icon-sunrise');
$this->addCrumb($project->name,['action'=>'show','params'=>['project'=>$project->_id]],$project->icon);
$this->addCrumb('Edit',['action'=>'edit','params'=>['project'=>$project->_id]]);
?>

<header class="page-header">
	<div class="page-title">
		<h2 class="rainbow-icon">
			<i class="icon-zap"></i>
			Modifier Project 
		</h2>
	</div>
	<div class="page-action">
		<a href="<?= $this->url(['action'=>'index']) ?>" class="btn btn-secondary">
			<i class="icon-arrow-left"></i>
			Retour
		</a>
	</div>
</header>

<section class="page-content">
	<?= $form->render() ?>
</section>