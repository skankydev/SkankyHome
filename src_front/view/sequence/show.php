<?php 
$this->setLayout('layout.default');
?>

<section class="page-content">
	<header class="page-header">
		<div class="page-title">
			<h2 class="rainbow-icon">
				<i class="icon-zap"></i>
				<?= $sequence->name ?>
			</h2>
		</div>
		<div class="page-action">
			<a href="<?= $this->url(['action'=>'edit','params'=>[$sequence->_id]]) ?>" class="btn btn-primary">
				<i class="icon icon-edit"></i>
				Edit
			</a>
			<a href="<?= $this->url(['action'=>'delete','params'=>[$sequence->_id]]) ?>" class="btn btn-error">
				<i class="icon-delete"></i>
				delete
			</a>
		</div>
	</header>

	<?php debug($sequence); ?>
</section>
