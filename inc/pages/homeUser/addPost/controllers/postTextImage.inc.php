<?php
/**
 * Post a text or an image
 *
 *	@author Pierre HUBERT
 */

isset($_SESSION) OR exit('Invalid call - '.$_SERVER['PHP_SELF']);

//An image does not require a valid text
if((isset($_FILES['image'])) AND ($_FILES['image']['error'] == 0)) 
	$image = true; 
else 
	$image = false;

//On vérifie si le texte n'est pas vide
if($_POST['texte'] != "" OR $image)
{
	//Vérification de la validité du post
	if(verifie_validite_ajout($_POST['texte']) OR $image)
	{
		
		//On vérifie si une image est incorporée au post
		if((isset($_FILES['image'])) AND ($_FILES['image']['error'] == 0))
		{
			//Envoi de l'image en ligne
			envoiimage(($_SESSION['ID'] == $idPersonn ? $_SESSION['ID'] : $idPersonn) , $_POST['texte'], $bdd, ($_SESSION['ID'] == $idPersonn ? 0 : $_SESSION['ID']), $niveau_visibilite);
		}
		else
		{
			//Ajout du texte
			if($_SESSION['ID'] == $idPersonn)
				ajouttexte($_SESSION['ID'], $_POST['texte'], $bdd, $niveau_visibilite);
			else //Si c'est un amis
				ajouttexte_amis($_SESSION['ID'], $idPersonn, $_POST['texte'], $bdd, $niveau_visibilite);
		}
	}
	else
	{
			//On affiche un message d'erreur
		?><script type='text/javascript'>affiche_notification_erreur("Votre texte est invalide : pas assez de caract&egrave;res diff&eacute;rents (au minimum 3).", "Erreur", 10);</script><?php
	}
}
else
{
	//On affiche un message d'erreur
	?><script type='text/javascript'>affiche_notification_erreur("L'ajout de textes vides est interdit !", "Erreur", 10);</script><?php
}