<?php
	//Sécurité
	if(!isset($ajax_search_users))
		die('Invalid Request!');
	if(!isset($_SESSION['ID']))
		die('Login required!');
	
	//Vérification de la présence des variables
	if(!isset($_GET['search']))
		die('Missing arguments');
	
	if($_GET['search'] == "")
		die('Invalid arguments !');
	
	//On envoi l'en-tête
	header("Content-type: text/plain");
	
	//On effectue la recherche
	$search = searchuser(corrige_accent_javascript($_GET['search']), $bdd, 5);
	
	//On définit les variables
	$separateur_user = "<|>";
	$separateur_info = "*!*";
	
	//On renvoi les résultats
	foreach($search as $i=>$renvoi)
	{
		//Si la requête n'est pas la première, on met un séparateur
		if($i != 0)
			echo $separateur_user;
		
		//On affiche les informations sur la personne (avec les séparateurs
		echo corrige_caracteres_speciaux($renvoi['ID'].$separateur_info.$renvoi['prenom']." ".$renvoi['nom'].$separateur_info.avatar($renvoi['ID'], './', 32, 32));
	}