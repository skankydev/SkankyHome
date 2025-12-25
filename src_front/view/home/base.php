<?php 
$this->setTitle('Base css');
$this->setLayout('layout.default');
?>

<section class="pb-xxl">
	<h1 class="glow-box rainbow-icon"><i class="icon-cpu"></i>Page de construction</h1><br>
</section>

<section class="pb-xxl">
	<p>Ceci est un <strong>paragraphe</strong> avec du texte normal, du <em>texte en italique</em>, du <u>texte souligné</u>, et un <a href="#">lien hypertexte</a>.</p>
	<p>Un autre paragraphe pour voir l'espacement et l'alignement.</p>
	<p>Lorem ipsum, dolor sit, amet consectetur adipisicing elit. Ea, dolore molestiae vero. Perspiciatis a, tempore dignissimos officiis, consectetur alias distinctio dolorum. Adipisci natus cum laudantium similique et dicta veniam deleniti, voluptatem nam nobis eligendi. Animi.</p>
	<div class="pb-s"><a href="#" class="btn"><i class="icon-play"></i> Lien stylisé comme un bouton</a> </div>
</section>

<section class="p-m">
	<!-- Lien classique -->
	<a href="/page">Lien normal</a> <br>

	<!-- Lien sans underline -->
	<a href="/page" class="no-underline">Sans soulignement</a> <br>

	<!-- Lien avec effet neon -->
	<a href="/page" class="neon">Lien neon</a> <br>

	<!-- Lien externe -->
	<a href="https://google.com" target="_blank">Lien externe</a> <br>

	<!-- Lien coloré -->
	<a href="/page" class="link-success">Succès</a> <br>
	<a href="/page" class="link-error">Erreur</a> <br>
	<a href="/page" class="link-warning">Warning</a> <br>

	<!-- Lien discret -->
	<a href="/page" class="link-muted">Lien discret</a> <br>

	<!-- Lien style bouton -->
	<a href="/page" class="link-button">
		<i class="icon-arrow-right"></i>Voir plus
	</a> <br>	
</section>

<section class=" p-m pb-xxl">

	<!-- Mix de tailles -->
	<div class="grid-layout pb-l pt-l">
		<div class="grid-third card">
			<div class="card-header">
				<h2 class="glitch" data-text="glitch">glitch</h2>
			</div>
			<div class="card-body">
				grid-third <br>
				<a href="#" class="btn-mini"><i class="icon-save"></i></a>
			</div>
		</div>
		<div class="grid-third card card-primary">
			<div class="card-header">
				<h2 class="rainbow-underline rainbow-icon"><i class="icon-zap"></i>rainbow-underline</h2>
			</div>
			<div class="card-body">
				<span class="text-primary">text-primary</span>
			</div>
			<div class="card-footer">
				<button class="btn btn-primary"><i class="icon-info"></i>primary</button>
				<button class="btn-mini btn-primary"><i class="icon-info"></i></button>
			</div>
		</div>
		<div class="grid-third card">
			<div class="card-header">
				<h2 class="corner-accent">corner-accent</h2>
			</div>
			<div class="card-body">
				grid-third
			</div>
		</div>
	</div>

	<!-- Thirds -->
	<div class="grid-layout pb-l pt-l">
		<div class="grid-quarter card card-info">
			<div class="card-header"><h2 class="corner-accent-info">corner-accent-info</h2></div>
			<div class="card-body">
				<div class="text-info">text-info</div>
			</div>
			<div class="card-footer">
				<button class="btn btn-info"><i class="icon-info"></i>info</button>
				<button class="btn-mini btn-info"><i class="icon-info"></i></button>

			</div>
		</div>
		<div class="grid-quarter card card-success">
			<div class="card-header"><h2 class="corner-accent-success">corner-accent-success</h2></div>
			<div class="card-body">
				<div class="text-success">text-success</div>
			</div>
			<div class="card-footer">
				<button class="btn btn-success"><i class="icon-success"></i>success</button>
				<button class="btn-mini btn-success"><i class="icon-success"></i></button>
			</div>
		</div>
		<div class="grid-quarter card card-warning">
			<div class="card-header"><h2 class="corner-accent-warning">corner-accent-warning</h2></div>
			<div class="card-body">
				<div class="text-warning">text-warning</div>
			</div>
			<div class="card-footer">
				<button class="btn btn-warning"><i class="icon-warning"></i>warning</button>
				<button class="btn-mini btn-warning"><i class="icon-warning"></i></button>
			</div>
		</div>
		<div class="grid-quarter card card-error">
			<div class="card-header"><h2 class="corner-accent-error">corner-accent-error</h2></div>
			<div class="card-body">
				<div class="text-error">text-error</div>
			</div>
			<div class="card-footer">
				<button class="btn btn-error"><i class="icon-error"></i>error</button>
				<button class="btn-mini btn-error"><i class="icon-error"></i></button>
			</div>
		</div>
	</div>

	<!-- Short et Long -->
	<div class="grid-layout pb-l pt-l">
		<div class="grid-quarter-long card">
			<div class="card-header">
				<h2 class="pulse">pulse</h2>
			</div>
			<div class="card-body">
				<ul>
					<li>Élément de liste non ordonnée 1</li>
					<li>Élément de liste non ordonnée 2
						<ul>
							<li>Sous-élément 1</li>
							<li>Sous-élément 2</li>
						</ul>
					</li>
					<li>Élément de liste non ordonnée 3</li>
				</ul>
				<br>
				<ol>
					<li>Élément de liste ordonnée 1</li>
					<li>Élément de liste ordonnée 2</li>
					<li>Élément de liste ordonnée 3</li>
				</ol>
				<br>
				<dl>
					<dt>label1</dt><dd>data1</dd>
					<dt>label2</dt><dd>data2</dd>
					<dt>label3</dt><dd>data3</dd>
				</dl>
			</div>
		</div>
		<div class="grid-half-long card">
			<div class="card-header">
				<h2 class="double-line">double-line</h2>
			</div>
			<div class="card-body">
				<table>
					<thead>
						<tr>
							<th>En-tête 1</th>
							<th>En-tête 2</th>
							<th>En-tête 3</th>
							<th class="action">Action</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>Cellule</td>
							<td>Cellule</td>
							<td>Cellule</td>
							<td class="action">
								<a href="#" class="btn-mini btn-info"><i class="icon-info"></i></a>
								<a href="#" class="btn-mini btn-warning"><i class="icon-edit"></i></a>
							</td>
						</tr>
						<tr>
							<td>Cellule</td>
							<td>Cellule</td>
							<td>Cellule</td>
							<td class="action">
								<a href="#" class="btn-mini btn-info"><i class="icon-info"></i></a>
								<a href="#" class="btn-mini btn-warning"><i class="icon-edit"></i></a>
							</td>
						</tr>
						<tr>
							<td>Cellule</td>
							<td>Cellule</td>
							<td>Cellule</td>
							<td class="action">
								<a href="#" class="btn-mini btn-info"><i class="icon-info"></i></a>
								<a href="#" class="btn-mini btn-warning"><i class="icon-edit"></i></a>
							</td>
						</tr>
						<tr>
							<td>Cellule</td>
							<td>Cellule</td>
							<td>Cellule</td>
							<td class="action">
								<a href="#" class="btn-mini btn-info"><i class="icon-info"></i></a>
								<a href="#" class="btn-mini btn-warning"><i class="icon-edit"></i></a>
							</td>
						</tr>
						<tr>
							<td>Cellule</td>
							<td>Cellule</td>
							<td>Cellule</td>
							<td class="action">
								<a href="#" class="btn-mini btn-info"><i class="icon-info"></i></a>
								<a href="#" class="btn-mini btn-warning"><i class="icon-edit"></i></a>
							</td>
						</tr>
					</tbody>
					<tfoot>
						<tr>
							<td>footer</td>
							<td>footer</td>
							<td>footer</td>
							<td>footer</td>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>
		<div class="grid-quarter-short card text-left">
			<div class="card-header">
				<h3><i class="icon-minus-circle"></i>grid-quarter-short h3</h3>
			</div>
		</div>
		<div class="grid-quarter-short card text-center">
			<div class="card-header">
				<h4><i class="icon-alert-octagon"></i>grid-quarter-short h4</h4>
			</div>
		</div>
		<div class="grid-quarter-short card text-right">
			<div class="card-header">
				<h5><i class="icon-octagon"></i>grid-quarter-short h5</h5>
			</div>
		</div>
	</div>
</section>

<section class="p-m">
	<div class="card p-m">
		<?= $form->render() ?>
	</div>
</section>


<section class="p-m">
	<div class="card p-m">
		<!-- Tooltip simple (top par défaut) -->
		<button data-tooltip="Enregistrer le scénario">
			<i class="icon-save"></i>
		</button>

		<!-- Tooltip avec position -->
		<button data-tooltip="Ajouter un segment" data-position="bottom">
			<i class="icon-add"></i>
		</button>

		<button data-tooltip="Supprimer" data-position="right">
			<i class="icon-trash"></i>
		</button>

		<!-- Tooltip avec couleur -->
		<button data-tooltip="Attention !" data-variant="warning">
			<i class="icon-warning"></i>
		</button>

		<button data-tooltip="Erreur détectée" data-variant="error">
			<i class="icon-error"></i>
		</button>

		<button data-tooltip="Action réussie" data-variant="success">
			<i class="icon-success"></i>
		</button>

		<!-- Tooltip multiline -->
		<button data-tooltip="Ce bouton permet de prévisualiser&#10;le segment sur le module" data-multiline>
			<i class="icon-eye"></i>
		</button>

		<!-- Tooltip large -->
		<button data-tooltip="Information détaillée importante" data-size="large">
			<i class="icon-info"></i>
		</button>

		<!-- Tooltip avec delay -->
		<button data-tooltip="Attend 0.5s avant d'apparaître" data-delay>
			<i class="icon-help"></i>
		</button>

		<!-- Sur du texte -->
		<span data-tooltip="Ceci est un tooltip sur du texte">
			Survolez-moi
		</span>
	</div>
</section>