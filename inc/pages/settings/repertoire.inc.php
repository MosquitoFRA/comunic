<?php
/**
 * Change personnal URL settings
 *
 * @author Pierre HUBERT
 */

if(!isset($_SESSION))
	exit("Invalid call !");

//Vérification de l'existence d'une demande de modification
if(isset($_POST['choix_url']))
{
	if(verifie_validite_sous_repertoire($_POST['choix_url'], $bdd))
	{
		//Mise à jour de la BDD
		$sql = "UPDATE utilisateurs SET sous_repertoire = ? WHERE ID = ?";
		$requete = $bdd->prepare($sql);
		$requete->execute(array($_POST['choix_url'], $_SESSION['ID']));
		
		//Message de succès
		?><script>affiche_notification_succes('Le nouveau sous-r&eacute;pertoire virtuel a &eacute;t&eacute; enregistr&eacute; et entre en vigueur d&egrave;s maintenant.', '', 7); </script><?php
		
		//Rechargement des informations
		$afficher = cherchenomprenom($_SESSION['ID'], $bdd);
	}
	else
		echo "<script>affiche_notification_erreur('Impossible de continuer avec le sous-r&eacute;pertoire: soit il est d&eacute;ja utilis&eacute;, soit il est incorrect.', 'Erreur', 10); </script>";
}

//Message
?><h3>Choix du r&eacute;pertoire</h3><?php

//Explication
?><p>Sur cette page, vous pouvez choisir un sous-r&eacute;pertoire virtuel qui, lorsqu'il est ouvert, redirigera vers votre page.</p><?php

//Formulaire
?><form action="<?php echo $_SERVER['PHP_SELF']; ?>?c=<?php echo $_GET['c']; ?>" method="post">
	Choix de l'URL : <?php echo $urlsite; ?><input type="text" name="choix_url" value="<?php echo $afficher['sous_repertoire']; ?>" /> <input type="submit" value="Valider" />
</form><?php