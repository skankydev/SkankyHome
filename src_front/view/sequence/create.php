<?php 
$this->setLayout('layout.default');
?>

<header class="page-header">
	<div class="page-title">
		<h2 class="rainbow-icon">
			<i class="icon-zap"></i>
			New Config Led
		</h2>
	</div>
	<div class="page-action">
		<a href="<?= $this->url(['action'=>'index']) ?>" class="btn btn-error">
			<i class="icon-skip-back"></i>
			Retour
		</a>
	</div>
</header>

<section class="page-content">
	<div class="form-wrapper">
		<?= $form->render() ?>
	</div>
</section>
