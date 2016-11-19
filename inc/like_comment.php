<?php
	//Sécurité
	if(!isset($_SESSION['ID']))
		die("Login required.");
	if(!isset($verification_edit_like_commentaire))
		die("Erreur 404.");
		
	//On vérifie l'existence de la variable
	if(!isset($_GET['idcommentaire']) || !isset($_GET['idtexte']) || !isset($_GET['aime']))
	{
		die("Incorrect request. (Action : ".$action.")");
	}
	
	//On récupère les informations sur le commentaire
	$sql = "SELECT * FROM commentaires WHERE ID = ?";
	$requete = $bdd->prepare($sql);
	$requete->execute(array($_GET['idcommentaire']));
	
	//Récupération du résultat
	if(!$info_commentaire = $requete->fetch())
	{
		//Fermeture de la requete
		$requete->closeCursor();
		
		//Message d'erreur
		die("404 Comment.");
	}
	
	//Fermeture de la requête
	$requete->closeCursor();
	
	//Vérification de la correspondance avec le texte
	if($info_commentaire['ID_texte'] != $_GET['idtexte'])
	{
		die("404 Text.");
	}
	
	//On télécharge les informations sur le commentaire
	$info_aime = requeteaime($info_commentaire['ID'], $bdd, "commentaire");
	
	//On détermine si il s'agit d'aimer ou de ne plus aimer
	if($info_aime['vousaimez'] == "1")
	{
		//Il s'agit de ne plus aimer
		//On retranche 1 au compteur
		$info_aime['personnesaiment']--;
		
		//On supprime l'entrée "d'aime" dans la bdd
		$sql = "DELETE FROM aime WHERE (ID_type = ?) && (ID_personne = ?) && (type = ?)";
		$suppression = $bdd->prepare($sql);
		$suppression->execute(array($info_commentaire['ID'], $_SESSION['ID'], "commentaire"));
	}
	else
	{
		//Il s'agit d'aimer le texte
		//On ajoute 1 au compteur
		$info_aime['personnesaiment']++;
		
		//On ajoute l'entrée dans la base de données
		$sql = "INSERT INTO aime (ID_type, ID_personne, Date_envoi, type) VALUES (?, ?, NOW(), ?)";
		$insertion = $bdd->prepare($sql);
		$insertion->execute(array($info_commentaire['ID'], $_SESSION['ID'], "commentaire"));
	}
	
	//On renvoie le nombre de personnes qui aiment maintenant ce texte
	echo $info_aime['personnesaiment'];