<?php 
$this->setLayout('layout.default');
?>

<header class="page-header">
	<div class="page-title">
		<h2 class="rainbow-icon">
			<i class="icon-zap"></i>
			<?= e($persona->name) ?>  
		</h2>
	</div>
	<div class="page-action">
		<a href="<?= $this->url(['action'=>'chat','params'=>[$persona->_id]]) ?>" class="btn btn-success">
			<i class="icon-message-circle"></i>
			Chat
		</a>
		<a href="<?= $this->url(['action'=>'edit','params'=>[$persona->_id]]) ?>" class="btn btn-primary">
			<i class="icon icon-edit"></i>
			Edit
		</a>
		<a href="<?= $this->url(['action'=>'delete','params'=>[$persona->_id]]) ?>" class="btn btn-error">
			<i class="icon-delete"></i>
			Delete
		</a>
	</div>
</header>

<section class="page-content grid-layout">
	<div class="grid-half card p-s">
		<dl>
			<dt>Name</dt>
			<dd><?= e($persona->name) ?></dd>
			<dt>created</dt>
			<dd><?= $persona->created_at?->format('d/m/Y H:i') ?></dd>
			<dt>updated</dt>
			<dd><?= $persona->updated_at?->format('d/m/Y H:i') ?></dd>
		</dl>
	</div>
	<div class="grid-half card p-s">
		<img src="<?= $persona->img_url ?>" alt="">
	</div>
	<div class="grid-full card p-m">
<pre>
<?= $persona->content ?>
</pre>
	</div>
</section>