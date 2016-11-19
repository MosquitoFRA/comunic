<?php
	//Sécurité
	if(!isset($_SESSION['ID']))
		die("Missing login."); //L'utilisateur doit être connecté
	if(!isset($verification_supp_commentaire))
		die("Unreconized call."); //L'appel du fichier doit être fait depuis action.php

	//Vérification de l'existence des variables requises
	if(isset($_GET['idtexte']) AND isset($_GET['idcommentaire']))
	{
		if($_GET['idtexte'] != "" AND $_GET['idcommentaire'] != "" AND $_GET['idcommentaire'] != "%")
		{
			//Vérification de l'autorisation de suppression
			$ok = false;
			
			//Récupération des informations sur ce texte
			$sql = "SELECT * FROM texte WHERE ID = ?";
			$requete = $bdd->prepare($sql);
			$requete->execute(array($_GET['idtexte']));
			
			//Vérification de l'existence du texte
			if($verifier = $requete->fetch())
			{
				//Si le texte est sur la page de la personne alors l'utilisateur est autorisé à supprimer le texte
				if($verifier['ID_personne'] == $_SESSION['ID'] || $verifier['ID_amis'] == $_SESSION['ID'])
				{
					//C'est OK
					$ok = true;
				}
			}
			else
			{
				//Sécurité
				die("Erreur 3");
			}
			
			//Fermeture de la requête de vérification de texte
			$requete->closeCursor();
			
			//Récupération des informations de la liste des commentaires pour ce texte
			$liste_commentaire = affichecommentaire($_GET['idtexte'], $bdd);
			
			//Vérifions que le commmentaire existe
			$verification_ok = 0;
			foreach($liste_commentaire as $verifier)
			{
				if($verifier['ID_personne'] == $_SESSION['ID'] && $verifier['ID'] = $_GET['idcommentaire']);
				{
					//Vérification ok
					$ok = true;
				}
				
				// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
				// ! SECURITE - OBLIGATOIRE POUR EVITER LA SUPPRESSION SYSTEMATIQUE DE TOUS LES COMMENTAIRES DU SITE !
				// ! NE PAS SUPPRIMER 						  								     SERVICE PIERRE 2015 !
				// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
				// On vérifie qu'il s'agit du bon texte
				if($verifier['ID_texte'] != $_GET['idtexte'])
					die("Broke for security.");
			}
			
			//Vérification de la présence d'autorisation
			if($ok == true)
			{
				/*//Mise à jour du commentaire
				$sql = "DELETE FROM commentaires WHERE ID = ? AND ID_texte = ?";
				
				//Exécution de la requête
				$requete = $bdd->prepare($sql);
				$requete->execute(array($_GET['idcommentaire'], $_GET['idtexte'])); 
					
				//On supprime les 'aimes' du commentaire
				delete_aimes_type_id($_GET['idcommentaire'], "commentaire", $bdd);
				*/
				
				//Suppression du commentaire
				suppcom($_GET['idcommentaire'], $bdd);
				
				echo "OK. finished";
			}
			else
				die("You are not allowed to do that.");
		}
		else
			echo "Erreur 2";
	}
	else
	{
		echo "Erreur 1";
	}