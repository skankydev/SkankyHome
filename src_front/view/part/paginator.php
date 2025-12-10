<?php $aClass = 'btn-mini'; ?>
<nav id="Paginator" class="text-center p-m">
	
	<?php $class = $aClass; if($first==$prev) $class .= ' btn-disabled'; ?>	
	
		<?php 
			$params['page'] = $first;
			echo $this->link('&#60;&#60;',['get'=>$params],['class'=>$class]); 
		?>
	
	<?php $class = $aClass; if($page==$prev) $class .= ' btn-disabled'; ?>
	
		<?php
		$params['page'] = $prev;
		echo $this->link('&#60;',['get'=>$params],['class'=>$class]); ?>
	
	<?php for ($i=$start; $i < $stop; ++$i): ?> 
		<?php $class = $aClass; if($i==$page) $class .= ' btn-success'; ?>	
		
		<?php 
		$params['page'] = $i;
		echo $this->link($i,['get'=>$params],['class'=>$class]); ?>
		
	<?php endfor ?>
	<?php $class = $aClass; if($page==$next) $class .= ' btn-disabled'; ?>	
	
	<?php 
	$params['page'] = $next;
	echo $this->link('&#62;',['get'=>$params],['class'=>$class]);
	?>
	
	<?php $class = $aClass; if($last==$next) $class .= ' btn-disabled'; ?>	
	
	<?php
	$params['page'] = $last;
	echo $this->link('&#62;&#62;',['get'=>$params],['class'=>$class]); ?>
</nav>