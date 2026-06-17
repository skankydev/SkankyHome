<div class="card widget-card card-info">
<a href="<?= $this->url($link) ?>" class="widget-link">
	<div class="card-body widget-body">
		<?php if ($target->img_url): ?>
			<div class="widget-image-wrapper p-s">
				<img src="<?= e($target->img_url) ?>" alt="<?= e($target->name) ?>" class="widget-image">
			</div>
		<?php elseif (!empty($target->icon)): ?>
			<div class="widget-icon p-s"><i class="<?= e($target->icon) ?>"></i></div>
		<?php endif; ?>
	</div>
	<div class="card-footer widget-name"><?= e($target->name) ?></div>
</a>
</div>
