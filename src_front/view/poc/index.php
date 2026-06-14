<?php
/**
 * POC — visualisation du pipeline de middlewares déclarés par attributs.
 * Vue volontairement autonome (pas de layout) pour rester isolée.
 *
 * @var string   $action    nom de l'action courante (index | secure)
 * @var string[] $declared  middlewares résolus depuis les attributs, dans l'ordre
 */
$this->setLayout(null);

use App\Middlewares\TrucMiddleware;
use App\Middlewares\BiduleMiddleware;

// À ce stade le pipeline est entièrement dépilé : les traces contiennent
// les phases before ET after de chaque middlewares.
$truc   = TrucMiddleware::$trace;
$bidule = BiduleMiddleware::$trace;
?>
<!doctype html>
<html lang="fr">
<head>
	<meta charset="utf-8">
	<title>POC Middleware — <?= e($action) ?></title>
	<style>
		body { font-family: ui-monospace, monospace; background:#0f1115; color:#d7dae0; padding:2rem; line-height:1.6; }
		h1 { color:#7aa2f7; } h2 { color:#9ece6a; margin-top:2rem; }
		a { color:#7aa2f7; }
		.box { background:#1a1d24; border:1px solid #2a2f3a; border-radius:8px; padding:1rem 1.25rem; margin:.5rem 0; }
		.step { padding:.15rem 0; }
		.before { color:#9ece6a; } .after { color:#f7768e; }
		.muted { color:#6b7280; }
		code { background:#2a2f3a; padding:.1rem .35rem; border-radius:4px; }
	</style>
</head>
<body>
	<h1>POC Middleware par attributs</h1>
	<p class="muted">Action courante : <code><?= e($action) ?></code></p>

	<p>
		<a href="/poc">/poc</a> (Truc seul) &nbsp;|&nbsp;
		<a href="/poc/secure">/poc/secure</a> (Truc + Bidule)
	</p>

	<h2>Middlewares déclarés (ordre résolu)</h2>
	<div class="box">
		<?php if (empty($declared)): ?>
			<span class="muted">aucun</span>
		<?php else: ?>
			<?php foreach ($declared as $i => $spec): ?>
				<div class="step">
					<?= $i + 1 ?>. <code><?= e($spec['class']) ?></code><?php
						if (!empty($spec['args'])):
							echo ' <span class="muted">args:</span> <code>' . e(implode(', ', $spec['args'])) . '</code>';
						endif;
					?>
				</div>
			<?php endforeach; ?>
		<?php endif; ?>
	</div>

	<h2>Exécution réelle (ordre oignon)</h2>
	<div class="box">
		<?php
		// Reconstruit une timeline lisible. before en vert, after en rouge.
		$render = function (array $trace) {
			foreach ($trace as $step) {
				$cls = str_contains($step, ':before') ? 'before' : 'after';
				echo '<div class="step ' . $cls . '">' . e($step) . '</div>';
			}
		};
		?>
		<strong>TrucMiddleware</strong> <span class="muted">(classe — englobe tout)</span>
		<?php $render($truc); ?>
		<br>
		<strong>BiduleMiddleware</strong> <span class="muted">(action <code>secure</code> uniquement)</span>
		<?php empty($bidule) ? print('<div class="step muted">— pas exécuté sur cette action —</div>') : $render($bidule); ?>
	</div>

	<p class="muted">
		Les <span class="before">before</span> tournent avant le controller, les
		<span class="after">after</span> après, dans l'ordre inverse (oignon).
		Truc (classe) enveloppe Bidule (action) qui enveloppe le controller.
	</p>
</body>
</html>
