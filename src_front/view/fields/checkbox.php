<div class="form-group <?= $errors ? 'has-error' : '' ?>">
	
	<input 
		type="<?= $type ?>" 
		id="<?= $id ?>" 
		name="<?= $name ?>" 
		value="<?= e($value) ?>"
		<?= $this->required() ?>
		<?= $this->createAttr($attributes) ?>
	>
	
	<?php if ($label): ?>
		<label for="<?= $id ?>"  <?= $this->createAttr($labelAttr) ?> ><?= e($label) ?></label>
	<?php endif; ?>
	
	<?php if ($errors): ?>
		<span class="text-error"><?= e($errors[0]) ?></span>
	<?php endif; ?>
</div>