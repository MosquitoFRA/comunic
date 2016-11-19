<?php
	//Sécurité
	if(!isset($_SESSION['ID']))
		die("Invalid Request.");
	if(!isset($verification_change_auteur_chat))
		die("404 File not found.");
	
	//Vérification de l'existence des variables
	if(!isset($_GET['size']))
		die("Missing arguments.");
	
	//Préparation de la mise à jour
	$size = ($_GET['size'] > 20 ? $_GET['size'] : 150);
	$size = ($size <= 220 ? $size : 150);
	
	//On met à jour l'état de l'affichage du chat privé
	$sql = "UPDATE utilisateurs SET height_private_chat = ? WHERE ID = ?";
	$requete = $bdd->prepare($sql);
	$requete->execute(array($size, $_SESSION['ID']));
	
	//Message de succès
	echo "OK. ".$size;