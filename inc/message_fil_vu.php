<?php
	//Sécurité
	if(!isset($_SESSION['ID']))
		die("Invalid Request.");
	if(!isset($message_fil_vu))
		die("404 File not found.");
		
	//Récupération des informations sur l'utilisateur
	$infos_user = cherchenomprenom($_SESSION['ID'], $bdd);
	
	//Modification dans la base de données
	update_sql("utilisateurs", "vu_message_info_fil = 1", "ID = ?", $bdd, array($_SESSION['ID']));
		echo "Succes";
	