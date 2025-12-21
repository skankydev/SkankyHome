<?php

namespace App\Utilities;

use PhpMqtt\Client\MqttClient;
use SkankyDev\Config\Config;
use SkankyDev\Utilities\Log;

class MqttSender {
	
	private static ?MqttClient $client = null;
	
	public static function getClient(): MqttClient {
		if (self::$client === null) {
			$config = Config::get('mqtt');
			
			self::$client = new MqttClient(
				$config['host'], 
				$config['port'], 
				'skanky-publisher',
				MqttClient::MQTT_3_1
			);
			
			self::$client->connect(null, true);
			Log::mqtt('Connected to broker');
		}
		
		return self::$client;
	}
	
	public static function publish(string $topic, array|string $message, int $qos = 0): void {
		$client = self::getClient();
		
		// Si array, convertir en JSON
		if (is_array($message)) {
			$message = json_encode($message);
		}
		
		$client->publish($topic, $message, $qos);
		Log::mqtt('Published', $topic, $message);
	}
	
	public static function disconnect(): void {
		if (self::$client !== null) {
			self::$client->disconnect();
			self::$client = null;
			Log::mqtt('Disconnected from broker');
		}
	}
}