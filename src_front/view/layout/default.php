<!DOCTYPE html>
<html lang="FR_fr">
<head>
	<meta charset="UTF-8" />
	<link rel="icon" type="image/png" href="/favicon.ico" />
	<title>
		<?php
			$titre = $this->getTitle();
			if (!empty($titre)) {
				echo ucwords($titre.' - ');
			}
		?>SkankyDev 
	</title>
	<?php 
		$this->addKeyWords('php, apache, mongodb, javascript');
		$this->addMeta('author','Schenck simon');
		$this->addMeta('description','le blog d un développeur web');
		$this->addJs('/dist/app.js','module');
		$this->addCss('/dist/styles.css');
		echo $this->getHeader(); 
	?>
</head>
<body id="MainBody">
	<section id="MainContainer">
		<header id="Header">
			<div class="layout-header">
				<a href="<?= $this->url(['controller' => 'Home','action' => 'index','namespace' => 'App']) ?>" class="main-title">SkankyHome</a>
			</div>
			<hr class="rainbow-diviser">
		</header>
		<?= $this->part('part.flash'); ?>
		<section id="Contents">
			<?= $this->fetch('content'); ?>
		</section>
		<footer id="Footer">
			<?= $this->part('part.footer'); ?>
		</footer>
	</section>
	<?= $this->getScript(); ?>
</body>
</html>
