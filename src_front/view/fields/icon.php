<div class="form-group <?= $errors ? 'has-error' : '' ?>">
	<?php if ($label): ?>
		<label for="<?= $id ?>"  <?= $this->createAttr($labelAttr) ?> > <?= e($label) ?></label>
	<?php endif; ?>
	
	<icon-picker 
		:id="'<?= $id ?>'" 
		:name="'<?= $name ?>'" 
		:required="<?= $this->required() ? 'true' : 'false' ?>"
		:icons='<?= json($icons) ?>'
		:value ="'<?= $value ?>'"
	>
	</icon-picker>
	
	<?php if ($errors): ?>
		<div class="text-error"><?= e($errors[0]) ?></div>
	<?php endif; ?>
</div>