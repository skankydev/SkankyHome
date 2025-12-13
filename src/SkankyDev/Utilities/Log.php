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