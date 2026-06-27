<?php
$this->setLayout('layout.default');
$this->addCrumb('Conversation', ['action' => 'index'], '');
$this->addCrumb($conversation->name, ['action' => 'show', 'params' => [$conversation->_id]], '');
$this->addCrumb('Modifier', ['action' => 'edit', 'params' => [$conversation->_id]], '');
?>

<header class="page-header">
	<div class="page-title">
		<h2 class="rainbow-icon">
			<i class=""></i>
			Modifier Conversation		</h2>
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
