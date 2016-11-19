<?php
//Script de d�connexion
//R�alis� pour le projet
//Nomm� Communic
//Et accessibles �
//tous
//Ce fichier ne 
//doit pas �tre
//Modifi� sauf
// Pour la redirection
//session_start();print_r($_SESSION); die();
//Inclusion de la configuration
include('inc/config/config.php');
	
	//D�finition de la variable de redirection
	$adresse = "index.php";
	
	//D�finition de la redirection
	$adresse_redirection = "location: ".$adresse;
	
	//Connexion � la session
	session_start();
	
	if(isset($_SESSION['logout_adress']))
	{
		//D�finition de l'adresse de redirection
		$adresse = $_SESSION['logout_adress'];
		$adresse_redirection = "Location: ".$_SESSION['logout_adress'];
		$preferenceredirection = "html";
		
		//Suppression de la variable (s�curit�)
		unset($_SESSION['logout_adress']);
	}

	//On supprime le multi-account
	if(isset($_SESSION['ID_parent']))
		unset($_SESSION['ID_parent']);
	
	//Destruction de la session
	unset($_SESSION['ID']);
	
	if($preferenceredirection == "headerphp")
	{
		//On v�rifie si on doit modifier l'adresse de redirection
		if(isset($_SESSION['roundcube_used']))
		{
			if($source = file_get_contents('inc/logout_with_roundcube.html'))
				unset($_SESSION['roundcube_used']);
			
			die(str_replace('%TARGET_REDIRECT%', $adresse, $source));
		}
		
		//Redirection vers la page demand�e
		header($adresse_redirection);
	}
	else
	{
		//Redirection HTML
		echo 'Please wait... <meta http-equiv="refresh" content="0; url='.$adresse.'" /> <a href="'.$adresse.'">It doesn\'t works ?</a> <a href="index.php">Home</a>';
		die();
	}
	
//Fin du script
//Rien ne doit
// Etre ajout�
// Apr�s ces
//commentaires