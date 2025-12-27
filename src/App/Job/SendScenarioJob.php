<?php 

namespace App\Job;

use App\Model\Document\Module;
use App\Model\Document\Scenario;
use App\Utilities\MqttSender;
use SkankyDev\Queue\Job\MasterJob;

class SendScenarioJob extends MasterJob {

	public array $cmd;
	public string $topic;
	public string $scenarioId;

	public function __construct(Module $module, Scenario $scenario){
		$datas = $scenario->lines;
		$this->scenarioId = $scenario->id;
		$result = [];
		foreach ($datas as $key => $value) {
			$result['line_'.$key] = $value;
		}
		$this->cmd = ['scenario' => $result,];
		$this->topic = $module->topic_cmd;

	}

	public function run():void{
		$json = json_encode($this->cmd['scenario']);
		$totalSize = strlen($json);
		$chunkSize = 1000; // bytes par chunk
		$totalChunks = ceil($totalSize / $chunkSize);
		
		// Message de début
		MqttSender::publish($this->topic, [
			'scenario_start' => [
				'id' => $this->scenarioId,
				'total_chunks' => $totalChunks,
				'total_size' => $totalSize
			],
		]);
		
		usleep(100000);
		
		// Envoyer chaque chunk
		for ($i = 0; $i < $totalChunks; $i++) {
			$chunk = substr($json, $i * $chunkSize, $chunkSize);
			
			MqttSender::publish($this->topic, [
				'scenario_chunk' => [
					'id' => $this->scenarioId,
					'chunk_index' => $i,
					'chunk_data' => $chunk
				]
			]);
			
			usleep(50000);
		}
		
		// Message de fin
		MqttSender::publish($this->topic, [
			'scenario_end' => [
				'id' => $this->scenarioId
			]
		]);
	}

}