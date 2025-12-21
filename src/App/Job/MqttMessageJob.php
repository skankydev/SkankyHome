<?php 

namespace App\Job;

use App\Model\ModuleCollection;
use SkankyDev\Queue\Job\MasterJob;
use SkankyDev\Utilities\Log;
use SkankyDev\Utilities\Traits\CliMessage;
use \Exception;

class MqttMessageJob extends MasterJob {

	use CliMessage;

	public function __construct(public string $topic,public string $message){

	}

	public function run():void {
		$this->info($this->topic);
		$this->warning($this->message);
		$message = json_decode($this->message,true);
		$moduleName = explode('/', $this->topic);
		$moduleName = end($moduleName);
		if(!isset($message['cmd'])){
			Log::mqtt('pas de commande');
			return;
		}
		$cmd = $message['cmd'].'Cmd';
		if(!method_exists($this,$message['cmd'])){
			Log::mqtt('pas de methode');
			return;
		}
		$module = ModuleCollection::_findOne(['name'=>$moduleName]);
		if(!$module){
			Log::mqtt('pas de Module');
			return;
		}

		try{

			$this->$cmd($module,$message['data']??[]);

		} catch (Exception $e) {
			Log::mqtt('error : '.$e->getMessage());
			
		}
		

	}


	public function helloCmd(Module $module, array $data){
		if(isset($data['vesions'])){
			$module->version = $data['vesions'];
		}
		$cmd = [
			'timestamp' => time(),
		];
		MqttSender::publish($module->topic_cmd,$cmd);
	}
}