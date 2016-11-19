<?php
//Renvoi d'un fichier Javascript contenant la liste des Smiley
//pour l'ajout rapide de Smiley
header('Content-type: application/x-javascript');
//header('Expires: '.date( "D, d M Y G:i:s T" , time()+3600*30*12*24));

//Inclusion de la liste
include('liste_smile.php');

//Préparation du renvoi de la liste
?>var liste_smile = [<?php

//Sécurité du script Javascript
$count = 0;

//Affichage de la liste
foreach($liste_smiley as $afficher)
{
	//Sécurité du script Javascript
	if ($count != 0) echo ","; else $count = 1;
	
	//Début de l'envoi
	echo "['";
	
	//Sécurité du script PHP
	if(is_array($afficher[0]))
		echo $afficher[0][0];
	else
		echo $afficher[0];
	
	//Fin de l'envoi
	echo "', '".$afficher[1]."', '".$afficher[2]."']";
}

//Fermeture de la liste
?>];