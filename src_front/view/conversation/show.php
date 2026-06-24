<?php
$this->setLayout('layout.default');
$this->addCrumb('Conversation', ['action' => 'index'], 'icon-message-circle');
$this->addCrumb($conversation->name, ['action' => 'show', 'params' => [$conversation->_id]], 'icon-message-circle');
$messageUrl = $this->url(['action' => 'message', 'params' => [$conversation->_id]]);
?>
<section id="ConversationChat" class="p-m">
	<conversation-chat
		:conversation='<?= json($conversation) ?>'
		:persona='<?= json($persona) ?>'
		:endpoint="'<?= e($messageUrl) ?>'"
	></conversation-chat>
</section>
