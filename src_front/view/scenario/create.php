<?php 
$this->setLayout('layout.default');


$links = [
	'save'  => $this->url(['action'=>'save']),
];
?>
<section id="ScenarioMaker">
	<scenario-maker
		:scenario='<?= json($scenario) ?>'
		:module='<?= json($module) ?>'
		:icons='<?= json($icons) ?>'
		:effects='<?= json($effects) ?>'
		:links='<?=  json($links) ?>'
	></scenario-maker>
</section>

