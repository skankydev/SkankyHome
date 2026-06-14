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

namespace SkankyDev\Http\Middleware\Attribute;

use Attribute;

/**
 * Declares the middlewares attached to a controller or one of its actions.
 *
 * Because routing is convention-based (no explicit route to hang middlewares on),
 * the controller/action is the stable target — so the attribute is read there at
 * dispatch time by MiddlewareManager.
 *
 * - On a class  → applies to every action of the controller.
 * - On a method → applies to that action only (in addition to class-level ones).
 *
 * Repeatable, and accepts several middlewares at once:
 *   #[Middleware(AuthMiddleware::class)]
 *   #[Middleware(TrucMiddleware::class, BiduleMiddleware::class)]
 *
 * Each value is either a fully qualified middleware class name or an alias
 * registered in the `class.middlewares` config map.
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class Middleware {

	/** @var string[] middleware class names (or config aliases) */
	public array $middlewares;

	public function __construct(string ...$middlewares) {
		$this->middlewares = $middlewares;
	}

}
