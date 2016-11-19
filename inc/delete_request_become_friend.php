<?php
	//Sécurité
	if(!isset($delete_request_become_friend))
		die("Invalid request!");
	if(!isset($_SESSION['ID']))
		die("Login Required!");
	
	//Vérification de l'existence des variables
	if(!isset($_GET['id']))
		die("Missing arguments!");
	
	//On vérifie le contenu des variables
	$id = $_GET['id']*1;
	if($id == 0)
		die("Invalid ID !");
	
	//On supprime maintenant la demande, qu'elle n'existe ou pas (avec sécurité)
	$sql = "DELETE FROM amis WHERE ID_personne = ? AND actif = 0 AND ID_amis = ?";
	$requete = $bdd->prepare($sql);
	$requete->execute(array($id, $_SESSION['ID']));
	
	//Redirection vers la page de l'amis
	header('Location: index.php?id='.$id);