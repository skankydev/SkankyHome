<?php $flash = flash() ?? []; ?>

<?php foreach ($flash as $key => $v): ?>
<div class="flash-message flash-<?= $v['type'] ?>" onclick="remove(this)">
	<i class="icon-<?= $v['type'] ?>"></i> <?= $v['message'] ?>
</div>
<?php endforeach ?>