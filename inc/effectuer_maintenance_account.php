<?php
	//Sécurité
	if(!isset($_SESSION['ID']))
		die("Login required !");
	if(!isset($ok_maintenance))
		die("Invalid request!");
	if(!isset($_SERVER['HTTP_REFERER']))
		die("Invalid call!");
		
	//Vérifions que l'appel du fichier vient bien de parametres.php
	if(!preg_match("/parametres.php?/", $_SERVER['HTTP_REFERER']))
		die("This request has been stopped for security reasons.");
		
	//Vérifions que la variable nécessaire existe
	if(!isset($_POST['password']))
		die('Invalid arguments!');
		
	//On propose un lien vers la page précédente
	echo "<title>Comunic - Maintenance de votre compte</title>";
	echo "<a href='".$_SERVER['HTTP_REFERER']."'>Retour &agrave; la page pr&eacute;c&eacute;dente</a> <br /><br />";
	
	//Vérification du mot de passe
	echo "Contr&ocirc;le du mot de passe...";
	//Cryptage du mot de passe envoyé
	$afficher = cherchenomprenom($_SESSION['ID'], $bdd); //Récupération des informations de l'utilisateur
	if(crypt_password($_POST['password']) != $afficher['password'])
		die("Mot de passe invalide! Quitte le script.");
	else
		echo "Mot de passe correct.<br />";
	
	//On effectue la maintenance
	//Suppression des notifications
	echo "Suppression de vos notifications...<br />";
	$sql = "DELETE FROM notification WHERE ID_personne = ".$_SESSION['ID']." || ID_createur = ".$_SESSION['ID'];
	$resultat = $bdd->query($sql);
	echo "Termin&eacute;. <br />";
	
	//On vide le chat prive
	echo "Suppression de vos posts sur le chat priv&eacute;...<br />";
	$sql = "DELETE FROM chatprive WHERE ID_personne = ".$_SESSION['ID'];;
	$resultat = $bdd->query($sql);
	echo "Termin&eacute;. <br />";
	
	//On quitte le script (sécurité)
	die();