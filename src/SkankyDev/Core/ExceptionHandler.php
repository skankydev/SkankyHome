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

namespace SkankyDev\Exception;

use Exception;
use SkankyDev\Utilities\Log;
use Throwable;

class ExceptionHandler {
	
	
	public function __construct(protected bool $debug = true) { }
	
	/**
	 * Gérer une exception
	 */
	public function handle(Throwable $exception): void {
		// Logger l'erreur
		Log::error($exception);
		
		// Afficher selon le mode
		if ($this->debug) {
			$this->renderDebug($exception);
		} else {
			$this->renderProduction($exception);
		}
	}
	
	/**
	 * Affichage debug (développement)
	 */
	protected function renderDebug(Throwable $exception): void {
		http_response_code(500);
		ob_start();
		?>
<html>
<head>
	<title>Exception</title>
	<link href="/dist/styles.css" rel="stylesheet" type="text/css">
	<script src="/dist/app.js" type="module" ></script>
</head>
<body id="MainBody">
	<section id="MainContainer">
		<header id="Header">
			<div class="layout-header">
				<a href="/" class="main-title">SkankyHome</a>
			</div>
			<hr class="rainbow-diviser">
		</header>
	</section>

	<section id="Contents">
		<header class="pb-m">
			<h1 class="glitch" data-text="Error <?= $exception->getCode() ?>">
				Error <?= $exception->getCode() ?><br>
			</h1>
			<div class="legend"><?= $exception->getMessage() ?></div>
		</header>
		<div class="pb-m">
			<div><span  class="text-error">Class</span> : <?= get_class($exception) ?> </div>
			<div><span  class="text-error">File</span> : <?= $exception->getFile() . ':' . $exception->getLine() ?></div>
		</div>
	
		<h2 class="corner-accent-info" >Stack Trace</h2>
		<div class="trace-list">
			<?php foreach ($exception->getTrace() as $value): ?>
				<div class="trace">
					<span class="trace-file"><?= isset($value['file'])? $value['file'] : ''; ?></span>
					<span class="trace-line"><?= isset($value['line'])?': '.$value['line']:''; ?></span>
					<span class="trace-class"><?= isset($value['class'])?$value['class']:''; ?></span>
					<span class="trace-type"><?= isset($value['type'])?$value['type']:''; ?></span>
					<span class="trace-function"><?= isset($value['function'])?$value['function']:''; ?>(<?= (!empty($value['args']))?'$arg['.count($value['args']).']':'' ; ?>)</span>
					<!-- <section class="trace-args"> 
					<?php if (!empty($value['args'])): ?>
						<?php debug($value['args'],' '); ?>
					<?php endif ?>
					</section> -->
					
				</div>
			<?php endforeach ?>	
		</div>
	</section>
</body>
</html>
		<?php 
		echo ob_get_clean();
	}
	
	/**
	 * Affichage production (utilisateur)
	 */
	protected function renderProduction(Throwable $exception): void {
		http_response_code(500);
		
		// Vue personnalisée ou message générique
		echo '<html><head><title>Erreur</title></head><body>';
		echo '<h1>Une erreur est survenue</h1>';
		echo '<p>Désolé, une erreur s\'est produite. Veuillez réessayer plus tard.</p>';
		echo '</body></html>';
	}

}