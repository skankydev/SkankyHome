<?php

namespace App\Job;

use App\Model\Document\Module;
use App\Utilities\MqttSender;
use SkankyDev\Queue\Job\MasterJob;

class SendCommandJob extends MasterJob {

	public string $topic;
	public string $cmd;
	public array $data;

	public function __construct(Module $module, string $cmd, array $data = []){
		$this->topic = $module->topic_cmd;
		$this->cmd   = $cmd;
		$this->data  = $data;
	}

	public function run(): void {
		MqttSender::publish($this->topic, ['cmd' => $this->cmd, 'data' => $this->data]);
	}
}
