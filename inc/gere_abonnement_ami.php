<?php
	//Sécurité
	if(!isset($gere_abonnement_ami))
		die("Invalid request !");
	if(!isset($_SESSION['ID']))
		die("Login required !");
	
	//Vérification de l'existence des variables nécessaires à l'exécution du script
	if(!isset($_GET['id']))
		die("Missing arguments!");
	
	//On commence par préparer l'exécution du script (avec sécurité)
	$id = $_GET['id']*1;
	
	//On vérifie si il s'agit de la même personne
	if($id == $_SESSION['ID'])
		die("Abonn&eacute;");
	
	//On vérifie que les deux personnes sont amies
	if(!detectesilapersonneestamie($_SESSION['ID'], $id, $bdd))
		die("You are not friend !");
		
	//On récupère les informations sur la table ami
	$sql = "SELECT * FROM amis WHERE (ID_personne = ?) && (ID_amis = ?) && (actif = 1)";
	$requeteamis = $bdd->prepare($sql);
	$requeteamis->execute(array($_SESSION['ID'], $id));
	
	//On renvoi le résultat suivant si la personne est amie ou non
	if(!$info_ami = $requeteamis->fetch())
	{
		//Fermeture de la requete
		$requeteamis->closeCursor();
		
		//Message d'erreur
		die("Une erreur a survenue. Merci de r&eacute;essayer ult&eacute;rieurement.");
	}
	
	//Fermeture de la requete
	$requeteamis->closeCursor();
	
	//On vérifie si il faut changer le statut d'abonnement
	if(isset($_GET['change']))
	{
		//On vérifie si il faut ajouter ou supprimer l'abonnement
		$statut = ($info_ami['abonnement'] == 0 ? 1 : 0);
		
		//On met à jour la base de données
		$sql = "UPDATE amis SET abonnement = ? WHERE ID_personne = ? AND ID_amis = ?";
		$requete = $bdd->prepare($sql);
		$requete->execute(array($statut, $_SESSION['ID'], $id));
		
		//On modifie la variable
		$info_ami['abonnement'] = $statut;
	}
	
	//On affiche le statut d'abonnement
	echo ($info_ami['abonnement'] == 0 ? "S'abonner" : "<img src='".path_img_asset('succes.png', "Vous &ecirc;tes abonn&eacute; &agrave; la personne.")."' /> Abonn&eacute;");