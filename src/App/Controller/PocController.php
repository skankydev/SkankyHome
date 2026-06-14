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

namespace App\Controller;

use App\Middlewares\BiduleMiddleware;
use App\Middlewares\TrucMiddleware;
use SkankyDev\Controller\MasterController;
use SkankyDev\Http\Middleware\Attribute\Middleware;
use SkankyDev\Http\Middleware\MiddlewareManager;

/**
 * POC — démo du système de middlewares par attributs.
 *
 * Le #[Middleware] au niveau classe s'applique à TOUTES les actions.
 * Le #[Middleware] au niveau action ne s'applique qu'à elle.
 *
 *   /poc         → TrucMiddleware seul (hérité de la classe)
 *   /poc/secure  → TrucMiddleware (classe) + BiduleMiddleware (action)
 */
#[Middleware(TrucMiddleware::class)]
class PocController extends MasterController {

	public function index() {
		return $this->renderTrace('index');
	}

	#[Middleware(BiduleMiddleware::class)]
	public function secure() {
		debug('Coucou');
		return $this->renderTrace('secure');
	}

	/**
	 * Affiche la liste des middlewares déclarés (ordre résolu) pour l'action courante.
	 * La vue, elle, lira les traces d'exécution réelles — disponibles complètes
	 * (before + after) car la vue est rendue après le dépilage du pipeline.
	 */
	private function renderTrace(string $action) {
		$manager = new MiddlewareManager();

		return view('poc.index', [
			'action'   => $action,
			'declared' => $manager->attributeMiddlewares(static::class, $action),
		]);
	}

}
