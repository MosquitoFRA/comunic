<?php
	//Service Pierre, 2013
	//Attention: modifier ce fichier peut
	//Compromettre l'int�gralit� de la 
	//S�curit� du site. Merci de ne modifier
	//Ce fichier qu'apr�s avoir pris connaissance
	//Du reste des composants de ce site.
	//Merci de votre compr�hension.
	/*Zone de s�curit�: top secret */
	/*---------------------------------------------*/
	//Variables de redirection
	$redirection  = 'index.php'; //Adresse vers laquelle il y aura une redirection si l'utilisateur n'est pas connect�.
	
	//ATTENTION: NE JAMAIS MODIFIER LA SUITE
	//D�marrage de la session
	if(!isset($_SESSION)) session_start();
	
	//Pr�paration de la redirection
	$adresse_redirection = 'location: '.$redirection;
	
	//V�rification de la connexion vers une session
	if(!isset($_SESSION['ID']))
	{
		header($adresse_redirection);
		die();
	}
	
	/*------------------------------------------*/
	/*Fin de la zone de s�curit�e top secrete---*/
	//Merci de ne rien mettre apr�s ce texte.
?>