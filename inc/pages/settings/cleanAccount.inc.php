<?php
/**
 * lean account settings
 *
 * @author Pierre HUBERT
 */

if(!isset($_SESSION))
	exit("Invalid call !");

?>
<h3>Nettoyage du compte</h3>
<p><i>Vous permet de "nettoyer votre compte" en supprimant des traces tel que vos posts dans le chat priv&eacute; ou vos anciennes notifications.</i></p>
<p>Ce qui sera fait :</p>
<p>
	<ul>
		<li>
			Suppression de toute vos notifications et de toute les notifications dont vous &ecirc;tes &agrave; l'origine
		</li>
		<li>
			Suppression de toute vos posts dans le chat priv&eacute;
		</li>
	</ul>
</p>		
<form action='action.php?actionid=28' method="post">
	<p>Pour des raisons de s&eacute;curit&eacute;, veuillez saisir votre mot de passe : </p>
		<input type="password" name="password" placeholder="Mot de passe" />
		<input type='submit' value='Effectuer la maintenance' />
</form>
<?php