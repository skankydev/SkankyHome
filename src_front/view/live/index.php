<?php 
$this->setTitle('Welcome');
$this->setLayout('layout.default');
?>

<section class="pb-xxl">
	<h1 class="rainbow-icon corner-accent">
		<i class="icon-airplay"></i>Live Mode
	</h1>
</section>

<section id="LiveMode">
	<live-mode
		:effects='<?= json($effects) ?>'
	></live-mode>
</section>
