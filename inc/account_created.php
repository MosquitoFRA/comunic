<?php
	//Sécurité
	if(!isset($account_created_check) OR !isset($mail))
		die('Invalid call!');
?><div class="metro account_created">
	<h1 class="titre">F&eacute;licitations !</h1>
	<p>Vous pouvez d&egrave;s &agrave; pr&eacute;sent utiliser Comunic pour communiquer avec vos amis! D&eacute;couvrez quelques-unes de ses fonctionalit&eacute;s :</p>
	<div class="img_account_created_contener"><?php echo code_inc_img(path_img_asset('discover_comunic_modifie.png'), "Fonctions de Comunic"); ?></div>
		
	<h2>C'est parti !</h2>
	<p>Connectez-vous d&egrave;s maintenant pour profiter de ses fonctionalit&eacute;s :</p>
	<form action="connecter.php<?php if(isset($redirect)) echo "?redirect=".urlencode($redirect); ?>" method="post" class="form_first_login">
		<div class="input-control password size4">
			<input type="password" value="" name="motdepasse" placeholder="Saisissez votre mot de passe"/>
		</div>
		<input type="hidden" name="mail" value="<?php echo $mail; ?>" />
		<button onClick="this.submit();">Connexion</button>
	</form>
</div>