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

namespace SkankyDev\Utilities;

use Exception;
use Throwable;

class Log {


	/**
	 * Writes an INFO entry to the daily context log file.
	 * @param string $context log file prefix, defaults to `skankydev`
	 */
	public static function info(string $message, string $context = 'skankydev'): void {
		$logFile = APP_FOLDER . '/logs/'.date('Y-m-d')."-{$context}.log";
		$formatted = sprintf(
			"[%s] INFO: %s\n",
			date('Y-m-d H:i:s'),
			$message
		);
		
		self::write($logFile, $formatted);
	}

	/**
	 * Writes a full exception (message, file, line, stack trace) to the daily error log.
	 */
	public static function error(Throwable $exception): void {
		$logFile = APP_FOLDER . '/logs/'.date('Y-m-d').'-error.log';
		$message = sprintf(
			"[%s] %s: %s in %s:%d\nStack trace:\n%s\n\n",
			date('Y-m-d H:i:s'),
			get_class($exception),
			$exception->getMessage(),
			$exception->getFile(),
			$exception->getLine(),
			$exception->getTraceAsString()
		);
		
		self::write($logFile, $message);
	}


	/**
	 * Writes a WARNING entry to the daily context log file.
	 * @param string $context log file prefix, defaults to `skankydev`
	 */
	public static function warning(string $message, string $context = 'skankydev'): void {
		$logFile = APP_FOLDER . '/logs/'.date('Y-m-d')."-{$context}.log";
		$formatted = sprintf(
			"[%s] WARNING: %s\n",
			date('Y-m-d H:i:s'),
			$message
		);
		
		self::write($logFile, $formatted);
	}

	/**
	 * Writes a job lifecycle event to the daily jobs log.
	 * @param string      $jobName FQCN of the job class
	 * @param string      $status  e.g. `queued`, `processing`, `completed`, `failed`
	 * @param string|null $details optional extra context
	 */
	public static function job(string $jobName, string $status, ?string $details = null): void {
		$logFile = APP_FOLDER . '/logs/'.date('Y-m-d').'-jobs.log';
		$message = sprintf(
			"[%s] [%s] %s",
			date('Y-m-d H:i:s'),
			strtoupper($status),
			$jobName
		);
		
		if ($details) {
			$message .= " - {$details}";
		}
		
		$message .= "\n";
		
		self::write($logFile, $message);
	}

	/**
	 * Writes an MQTT event to the daily mqtt log.
	 * @param string      $action  e.g. `Published`, `Received`
	 * @param string|null $topic   MQTT topic
	 * @param string|null $message raw message payload
	 */
	public static function mqtt(string $action, ?string $topic = null, ?string $message = null): void {
		$logFile = APP_FOLDER . '/logs/'.date('Y-m-d').'-mqtt.log';
		
		$formatted = sprintf(
			"[%s] %s",
			date('Y-m-d H:i:s'),
			$action
		);
		
		if ($topic) {
			$formatted .= " | Topic: {$topic}";
		}
		
		if ($message) {
			$formatted .= " | Message: {$message}";
		}
		
		$formatted .= "\n";
		
		self::write($logFile, $formatted);
	}

	/**
	 * Writes a DEBUG entry to the daily debug log. No-op if the DEBUG constant is not set or false.
	 */
	public static function debug(string $message, array $context = []): void {
		// Ne log que si en mode debug
		if (!defined('DEBUG') || !DEBUG) {
			return;
		}
		
		$logFile = APP_FOLDER . '/logs/'.date('Y-m-d').'-debug.log';
		
		$formatted = sprintf(
			"[%s] DEBUG: %s",
			date('Y-m-d H:i:s'),
			$message
		);
		
		if (!empty($context)) {
			$formatted .= "\nContext: " . json_encode($context, JSON_PRETTY_PRINT);
		}
		
		$formatted .= "\n\n";
		
		self::write($logFile, $formatted);
	}

	/**
	 * Appends a message to a log file, creating the directory if needed.
	 */
	private static function write(string $logFile, string $message): void {
		$logDir = dirname($logFile);
		
		if (!is_dir($logDir)) {
			mkdir($logDir, 0775, true);
		}
		
		file_put_contents($logFile, $message, FILE_APPEND);
	}

	/**
	 * Deletes log files older than $days days from the logs directory.
	 */
	public static function cleanup(int $days = 10): void {
		$logDir = APP_FOLDER . '/logs';
		
		if (!is_dir($logDir)) {
			return;
		}
		
		$files = glob($logDir . '/*.log');
		$limit = time() - ($days * 86400);
		
		foreach ($files as $file) {
			if (filemtime($file) < $limit) {
				unlink($file);
			}
		}
	}
}