<?php 

namespace App\Job;

use App\Model\Document\Module;
use App\Model\Document\Scenario;
use App\Utilities\MqttSender;
use SkankyDev\Queue\Job\MasterJob;

class SendScenarioJob extends MasterJob {

	public array $cmd;
	public string $topic;

	public function __construct(Module $module, Scenario $scenario){
		$datas = $scenario->lines;
		$result = [];
		foreach ($datas as $key => $value) {
			$result['line_'.$key] = $value;
		}
		$this->cmd = ['scenario' => $result,];
		$this->topic = $module->topic_cmd;

	}

	public function run():void{
		MqttSender::publish($this->topic, $this->cmd);
	}

}