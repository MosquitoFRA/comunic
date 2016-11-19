<?php
/**
 * Change avatar visibility settings
 *
 * @author Pierre HUBERT
 */

if(!isset($_SESSION))
	exit("Invalid call !");

//On vérifie si il faut changer le niveau de visibilité
if(isset($_POST['niveau_visibilite_avatar']))
{
	//Préparation du contrôle de la valeu
	$nouveau_niveau_visibilite_avatar = $_POST['niveau_visibilite_avatar'];
	
	//Contrôle de la valeur
	if($nouveau_niveau_visibilite_avatar == 1 OR $nouveau_niveau_visibilite_avatar == 2 OR $nouveau_niveau_visibilite_avatar == 3)
	{
		//Modification de la valeur
		if(modifie_niveau_visibilite_avatar($_SESSION['ID'], $nouveau_niveau_visibilite_avatar))
		{
			//Message de succès
			?><script type="text/javascript">affiche_notification_succes("Le niveau de visibilit&eacute; de l'avatar a &eacute;t&eacute; chang&eacute;.", "", 5);</script><?php
		}
		else
		{
			//Message d'erreur
			?><script type="text/javascript">affiche_notification_erreur("La modification ud niveau de visibilit&eacute; de l'avatar a &eacute;chou&eacute;e.", "", 5);</script><?php
		}
	}
	else
	{
		//Message d'erreur
		?><script type="text/javascript">affiche_notification_erreur("La valeur saisie pour le niveau de visibilit&eacute; de l'avatar est incorrecte.", "", 5);</script><?php
	}
}

//Récupération du niveau actuel de visibilité de l'avatar
$niveau_visibilite_avatar = get_niveau_visibilite_avatar($_SESSION['ID']);

?>
	<h3>Visibilit&eacute; de votre avatar</h3>
	<p><i>Cette fonctionalit&eacute; vous permet de choisir quelle type d'utilisateur pourra visualiser votre avatar.</i></p>
	<form action='<?php echo $_SERVER['PHP_SELF']; ?>?c=<?php echo $_GET['c']; ?>' method="post">
		<p>Quelle groupe de personne pourra voir votre avatar: </p>
		
			<!-- Choix du niveau avec comme pré-définit le niveau actuel -->
			<label><input type="radio" name="niveau_visibilite_avatar" value="1" <?php echo($niveau_visibilite_avatar == 1 ? "checked" : ""); ?> /> Moi et mes amis </label> <br />
			<label><input type="radio" name="niveau_visibilite_avatar" value="2" <?php echo($niveau_visibilite_avatar == 2 ? "checked" : ""); ?> /> Moi, mes amis et les personnes connect&eacute;es </label> <br />
			<label><input type="radio" name="niveau_visibilite_avatar" value="3" <?php echo($niveau_visibilite_avatar == 3 ? "checked" : ""); ?> /> Tout le monde </label> <br />
			<!-- Fin de: Choix de niveau avec comme pré-définit le niveau actuel -->
			
			<!-- Bouton de confirmation -->
				<input type="submit" value="Modifier" />
			<!-- Fin de: Bouton de confirmation -->
	</form>
	<!--<a href="action.php?actionid=4">Envoyer les donn&eacute;es par mail</a>-->
<?php