<?php
/**
 * Change Piwik settings
 *
 * @author Pierre HUBERT
 */

if(!isset($_SESSION))
	exit("Invalid call !");

//On vérifie si une demande de modification a été envoyée
if(isset($_POST['send']))
{
	//On vérifie quel a été le choix de l'utilisateur
	$allow_piwik = (isset($_POST['allow_piwik']) ? 1 : 0);
	
	//On met à jour la base de données
	update_sql("utilisateurs", "allow_piwik = ?", "ID = ?", $bdd, array($allow_piwik, $_SESSION['ID']));
	
	//On applique le nouveau choix
	//Si Piwik est interdit, on l'applique dès maintenant
	if($allow_piwik == 0)
		$_SESSION['block_piwik'] = true;
	//Sinon, on s'assure que son autorisation est bien appliquée.
	elseif(isset($_SESSION['block_piwik']))
		unset($_SESSION['block_piwik']);
	
	//On actualise les informations de l'utilisateurs
	$afficher = cherchenomprenom($_SESSION['ID'], $bdd);
}
?><style type="text/css">
	.piwik_manager {
		text-align: justify;
	}
	
	.form_piwik_manager {
		text-align: center;
	}
</style>
<div class="piwik_manager">
	<h3>Gestion de Piwik</h3>
	<p>Piwik est un outils d'analyse d'audience d&eacute;velopp&eacute; pour les sites web.</p>
	<p>Piwik est le syst&egrave;me d'analyse d'audience de site web le plus respectueux de la vie priv&eacute;e du march&eacute; : les informations sont syst&eacute;matiquements anonymis&eacute;es avant d'&ecirc;tre enregistr&eacute;es, de plus si vous activer activer "Do No Track" sur votre navigateur, celui-ci n'envoi pas de donn&eacute;es vers le serveur. Sachez &eacute;galement que Piwik a fait l'objet d'une d&eacute;rrogation de la part de la CNIL, les webmasters optant pour ce dernier n'ont pas &agrave; afficher de banni&egrave;res sur leur site informant leurs utilisateurs que le site recueille des informations, et ce &agrave; deux conditions :</p>
	<ol>
		<li>Que les adresses IP des utilisateurs soient anonymis&eacute;es avant d'&ecirc;tre envoy&eacute;es au serveur.</li>
		<li>Que les sites web affichent une page telle que celle-ci pour informer les utilisateurs et leur permettre de d&eacute;sactiver Piwik sur leur site.</li>
	</ol>
	<p>Nous avons besoin des informations envoy&eacute;es par Piwik afin de savoir notamment quels sont les navigateurs les plus utilis&eacute;s par nos utilisateurs afin de faire &eacute;voluer Comunic dans le but de le rendre plus performant avec ces derniers. Piwik nous permet &eacute;galement de d&eacute;terminer l'audience de Comunic par de simples graphiques affichant des valeurs approch&eacute;es de l'&eacute;volution des visites du site.<br />Cependant nous mettons un point d'honneur, en accord avec les directives de la CNIL, de vous permettre de d&eacute;sactiver Piwik. Cette d&eacute;sactivation entre en vigueur d&egrave;s que vous vous connectez &agrave; votre compte Comunic.</p>
	<p>N'h&eacute;sitez pas &agrave; <a href="contact.php">nous contacter</a> pour plus d'informations</p>

	<form class="form_piwik_manager" action="<?php echo $_SERVER['PHP_SELF']; ?>?c=<?php echo $_GET['c']; ?>" method="post">
		<div class="input-control switch">
			<label>
				<input type="checkbox" name="allow_piwik" <?php echo ($afficher['allow_piwik'] == 1 ? "checked" : ""); ?> />
				<span class="check"></span>
				Autoriser Piwik <i>(Activ&eacute; par d&eacute;faut)</i>
			</label>
		</div>
		
		<!-- Valider -->
		<input type="hidden" name="send" value="1" />
		<input type="submit" value="Enregistrer" />
	</form>
</div>