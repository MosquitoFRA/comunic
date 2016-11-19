<?php
/**
 * Post a personnal video
 *
 *	@author Pierre HUBERT
 */

isset($_SESSION) OR exit('Invalid call - '.$_SERVER['PHP_SELF']);

if($_POST['idvideo'] != "" && $_POST['niveau_visibilite'] != "")
{
	//On vérifie si il faut prendre la dernière vidéo disponible
	if($_POST['idvideo'] == "last_one")
		$_POST['idvideo'] = id_video_plus_recente($_SESSION['ID'], $bdd); //Récupération de l'ID de la vidéo la plus récente

	if(isset_video($_POST['idvideo'], $_SESSION['ID'], $bdd))
	{
		if($_POST['niveau_visibilite'] <= 3)
		{
			//Ajout de la vidéo
			if($_SESSION['ID'] == $idPersonn)
			add_movie($_SESSION['ID'], $_POST['commentaire_video'], $_POST['idvideo'], $bdd, $_POST['niveau_visibilite']);
			else //Si c'est un amis
			add_movie($idPersonn, $_POST['commentaire_video'], $_POST['idvideo'], $bdd, $_POST['niveau_visibilite'], $_SESSION['ID']);
		}
		else
		{
			?><script>affiche_notification_erreur("Il y a une erreur dans votre requ&ecirc;te.");</script><?php //Vidéo indisponible
		}
	}
	else
	{
		?><script>affiche_notification_erreur("La vid&eacute;o dmand&eacute;e n'est pas disponible.");</script><?php //Vidéo indisponible
	}
}