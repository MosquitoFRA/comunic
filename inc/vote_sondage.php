<?php
	//Sécurité
	if(!isset($_SESSION['ID']))
		die("Login required.");
	if(!isset($ok_to_vote_sondage))
		die("404 File not found.");
	
	//On détermine la raison pour laquelle cette page a été appelée
	if(!isset($_GET['type']))
	{
		die("Erreur: Appel de la page incorrect !");
	}
	
	//Enregistrement du type d'appel
	$type = $_GET['type'];
	
	if($type == "vote")
	{
		//Exemple d'appel : http://192.168.1.5/divers/comunic/action.php?actionid=37&id_user=1&id_sondage=3&id_reponse=5&type=vote
		if(!isset($_GET['id_user']) OR !isset($_GET['id_sondage']) OR !isset($_GET['id_reponse']))
			die("Missing arguments !");
			
			//Enregistrement des informations
			$id_user = $_GET['id_user']*1;
			$id_sondage = $_GET['id_sondage']*1;
			$id_reponse = $_GET['id_reponse']*1;
			
			//Sécurité
			if($id_user < 1 OR $id_sondage < 1 OR $id_reponse < 1)
				die("Invalid arguments!");
			
			//On récupère les informations sur le sondage
			$infos_sondage = select_sql("sondage", "ID = ? AND ID_utilisateurs = ?", $bdd, array($id_sondage, $id_user));
			
			//On vérifie que le sondage existe
			if(count($infos_sondage) == 0)
				die("Le sondage n'a pas &eacute;t&eacute; trouv&eacute; !");
			
			//On récupère les informations sur la réponse du sondage
			$infos_reponse = select_sql("sondage_choix", "ID = ? AND ID_sondage = ?", $bdd, array($id_reponse, $id_sondage));
			
			//On vérifie que la réponse existe
			if(count($infos_reponse) == 0)
				die("La r&eacute;ponse au sondage n'exite pas !");
			
			//On vérifie maintenant si la personne a déjà voté
			if(!vote_personne_sondage($_SESSION['ID'], $id_sondage, $bdd))
			{
				//Enregistrement du vote dans la base de données
				insert_sql("sondage_reponse", "ID_utilisateurs, ID_sondage, ID_sondage_choix, date_envoi", "?, ?, ?, NOW()", $bdd, array($_SESSION['ID'], $id_sondage, $id_reponse));
				
				//Message de succès
				echo "La r&eacute;ponse a &eacute;t&eacute; prise en compte.";
			}
			else
				die("La personne a d&eacute;ja vot&eacute;.");
	}
	elseif($type == "cancel_vote")
	{
		//Vérification de sécurité
		if(!isset($_GET['id_sondage']) OR !isset($_GET['id_choix']))
			die("Missing arguments !");
		
		//Enregistrement des informations
		$id_sondage = $_GET['id_sondage'];
		$id_choix = $_GET['id_choix'];
		
		//Action sur la base de donnée
		delete_sql("sondage_reponse", "ID_utilisateurs = ? AND ID_sondage_choix = ? AND ID_sondage = ?", $bdd, array($_SESSION['ID'], $id_choix, $id_sondage));
		
		//Message de succès
		echo "La r&eacute;ponse au sondage a &eacute;t&eacute; supprim&eacute;e avec succ&egrave;s.";
	}
	else
		die("Cette raison '".$type."' n'est pas encore support&eacute;e par le syst&egrave;me.");