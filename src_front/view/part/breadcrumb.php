<?php 

$breadcrumb = [];

$breadcrumb[] = [
	'label' => 'Home',
	'url' => $this->url(['controller' => 'Home', 'action' => 'index']),
	'icon' => 'icon-home',
];

foreach ($this->breadcrumbInfo as $item) {
	$breadcrumb[] = $item;
}


?>

<?php if (!empty($breadcrumb)): ?>
<nav class="breadcrumb" aria-label="Breadcrumb">
	<?php foreach ($breadcrumb as $key => $item): ?>
		<?php if ($item['url']): ?>
			<a href="<?= $item['url'] ?>" class="breadcrumb-item">
				<i class="<?= $item['icon'] ?>"></i>
				<?= e($item['label']) ?>
			</a>
		<?php else: ?>
			<span class="breadcrumb-item breadcrumb-current">
				<?= e($item['label']) ?>
			</span>
		<?php endif; ?>
		
		<?php if ($key < count($breadcrumb) - 1): ?>
			<span class="breadcrumb-separator"><i class="icon-chevron-right"></i></span>
		<?php endif; ?>
	<?php endforeach; ?>
</nav>
<?php endif; ?>