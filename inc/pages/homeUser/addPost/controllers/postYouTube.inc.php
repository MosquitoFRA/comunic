<?php
/**
 * Post a YouTube video
 *
 *	@author Pierre HUBERT
 */

isset($_SESSION) OR exit('Invalid call - '.$_SERVER['PHP_SELF']);

if($_POST['youtube'] != "")
{
	//Exemple d'URL YouTube :
	// https://www.youtube.com/watch?v=1MBU0yRgBTw
	
	if(strpbrk($_POST['youtube'], "<https://www.youtube.com/watch?v=>") != false OR strpbrk($_POST['youtube'], "<http://www.youtube.com/watch?v=>") != false)
	{
		$adresse = strstr($_POST['youtube'], "watch?v=");

		$adresse = str_replace("watch?v=", "", $adresse);
		
		if($adresse != "")
		{
			//Définition du texte de la vidéo
			$source = "";
			
			if($_POST['commentyoutube'] != "")
			{
				if(!preg_match('/endof/', $_POST['commentyoutube']))
				{
					$source .= $_POST['commentyoutube'];
				}
			}
			
			//Enregistrement de la vidéo
			//Ajout du texte
			if($_SESSION['ID'] == $idPersonn)
				ajouttexte($_SESSION['ID'], $source, $bdd, $niveau_visibilite, "youtube", $adresse);
			else //Si c'est un amis
				ajouttexte_amis($_SESSION['ID'], $idPersonn, $source, $bdd, $niveau_visibilite, "youtube", $adresse);
			
			//Message de succès
			?><script>affiche_notification_succes("La vid&eacute;o a bien &eacute;t&eacute; enregistr&eacute;e.");</script><?php
		}
		else
		{
			?><script>affiche_notification_erreur("L'adresse de la vid&eacute;o YouTube est incorrecte.");</script><?php
		}
	}
	else
	{
		?><script>affiche_notification_erreur("L'adresse de la vid&eacute;o YouTube est incorrecte. Le nom de domaine ou l'URL est incorrecte.");</script><?php
	}
}