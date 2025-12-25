<?php 
$this->setLayout('layout.default');
$this->addCrumb('Module',['action'=>'index'],'icon-zap');
$this->addCrumb('Creat',['action'=>'create'],'');
?>

<header class="page-header">
	<div class="page-title">
		<h2 class="rainbow-icon">
			<i class="icon-zap"></i>
			Ajouter Module
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