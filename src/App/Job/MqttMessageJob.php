<?php 

namespace App\Job;

use App\Model\Document\Module;
use App\Model\ModuleCollection;
use App\Utilities\MqttSender;
use SkankyDev\Queue\Job\MasterJob;
use SkankyDev\Queue\Queue;
use SkankyDev\Utilities\Log;
use SkankyDev\Utilities\Traits\CliMessage;
use SkankyDev\Utilities\Traits\StringFacility;
use \Exception;

class MqttMessageJob extends MasterJob {

	use CliMessage, StringFacility;

	public function __construct(public string $topic,public string $message){

	}

	public function run():void {
		$this->info($this->topic);
		$this->warning($this->message);
		$message = json_decode($this->message,true);
		$moduleSlug = explode('/', $this->topic);
		$moduleSlug = end($moduleSlug);
		
		if(!isset($message['cmd'])){
			Log::mqtt('pas de commande');
			$this->error('pas de commande');
			return;
		}
		$cmd = $message['cmd'].'Cmd';
		if(!method_exists($this,$cmd)){
			Log::mqtt('pas de methode');
			$this->error('pas de methode');

			return;
		}
		$module = ModuleCollection::_findOne(['slug'=>$moduleSlug]);

		if(!$module){
			Log::mqtt('pas de Module '.$moduleSlug);
			$this->error('pas de Module '.$moduleSlug);
			return;
		}

		try{
			$this->$cmd($module,$message['data']??[]);
		} catch (Exception $e) {
			Log::mqtt('error : '.$e->getMessage());
			$this->error('error : '.$e->getMessage());
		}
	}

	public function forwardCmd(Module $module, array $data): void {
		if(!isset($data['target']) || !isset($data['cmd'])){
			$this->error('forward: target ou cmd manquant');
			return;
		}
		$target = ModuleCollection::_findOne(['slug' => $data['target']]);
		if(!$target){
			$this->error('forward: module cible introuvable : '.$data['target']);
			return;
		}
		Queue::push(new SendCommandJob($target, $data['cmd'], $data['data'] ?? []));
	}

	public function helloCmd(Module $module, array $data){
		if(isset($data['version'])){
			$module->version = $data['version'];
			ModuleCollection::_save($module);
		}
		$cmd = [
			'timestamp' => time(),
		];
		MqttSender::publish($module->topic_cmd,$cmd);
	}
}