<!DOCTYPE html>
<html lang=<?= '"'.SkankyDev\I18n\Localization::getLang().'"'; ?>>
<head>
	<meta charset="UTF-8" />
	<link rel="icon" type="image/png" href="/favicon.ico" />
	<title>
		<?php
			$titre =  $this->getTitle();
			if (!empty($titre)) {
				echo ucwords($titre.' - ');
			}
		?>SkankyDev 
	</title>
	<?php 
		$this->addKeyWords('php, apache, mongodb, javascript');
		$this->addMeta('author','Schenck simon');
		$this->addMeta('description','le blog d un développeur web');
		$this->addJs("/vendor/jquery/jquery-2.1.4.min.js");
		$this->addJs("/js/skankydev.js");
		$this->addCss("/css/app.css");
		//$this->addCss("/css/skankydev.css");
		echo $this->getHeader(); 
	?>
</head>
<body>
	<?= $this->element('admin-menu'); ?>
	<section id="Container">

		<header id="Header">
			<div class="layout-header">
				<div class='sit-title'>
					<h1>
						<i class="fa fa-unlock-alt fa-flip-horizontal sit-title-icone"></i>SkankyDev
						<legend class="sit-title-legende">'let's try to do something with it!'</legend>
					</h1>
				</div>
				<div class="empty-space"></div>
				<?= $this->element('user-statu'); ?>
			</div>
		</header>
		
		<section id="Contents">
			<?= $this->Flash->display(); ?>
			<?= $this->fetch('content'); ?>
		</section>
		<footer id="Footer">
			<?= $this->element('footer'); ?>
		</footer>
	</section>
	<?= $this->element('debug'); ?>
	<?= $this->getScript(); ?>
</body>
</html>
