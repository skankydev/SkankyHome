<?php 
$this->setLayout('layout.default');
$this->addCrumb('Project',['action'=>'index'],'icon-sunrise');
?>

<header class="page-header">
	<div class="page-title">
		<h2 class="rainbow-icon">
			<i class="icon-sunrise"></i>
			Project
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
	<?= $this->part('part.table', ['paginator' => $projects]); ?>
</section>
