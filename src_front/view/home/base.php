<?php 
$this->setTitle('Welcome');
$this->setLayout('layout.default');
?>

<section class="pb-xxl">
	<h1> Ceci est un titre </h1>
	<h2> Ceci est un titre </h2>
	<h3> Ceci est un titre </h3>
	<h4> Ceci est un titre </h4>
	<h5> Ceci est un titre </h5>
	<h6> Ceci est un titre </h6>
</section>

<section class="pb-xxl">
	
	
	
	<h2 class="glow-box"> Ceci est <u>glow-box</u></h2><br>
	<h2 class="pulse"> Ceci est <u>pulse</u></h2><br>
	<h2 class="glitch" data-text="Ceci est glitch"> Ceci est <u>glitch</u></h2><br>
	
</section>

<section class="pb-xxl">
	<p>Ceci est un <strong>paragraphe</strong> avec du texte normal, du <em>texte en italique</em>, du <u>texte souligné</u>, et un <a href="#">lien hypertexte</a>.</p>
	<p>Un autre paragraphe pour voir l'espacement et l'alignement.</p>
	<p>Lorem ipsum, dolor sit, amet consectetur adipisicing elit. Ea, dolore molestiae vero. Perspiciatis a, tempore dignissimos officiis, consectetur alias distinctio dolorum. Adipisci natus cum laudantium similique et dicta veniam deleniti, voluptatem nam nobis eligendi. Animi.</p>
	<div class="pb-s"><a href="#" class="btn"><i class="icon-play"></i> Lien stylisé comme un bouton</a> </div>
</section>

<section class="pb-xxl">

</section>

<section class="pb-xxl">

	<!-- Mix de tailles -->
	<div class="grid-layout pb-l pt-l">
		<div class="grid-third card">
			<div class="card-header">
				<h2 class="glitch" data-text="glitch">glitch</h2>
			</div>
			<div class="card-body">
				grid-third
			</div>
			<div class="card-footer">
				<button class="btn btn-primary"><i class="icon-info"></i>primary</button>
				<button class="btn-mini btn-primary"><i class="icon-info"></i></button>
			</div>
		</div>
		<div class="grid-third card card-primary">
			<div class="card-header">
				<h2 class="rainbow-underline rainbow-icon"><i class="icon-zap"></i>rainbow-underline</h2>
			</div>
			<div class="card-body">
				grid-third <br>
				<a href="#" class="btn-mini"><i class="icon-save"></i></a>
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
				<table border="1" cellpadding="5" cellspacing="0">
					<thead>
						<tr>
							<th>En-tête 1</th>
							<th>En-tête 2</th>
							<th>En-tête 3</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>Cellule</td>
							<td>Cellule</td>
							<td>Cellule</td>
							<td>
								<a href="#" class="btn-mini">E</a>
								<a href="#" class="btn-mini">V</a>
							</td>
						</tr>
						<tr>
							<td>Cellule</td>
							<td>Cellule</td>
							<td>Cellule</td>
							<td>
								<a href="#" class="btn-mini">E</a>
								<a href="#" class="btn-mini">V</a>
							</td>
						</tr>
						<tr>
							<td>Cellule</td>
							<td>Cellule</td>
							<td>Cellule</td>
							<td>
								<a href="#" class="btn-mini">E</a>
								<a href="#" class="btn-mini">V</a>
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
			<h3><i class="icon-minus-circle"></i>grid-quarter-short h3</h3>
		</div>
		<div class="grid-quarter-short card text-center">
			<h4><i class="icon-alert-octagon"></i>grid-quarter-short h4</h4>
		</div>
		<div class="grid-quarter-short card text-right">
			<h5><i class="icon-octagon"></i>grid-quarter-short h5</h5>
		</div>
	</div>
</section>
