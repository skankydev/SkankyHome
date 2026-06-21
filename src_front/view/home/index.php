<?php
$this->setTitle('Accueil');
$this->setLayout('layout.default');
?>

<header class="page-header">
	<div class="page-title">
	</div>
	<div class="page-action">
		<a href="<?= $this->url(['controller' => 'widget', 'action' => 'index']) ?>" class="btn btn-primary">
			<i class="icon icon-settings"></i>
			Gérer les raccourcis
		</a>
	</div>
</header>

<section class="page-content">
	<div class="widget-grid">
		<?php foreach ($targets as $target): ?>
			<?= $this->part('part.widget', $target) ?>
		<?php endforeach; ?>
	</div>
</section>
