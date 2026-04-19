<?php
$this->setLayout('layout.default');
$this->addCrumb('Effect Preview', ['action' => 'index'], 'icon-zap');
?>

<header class="page-header">
	<div class="page-title">
		<h2 class="rainbow-icon">
			<i class="icon-zap"></i>
			Effect Preview
		</h2>
	</div>
</header>

<section id="EffectPreview">
	<effect-preview
		:effects='<?= json($effects) ?>'
	></effect-preview>
</section>
