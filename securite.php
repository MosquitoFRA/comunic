<?php
	//Service Pierre, 2013
	//Attention: modifier ce fichier peut
	//Compromettre l'intégralité de la 
	//Sécurité du site. Merci de ne modifier
	//Ce fichier qu'après avoir pris connaissance
	//Du reste des composants de ce site.
	//Merci de votre compréhension.
	/*Zone de sécurité: top secret */
	/*---------------------------------------------*/
	//Variables de redirection
	$redirection  = 'index.php'; //Adresse vers laquelle il y aura une redirection si l'utilisateur n'est pas connecté.
	
	//ATTENTION: NE JAMAIS MODIFIER LA SUITE
	//Démarrage de la session
	if(!isset($_SESSION)) session_start();
	
	//Préparation de la redirection
	$adresse_redirection = 'location: '.$redirection;
	
	//Vérification de la connexion vers une session
	if(!isset($_SESSION['ID']))
	{
		header($adresse_redirection);
		die();
	}
	
	/*------------------------------------------*/
	/*Fin de la zone de sécuritée top secrete---*/
	//Merci de ne rien mettre après ce texte.
?>