%?php
$this->setLayout('layout.default');
$this->addCrumb('<?= $name ?>', ['action' => 'index'], '');
$this->addCrumb($<?= $singularCamel ?>-><?= $this->labelField() ?>, ['action' => 'show', 'params' => [$<?= $singularCamel ?>->_id]], '');
$this->addCrumb('Modifier', ['action' => 'edit', 'params' => [$<?= $singularCamel ?>->_id]], '');
?>

<header class="page-header">
	<div class="page-title">
		<h2 class="rainbow-icon">
			<i class=""></i>
			Modifier <?= $name ?>
		</h2>
	</div>
	<div class="page-action">
		<a href="%?= $this->url(['action'=>'index']) ?>" class="btn btn-secondary">
			<i class="icon-arrow-left"></i>
			Retour
		</a>
	</div>
</header>

<section class="page-content">
	%?= $form->render() ?>
</section>
