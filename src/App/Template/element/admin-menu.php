<?php 
$user = SkankyDev\Auth::getAuth();
$perm = SkankyDev\Auth::getPermission();
//debug($perm);
 ?>
<?php if (!empty($perm)&&($perm->name==='admin')): ?>
<?php $action = $perm->action;  ?>
<div class="admin-panel hideaway">
	<header class="admin-btn-containe">
		<a class="admin-btn hideaway-btn">
			<span class="admin-ligne"></span>
			<span class="admin-ligne"></span>
			<span class="admin-ligne"></span>
		</a>
	</header>
	<section class="admin-content">
		<ul class="admin-option">
		<?php if ($action['Post']['list']==='allow'): ?>
			<li class="admin-button">
				<?= $this->link('Post', ['controller'=>'post','action'=>'list'],['class'=>'admin-link']);?>
			</li>
		<?php endif ?>
		<?php if ($action['Taxonomie']['index']==='allow'): ?>
			<li class="admin-button">
				<?= $this->link('Taxonomie', ['controller'=>'taxonomie','action'=>'index'],['class'=>'admin-link']);?>
			</li>
		<?php endif ?>
		<?php if ($action['Menu']['index']==='allow'): ?>
			<li class="admin-button">
				<?= $this->link('Menu', ['controller'=>'menu','action'=>'index'],['class'=>'admin-link']);?>
			</li>
		<?php endif ?>
		<?php if ($action['User']['index']==='allow'): ?>
			<li class="admin-button">
				<?= $this->link('User', ['controller'=>'user','action'=>'index'],['class'=>'admin-link']);?>
			</li>
		<?php endif ?>
		<?php if ($action['Permission']['index']==='allow'): ?>
			<li class="admin-button">
				<?= $this->link('Permission', ['controller'=>'permission','action'=>'index'],['class'=>'admin-link']);?>
			</li>
		<?php endif ?>
		</ul>
	</section>
</div>
<?php endif ?>