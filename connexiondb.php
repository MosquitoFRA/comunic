<?php
/**
 * Database connexion file
 * 
 * @author Pierre HUBERT
 */

//Récupération du fichier de configuration
include('inc/config/config.php');

//Connexion à la base de données
try { 
	$bdd = new PDO('mysql:host='.$hotedb.';dbname='.$nomdb, $userdb, $passworddb); 
} catch(Exception $e) {
	include(pagesRelativePath('common/head.php')); 
	include(pagesRelativePath('common/pageTop.php'));  
	echo'<p>Une erreur est survenue.<a href="index.php">R&eacute;essayer</a></p>'; 
	die(); 
}

?>