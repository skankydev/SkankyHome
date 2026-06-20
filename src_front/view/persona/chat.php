<?php
$this->setLayout('layout.default');

$this->addCrumb('Persona',['controller'=> 'persona', 'action'=>'index'],'icon-message-circle');
$this->addCrumb($persona->name,['action'=>'show','params'=>['persona'=>$persona->_id]],'icon-message-circle');
$this->addCrumb('Chat',['action'=>'chat','params'=>['persona'=>$persona->_id]],'icon-message-circle');
?>
<section id="PersonaChat" class="p-m">
	<persona-chat
		:persona='<?= json($persona) ?>'
		:llama-url="'<?= e($llamaUrl) ?>'"
	></persona-chat>
</section>
