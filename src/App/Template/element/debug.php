<section id="debug" class="hideaway">
	<header class="debug-btn-containe">
		<button class="btn-debug hideaway-btn">DEBUG</button>
	</header>
	<section class="widget-debug-list hideaway-list">
		<div class="widget-debug hideaway">
			<header class="debug-btn-containe">
				<button class="btn-debug hideaway-btn">Historique</button>
			</header>
			<section class="widget-debug-content">
				<?php echo $this->element('histories',SkankyDev\Auth::getInstance()->getHistories()); ?>
			</section>
		</div>
		<div class="widget-debug hideaway">
			<header class="debug-btn-containe">
				<button class="btn-debug hideaway-btn">Session</button>
			</header>
			<section class="widget-debug-content">
			<pre>
				<code>
					<?php debug($_SESSION); ?>
				</code>
			</pre>
			</section>
		</div>
		<div class="widget-debug hideaway">
			<header class="debug-btn-containe">
				<button class="btn-debug hideaway-btn">Debug Listener</button>
			</header>
			<section class="widget-debug-content">
				<?php debug(SkankyDev\EventManager::getInstance()->getListener('Debug')); ?>
			</section>
		</div>
		<div class="widget-debug hideaway">
			<header class="debug-btn-containe">
				<button class="btn-debug hideaway-btn">Event Mapping</button>
			</header>
			<section class="widget-debug-content">
				<?php debug(SkankyDev\EventManager::getInstance()->getEventMapping()); ?>
			</section>
		</div>
		<div class="widget-debug hideaway">
			<header class="debug-btn-containe">
				<button class="btn-debug hideaway-btn">Listener</button>
			</header>
			<section class="widget-debug-content">
				<?php debug(SkankyDev\EventManager::getInstance()->getListenerListe()); ?>
			</section>
		</div>
		<div class="widget-debug debug-time">
			<?php 
				$starttime = $_SERVER['REQUEST_TIME_FLOAT'];
				$endtime = microtime(true);
				$time = ($endtime-$starttime)*1000;
				$time = (int)$time;
			?>
			<span class="debug-time"><?php echo $time; ?> ms</span>
		</div>
	</section>
</section>
<!-- see you soon script -->