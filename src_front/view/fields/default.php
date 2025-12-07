<div class="form-group <?= $errors ? 'has-error' : '' ?>">
	<?php if ($label): ?>
		<label for="<?= $id ?>"  <?= $this->createAttr($labelAttr) ?> ><?= e($label) ?></label>
	<?php endif; ?>
	
	<input 
		type="<?= $type ?>" 
		id="<?= $id ?>" 
		name="<?= $name ?>" 
		value="<?= $value ?>"
		<?= $this->required() ?>
		<?= $this->createAttr($attributes) ?>
	>
	
	<?php if ($errors): ?>
		<div class="text-error"><?= e($errors[0]) ?></div>
	<?php endif; ?>
</div>