<?php 
$this->setLayout('layout.default');
$this->addCrumb('Persona',['action'=>'index'],'icon-message-circle');
$this->addCrumb($persona->name,['action'=>'show','params'=>['persona'=>$persona->_id]],'');
$this->addCrumb('Edit',['action'=>'edit','params'=>['persona'=>$persona->_id]]);
?>

<header class="page-header">
	<div class="page-title">
		<h2 class="rainbow-icon">
			<i class="icon-message-circle"></i>
			Modifier Persona 
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