<?php 
/**
 * Copyright (c) 2025 SCHENCK Simon
 * 
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 * @copyright     Copyright (c) SCHENCK Simon
 *
 */

namespace App\Command;

use App\Job\MqttMessageJob;
use PhpMqtt\Client\Exceptions\MqttClientException;
use PhpMqtt\Client\MqttClient;
use SkankyDev\Command\MasterCommand;
use SkankyDev\Config\Config;
use SkankyDev\Queue\Queue;

class MqttLoop extends MasterCommand {

	static protected string $signature = 'mqtt-loop';
	static protected string $help = 'Ecout les message envoyer par les module';


	public function run(array $arg = []): void {
		try {
			$this->info('🚏 Mqtt Loop started');
			$config = Config::get('mqtt');
			$client = new MqttClient($config['host'], $config['port'], 'skanky-subscriber', MqttClient::MQTT_3_1);
			$client->connect(null, true);

			$client->subscribe('skankyhome/info/#', function (string $topic, string $message, bool $retained) {
				Queue::push(new MqttMessageJob($topic, $message));
			}, MqttClient::QOS_AT_MOST_ONCE);

			$client->loop(true);

			$client->disconnect();
		} catch (MqttClientException $e) {

			$this->error('Ya un truc qui marche plus dans le mqtt.');
		}
	}

}