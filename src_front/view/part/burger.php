<div id="BurgerWrapper" class="burger-wrapper">
	<div class="burger-menu"> 
		<span class="burger-line burger-line-1"></span>
		<span class="burger-line burger-line-2"></span>
		<span class="burger-line burger-line-3"></span>
	</div>
	<div class="burger-content">
		<div class="burger-logo-wrapper">
			<div class="logo-skankyhome"></div>
		</div>
		<hr class="rainbow-diviser">
		<div class="burger-link-wrapper">
			<div class="burger-link">
				<a href="<?= $this->url(['controller'=>'home','action'=>'index']) ?>"><i class="icon-home"></i>Home</a>
			</div>
			<div class="burger-link">
				<a href="<?= $this->url(['controller'=>'module','action'=>'index']) ?>"><i class="icon-zap"></i>Module</a>
			</div>
		</div>
	</div>
</div>