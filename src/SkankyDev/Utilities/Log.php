<?php

namespace SkankyDev\Utilities;

use Exception;
use Throwable;

class Log {

	public static function error(Throwable $exception): void {
		$logFile = APP_FOLDER . '/logs/'.date('Y-m-d').'-error.log';
		$logDir = dirname($logFile);
		
		if (!is_dir($logDir)) {
			mkdir($logDir, 0775, true);
		}
		
		$message = sprintf(
			"[%s] %s: %s in %s:%d\nStack trace:\n%s\n\n",
			date('Y-m-d H:i:s'),
			get_class($exception),
			$exception->getMessage(),
			$exception->getFile(),
			$exception->getLine(),
			$exception->getTraceAsString()
		);
		
		file_put_contents($logFile, $message, FILE_APPEND);
	}

}