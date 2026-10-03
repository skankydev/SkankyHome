<?php
namespace App\Utilities;

use SkankyDev\Utilities\Log;

/**
 * Log des échanges MQTT, dans logs/{date}-mqtt.log.
 * Propre à SkankyHome : le framework ne connaît pas MQTT.
 */
class MqttLog {

	public static function write(string $action, ?string $topic = null, ?string $message = null): void {
		$line = $action;
		if ($topic) {
			$line .= " | Topic: {$topic}";
		}
		if ($message) {
			$line .= " | Message: {$message}";
		}
		Log::info($line, 'mqtt');
	}
}
