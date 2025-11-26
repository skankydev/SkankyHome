<?php $aClass = 'btn-paging'; ?>
<nav id="Paginator">
	<ul class="paging">
		<?php $class = $aClass; if($first==$prev) $class .= ' paging-disabled'; ?>	
		<li>
			<?php 
				$params['page'] = $first;
				echo $this->link('&#60;&#60;',['get'=>$params],['class'=>$class]); 
			?>
		</li>
		<?php $class = $aClass; if($page==$prev) $class .= ' paging-disabled'; ?>
		<li>
			<?php
			$params['page'] = $prev;
			echo $this->link('&#60;',['get'=>$params],['class'=>$class]); ?>
		</li>
		<?php for ($i=$start; $i < $stop; ++$i): ?> 
			<?php $class = $aClass; if($i==$page) $class .= ' paging-current'; ?>	
			<li>
				<?php 
				$params['page'] = $i;
				echo $this->link($i,['get'=>$params],['class'=>$class]); ?>
			</li>
		<?php endfor ?>
		<?php $class = $aClass; if($page==$next) $class .= ' paging-disabled'; ?>	
		<li>
			<?php 
			$params['page'] = $next;
			echo $this->link('&#62;',['get'=>$params],['class'=>$class]);
			?>
		</li>
		<?php $class = $aClass; if($last==$next) $class .= ' paging-disabled'; ?>	
		<li>
			<?php
			$params['page'] = $last;
			echo $this->link('&#62;&#62;',['get'=>$params],['class'=>$class]); ?>
		</li>
	</ul>
</nav>