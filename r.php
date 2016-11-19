<?php
//Démarrage de la session
session_start();

//Init page
include('../inc/initPage.php');

//Détermination du chemin
$path = str_replace('.php', '', strstr($_SERVER['REQUEST_URI'], ".php"));

//Sécurité
$path = str_replace(array("admin3660", "../", ".php"), '', $path);

if($path != "")
{
	//On vérifie si il s'agit de javascript
	if(preg_match("<js/>", $path))
	{
		//En-tête javascript
		header('Content-type: application/x-javascript');
		
		$file = str_replace("/js/", '', $path);
		
		if($file == "liste_groupes.js" AND isset($_SESSION['ID']))
		{
			//Ouverture du fichier
			echo "var liste_groupes = [";
			
			//Listing des groupes de personnes
			$liste = list_groupes_personnes($_SESSION['ID'], $bdd);
			
			//Affichage de la liste
			foreach($liste as $afficher)
			{
				echo "[".$afficher['ID'].", '".str_replace("'", '\\'."'", $afficher['nom'])."'],";
			}
			
			//Fermeture du fichier
			echo "];";
		}
		elseif($file == "liste_smiley.js")
		{
			//Le fichier est durable
			header('Cache-Control:	max-age=1800');
		
			//Inclusion du fichier d'affichage de la liste
			include 'inc/smile_javascript.php';
		}
		elseif(file_exists("js/".$file))
		{
			echo file_get_contents("js/".$file);
		}
		else
			header("HTTP/1.0 404 Not Found");
	}
	else
		header("HTTP/1.0 404 Not Found"); //Fichier non trouvé
}
else
	header("HTTP/1.0 404 Not Found"); //Fichier non trouvé