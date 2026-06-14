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

namespace App\Middlewares;

use SkankyDev\Http\Middleware\MiddlewareInterface;
use SkankyDev\Http\Request;

/**
 * POC middleware — same as TrucMiddleware, records its passage to illustrate
 * action-level (method) attribute resolution.
 */
class BiduleMiddleware implements MiddlewareInterface {

	/** @var string[] shared trace of the pipeline execution (POC observability) */
	public static array $trace = [];

	/**
	 * @param string $label valeur reçue depuis l'attribut #[Middleware(self::class, 'xxx')]
	 *                       — injectée par MasterFactory dans le constructeur.
	 */
	public function __construct(private string $label = 'youpi') {}

	public function handle(Request $request, callable $next): mixed {
		self::$trace[] = "Bidule:before({$this->label})";
		$response = $next($request);
		self::$trace[] = "Bidule:after({$this->label})";
		return $response;
	}

}
