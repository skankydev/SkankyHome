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
	 * Log info générale
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
	 * Log une erreur (exception)
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
	 * Log warning
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
	 * Log pour les jobs de la queue
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
	 * Log pour le loop MQTT
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
	 * Log de debug (seulement en dev)
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
	 * Écrire dans un fichier de log
	 * @private
	 */
	private static function write(string $logFile, string $message): void {
		$logDir = dirname($logFile);
		
		if (!is_dir($logDir)) {
			mkdir($logDir, 0775, true);
		}
		
		file_put_contents($logFile, $message, FILE_APPEND);
	}

	/**
	 * Nettoyer les vieux logs (> X jours)
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