<?php
	//Sécurité
	if(!isset($_SESSION['ID']))
		die("Login required.");
	if(!isset($like_texte_page))
		die("Erreur 404.");
		
	//On vérifie l'existence de la variable
	if(!isset($_GET['id']) || !isset($_GET['aime']) || !isset($_GET['type']))
	{
		die("Invalid request. (Action: ".$action.")");
	}
	
	//On enregistre l'ID
	$id = $_GET['id']*1;
	
	//On détermine (séurité) si il s'agit d'un texte ou d'une page
	if($_GET['type'] == "texte")
	{
		//Il s'agit d'un texte
		$type = "texte";
		
		//On récupère les informations sur le texte pour vérifier si il existe
		$sql = "SELECT * FROM texte WHERE ID = ?";
		$requete = $bdd->prepare($sql);
		$requete->execute(array($id));
		
		//Récupération du résultat
		if(!$info_texte = $requete->fetch())
		{
			//Fermeture de la requete
			$requete->closeCursor();
			
			//Message d'erreur
			die("404 Text.");
		}
		
		//Fermeture de la requête
		$requete->closeCursor();
	}
	else
	{
		//Il s'agit d'une page
		$type = "page";
		
		//On vérifie si la personne existe
		if(!isset_account($id, $bdd))
		{
			die("This account doesn't exists.");
		}
	}
	
	//On télécharge les informations sur les aimes du type
	$info_aime = requeteaime($id, $bdd, $type);
	
	//On détermine si il s'agit d'aimer ou de ne plus aimer
	if($info_aime['vousaimez'] == "1")
	{
		//Il s'agit de ne plus aimer
		//On retranche 1 au compteur
		$info_aime['personnesaiment']--;
		$info_aime['vousaimez'] = 0;
		
		//On supprime l'entrée "d'aime" dans la bdd
		$sql = "DELETE FROM aime WHERE (ID_type = ?) && (ID_personne = ?) && (type = ?)";
		$suppression = $bdd->prepare($sql);
		$suppression->execute(array($id, $_SESSION['ID'], $type));
	}
	else
	{
		//Il s'agit d'aimer le texte
		//On ajoute 1 au compteur
		$info_aime['personnesaiment']++;
		$info_aime['vousaimez'] = 1;
		
		//On ajoute l'entrée dans la base de données
		$sql = "INSERT INTO aime (ID_type, ID_personne, Date_envoi, type) VALUES (?, ?, NOW(), ?)";
		$insertion = $bdd->prepare($sql);
		$insertion->execute(array($id, $_SESSION['ID'], $type));
	}
	
	//On prépare le code source pour l'affichage
	$vousaimez = $info_aime['vousaimez'];
	$personnesaiment = $info_aime['personnesaiment'];
	
	//On renvoie le code source pour le nouvel affichage
	
	if($vousaimez == 0)
	{			
		echo "<span class='aime' ><a onClick='like_text_page(".$id.", \"".$type."\", 0)' >";
			echo code_inc_img(path_img_asset('aime.png'));
		echo " ".$lang[33]."</a> </span>";
	}
	else
	{
		echo "<span class='aime' ><a onClick='like_text_page(".$id.", \"".$type."\", 1)' >";
			echo code_inc_img(path_img_asset('aimeplus.png'));
		echo " ".$lang[34]."</a> </span> ";
	}
	
	echo "&nbsp;";
	
	//Fin de l'affichage du code source
	if ($personnesaiment == 1)
	{
		echo " Une personne aime.";
	}
	elseif ($personnesaiment != 0)
	{
		echo " ".$personnesaiment." personnes aiment.";
	}