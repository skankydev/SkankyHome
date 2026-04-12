<?php
$this->setLayout('layout.default');
$this->addCrumb('MQTT Monitor', ['action' => 'index'], 'icon-activity');
?>

<header class="page-header">
	<div class="page-title">
		<h2 class="rainbow-icon">
			<i class="icon-activity"></i>
			MQTT Monitor
		</h2>
	</div>
</header>

<section id="MqttMonitor" class="mh-m">
	<mqtt-monitor></mqtt-monitor>
</section>
