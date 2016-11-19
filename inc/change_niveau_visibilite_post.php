<?php
	//Service Pierre
	// 2015 - Tous droits réservés
	
	//Fichier permettant de changer le niveau de visibilité d'un post
	
	//Sécurité
	if(!isset($verification_visibilite))
		die(); //Sécurité
	if(!isset($_SESSION['ID']))
		die(); //Sécurité
		
	//Vérification de l'existence des variables requises
	if(isset($_GET['nouveau_niveau_visibilite']) && isset($_GET['idtexte']))
	{
		//Définition des variables
		$nouveau_niveau_visibilite = $_GET['nouveau_niveau_visibilite'];
		$idtexte = $_GET['idtexte'];
		
		if($nouveau_niveau_visibilite*1 != 0 AND $idtexte*1 != 0)
		{
			//Vérification du niveau de visibilite
			if($nouveau_niveau_visibilite > 0 AND $nouveau_niveau_visibilite < 4)
			{
				//Mise à jour de la video
				$sql = "UPDATE texte SET niveau_visibilite = ? WHERE (ID_personne = ? AND ID_amis = 0 AND ID = ?) OR (ID_amis = ? AND ID = ?)";
				
				//Exécution de la requête
				$requete = $bdd->prepare($sql);
				$requete->execute(array($nouveau_niveau_visibilite*1, $_SESSION['ID'], $idtexte, $_SESSION['ID'], $idtexte)); 
				
				echo "OK. Fini.";
			}
			else
			{
				die("Niveau de visibilite incorrect.<br />");
			}
		}
	}