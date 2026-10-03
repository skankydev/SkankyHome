<?php

namespace App\Utilities;

use PhpMqtt\Client\MqttClient;
use SkankyDev\Config\Config;

class MqttSender {
	
	
	public static function getClient(): MqttClient {
		
		$config = Config::get('mqtt');
		
		$client = new MqttClient(
			$config['host'], 
			$config['port'], 
			'skanky-publisher',
			MqttClient::MQTT_3_1
		);
		
		$client->connect(null, true);

		
		return $client;
	}
	
	public static function publish(string $topic, array|string $message, int $qos = 0): void {
		$client = self::getClient();
		
		// Si array, convertir en JSON
		if (is_array($message)) {
			$message = json_encode($message);
		}
		
		$client->publish($topic, $message, $qos);
		MqttLog::write('Published', $topic, $message);
		self::disconnect($client);
	}
	
	public static function disconnect(MqttClient $client): void {
		if ($client !== null) {
			$client->disconnect();
			$client = null;
			//MqttLog::write('Disconnected from broker');
		}
	}
}