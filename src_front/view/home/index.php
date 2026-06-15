<?php
$this->setTitle('Accueil');
$this->setLayout('layout.default');
?>

<header class="page-header">
	<div class="page-title">
		<h2 class="rainbow-icon"><i class="icon-home"></i> Accueil</h2>
	</div>
	<div class="page-action">
		<a href="<?= $this->url(['controller' => 'widget', 'action' => 'index']) ?>" class="btn btn-primary">
			<i class="icon icon-settings"></i>
			Gérer les raccourcis
		</a>
	</div>
</header>

<section class="page-content">
	<div class="widget-grid grid-layout">
		<?php foreach ($widgets as $widget): ?>
			<?= $this->part('part.widget', ['widget' => $widget]) ?>
		<?php endforeach; ?>
	</div>
</section>
