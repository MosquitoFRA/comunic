<?php
	//Sécurité
	if(!isset($_SESSION['ID']))
		die("Invalid Request.");
	if(!isset($ouvre_ferme_session_chat))
		die("404 File not found.");
	
	//Vérification de l'existence des variables
	if(!isset($_GET['id']))
		die("Missing arguments.");
	
	//On crèe la variable liste si nécessaire
	if(!isset($_SESSION['private_chat'][$_SESSION['ID']]))
	{
		//Création des variables
		$_SESSION['private_chat'] = array();
		$_SESSION['private_chat'][$_SESSION['ID']] = array();
	}
	
	//Préparation de la mise à jour
	$id = $_GET['id']*1;
	
	//On détermine si il faut ajouter ou supprimer la conversation
	if(isset($_SESSION['private_chat'][$_SESSION['ID']][$id]))
	{
		//On supprime la conversation si demandé
		if(isset($_GET['remove']))
		{
			unset($_SESSION['private_chat'][$_SESSION['ID']][$id]);
		}
		else
		{
			//Rien à faire
			die("Nothing to be done.");
		}
	}
	//Dans ce cas il faut ajouter la conversation
	else
	{
		//Si une session temporaire d'affichage de conversations existe, il faut la fermer
		if(isset($_SESSION['private_chat'][$_SESSION['ID']][0]))
			unset($_SESSION['private_chat'][$_SESSION['ID']][0]);
		
		//Vérification de l'existence du compte
		if(!isset_account($id, $bdd) && $id != 0)
			die("Erreur: Le compte demand&eacute; n'existe pas !");
		else
		{
			//On ajoute la conversation à la liste
			//Vérification de l'existence du compte
			$_SESSION['private_chat'][$_SESSION['ID']][$id] = 1;
			
			//Si demandé, on effectue une redirection vers la conversation demandée
			if(isset($_GET['autoredirect']))
				header('Location: '.$urlsite.'privatechat.php?id='.$id);
		}
	}
	
	//Message de succès
	echo "OK. ".$id;