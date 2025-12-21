<?php 

namespace App\Job;

use App\Model\Document\Firmware;
use App\Model\Document\Module;
use App\Utilities\MqttSender;
use SkankyDev\Queue\Job\MasterJob;

class SendFirmwareJob extends MasterJob {

	public array $cmd;
	public string $topic;

	public function __construct(Module $module, Firmware $firmware){
		$this->cmd = ['update' => $firmware->file['url'],];
		$this->topic = $module->topic_cmd;

	}

	public function run():void{
		MqttSender::publish($this->topic, $this->cmd);
	}

}