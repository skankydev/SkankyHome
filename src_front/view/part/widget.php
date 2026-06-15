<?php
/**
 * Carte widget polymorphe pour la home.
 * Résout la cible depuis (target_collection + target_id) et affiche, dans l'ordre :
 * son image (img) sinon son icône (icon) sinon rien, plus son nom. Le lien dépend
 * de la convention widgetLink() de la collection cible.
 *
 * @var object $widget  document Widget (target_collection, target_id)
 */
$collectionClass = $widget->target_collection;

// Collection invalide (typo) ou cible supprimée → on n'affiche pas la carte.
if (!class_exists($collectionClass)) {
	return;
}
$target = $collectionClass::_findById((string) $widget->target_id);
if (!$target) {
	return;
}

$link = $collectionClass::_widgetLink($target);
?>
<a href="<?= $this->url($link) ?>" class="card widget-card">
	<?php if (!empty($target->img)): ?>
		<img src="<?= e($target->img) ?>" alt="<?= e($target->name) ?>" class="widget-image">
	<?php elseif (!empty($target->icon)): ?>
		<i class="<?= e($target->icon) ?> widget-icon"></i>
	<?php endif; ?>
	<span class="widget-name"><?= e($target->name) ?></span>
</a>
