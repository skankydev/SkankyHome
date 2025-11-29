<?php 
$this->setTitle('Welcome');
$this->setLayout('layout.default');
?>

<header class="home-cover">
	<div class="home-text">
		<span class="debug-php">&lt;?=</span><span class="debug-keyword"> __</span>(<span class="debug-text">"Hello World"</span>) <span class="debug-php">?&gt;</span>
	</div>
</header>
<section id="Index">
	<div class="layout-index">
		<div>
			<?= _('Lorem ipsum dolor sit amet, consectetur adipisicing elit. Laudantium, numquam?'); ?>
			<ul>
				<li><?= _('PHP version: ').PHP_VERSION; ?></li>
				<li><?= _('Mongodb driver: ').phpversion("mongodb"); ?></li>
				<li><?= _('GetText: ').phpversion("gettext"); ?></li>
				<li><?= _('Intl: ').phpversion("intl"); ?></li>
			</ul>
		</div>
		<div>
			<?= _('Laborum repellendus ducimus earum. Libero labore ducimus nulla architecto sapiente.'); ?>
		</div>
		<div>
			<?= _('Expedita hic quas vel vero culpa molestias itaque, quidem saepe. ');?>
		</div>
	</div>
	<?php debug($this) ?>
</section>

