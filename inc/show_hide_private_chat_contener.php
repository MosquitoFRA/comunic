<?php
	//Sécurité
	if(!isset($_SESSION['ID']))
		die("Invalid Request.");
	if(!isset($verification_show_hide_private_chat))
		die("404 File not found.");
	
	//Vérification de l'existence des variables
	if(!isset($_GET['show']))
		die("Missing arguments.");
	
	//Préparation de la mise à jour
	$show = ($_GET['show'] == "1" ? 1 : 0);
	
	//On met à jour l'état de l'affichage du chat privé
	$sql = "UPDATE utilisateurs SET view_private_chat = ? WHERE ID = ?";
	$requete = $bdd->prepare($sql);
	$requete->execute(array($show, $_SESSION['ID']));
	
	//Message de succès
	echo "OK. ".$show;