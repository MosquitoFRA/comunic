<?php
/**
 * If two personns are friend, this file check
 * the second one has right to make posts on 
 * the first one page.
 *
 *	@author Pierre HUBERT
 */

isset($_SESSION) OR exit('Invalid call - '.$_SERVER['PHP_SELF']);

if($afficher['autoriser_post_amis'] == 1 and detectesilapersonneestamie($_SESSION['ID'], $idPersonn, $bdd))
{
	//Requête de recherche (en fait, on regarde la ligne "ami" du point de vue de l'autre personne...)
	$sql = "SELECT * FROM amis WHERE (ID_personne = ?) && (ID_amis = ?) && (actif = 1)";
	
	//Exécution de la requête
	$requeteamis = $bdd->prepare($sql);
	$requeteamis->execute(array($idPersonn, $_SESSION['ID']));
	
	//On renvoi le résultat suivant si la personne est amie ou non
	if(!$verifierautorisation = $requeteamis->fetch())
	{
		//On ne possède pas de droit
	}
	else
	{
		//On vérifie si l'on a le droit
		if($verifierautorisation['autoriser_post_page'] == 1)
			$allowcreatepost = 1; //On l'a!
	}

	//Fermeture de la requete
	$requeteamis->closeCursor();
}