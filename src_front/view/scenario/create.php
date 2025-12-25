<?php 
$this->setLayout('layout.default');

$this->addCrumb('Module',['controller'=> 'module', 'action'=>'index'],'icon-zap');
$this->addCrumb($module->name,['controller'=> 'module', 'action'=>'show','params'=>['module'=>$module->_id]],$module->icon);
if($scenario->_id != '' && $scenario->name != ''){
	$this->addCrumb($scenario->name,['action'=>'edit','params'=>['scenario'=>$scenario->_id]],$scenario->icon);
}else{
	$this->addCrumb('Scenario',['action'=>'create'],'icon-film');
}

$links = [
	'save'  => $this->url(['action'=>'save']),
];
?>
<section id="ScenarioMaker">
	<scenario-maker
		:scenario-origin='<?= json($scenario) ?>'
		:module='<?= json($module) ?>'
		:icons='<?= json($icons) ?>'
		:effects='<?= json($effects) ?>'
		:links='<?=  json($links) ?>'
	></scenario-maker>
</section>

