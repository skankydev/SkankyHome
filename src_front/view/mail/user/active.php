
<section>
	<header><h1><?php echo "Bonjour ".$user->login ?></h1></header>
	<section>
		<p>Merci de cliquer sur le lien suivant pour activer votre compte</p>
		<?php echo $this->link('cliquez ici', ['controller'=>'user','action'=>'active','params'=>['login'=>$user->login,'token'=>$user->verifToken->value]],['class'=>'']);?><br>
	</section>
	<footer>
		À bientôt.
	</footer>
</section>