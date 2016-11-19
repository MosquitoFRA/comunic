<?php
	//Sécurité
	if(!isset($_SESSION['ID']))
		die("Login required !");
	if(!isset($delete_avatar_img_background))
		die("Invalid request!");
	if(!isset($_SERVER['HTTP_REFERER']))
		die("Invalid call!");
	
	//Vérification de l'existence des variables
	if(!isset($_GET['type']))
		die("Invalid arguments !");
		
	//Vérifions que l'appel du fichier vient bien de parametres.php
	if(!preg_match("/parametres.php/", $_SERVER['HTTP_REFERER']))
		die("This request has been stopped for security reasons.");
		
	if($_GET['type'] == "avatar")
	{
		//Suppresion de l'avatar
		$type = "avatars";
	}
	elseif($_GET['type'] == "imgfond")
	{
		//Suppresion de l'avatar
		$type = "imgfond";
	}
	else
		die("Incorrect arguments!");
	
	//On définit l'adresse du fichier d'indication
	$adresse_fichier_indication = relativeUserDataFolder($type.'/adresse_'.$type.'/'.$_SESSION['ID'].'.txt');
	
	//On commence par vérifier si une image de fond a été postée
	if(file_exists($adresse_fichier_indication))
	{
		//On a le fichier texte d'indication d'adresse
		//On vérifie tout de même si le fichier existe
		//Récupération de l'adresse indiquée par le fichier
		$adresse = file_get_contents($adresse_fichier_indication);
		
		//On vérifie si le fichier existe
		if(file_exists(relativeUserDataFolder($type."/".$adresse)))
		{
			//Le fichier existe ->
			//On supprime le fichier
			unlink(relativeUserDataFolder($type."/".$adresse));
		}
		
		//On supprime maintenant le fichier texte d'indication
		unlink($adresse_fichier_indication);
	}
	else
		echo "L'avatar n'existe pas...";
	
	//On redirige vers la page précédente
	header('Location: '.$_SERVER['HTTP_REFERER']);
	
	//On quitte le script (sécurité)
	die();