<?php
/**
 * Project's main function file
 * For Comunic project use only
 * All rights reserved
 *
 * @author Pierre HUBERT
 * @date Began by the end of 2013
*/


//Avertissement : ce fichier nécessite l'inclusion de la configuration (config.php)

//Affichage de l'avatar
//Recherche de l'avatar de la personne
function avatar($idavatar, $urlsite="./", $whidth=32, $height=32, $id="", $juste_avatar = false)
{	
	//On définit l'adresse de l'avatar
	$adresse_avatar = "0.jpg";
	
	if(file_exists(relativeUserDataFolder("avatars/adresse_avatars/".$idavatar.".txt")))
	{
		//Contrôle du niveau de visibilité
		$niveau_visibilite_avatar = (get_niveau_visibilite_avatar($idavatar, $urlsite));
		
		//On définit que c'est n'est pas bon.
		$ok = false;
		
		//On contrôle l'avatar
		$ok = ($niveau_visibilite_avatar == 3 ? true : $ok); //On vérifie si l'avatar est ouvert
		
		//On vérifie si ce n'est pas encore bon et si l'utilisateur est connecté
		if(!$ok AND isset($_SESSION['ID']))
		{
			$ok = (($niveau_visibilite_avatar == 2) AND (isset($_SESSION['ID'])) ? true : $ok); //On vérifie si l'avatar est ouvert et si l'utilisateur est connecté
			$ok = ($idavatar == $_SESSION['ID'] ? true : $ok); //On vérifie si il s'agit de l'avatar de l'utilisateur actuel
			
			//On vérifie si ce n'est pas encore bon et si les deux personnes sont amies
			//Inclusion de la base de données
			include('connexiondb.php');
			
			if(!$ok AND detectesilapersonneestamie($_SESSION['ID'], $idavatar, $bdd))
			{
				//Dans ce ce cas c'est OK
				$ok = true;
			}
		}
		
		//Si possible, on change l'adresse de l'avatar
		if($ok)
			$adresse_avatar = file_get_contents(relativeUserDataFolder("avatars/adresse_avatars/".$idavatar.".txt"));
	}
	
		//Renvoi du résultat
		if(!$juste_avatar)
			return "<img src='".webUserDataFolder()."avatars/".$adresse_avatar."' whidth='".$whidth."' height='".$height."' id='".$id."' />";
		else
			return webUserDataFolder()."avatars/".$adresse_avatar;
}

//Affichage de l'image de fond
//Recherche de l'image de fond de la personne
function imgfond($id_img_fond, $urlsite="./", $whidth=32, $height=32, $id="", $juste_url = false)
{
	//On vérifie si un complément est disponible
	if(file_exists(relativeUserDataFolder("imgfond/adresse_imgfond/".$id_img_fond.".txt")))
		$url =  webUserDataFolder("imgfond/".file_get_contents(relativeUserDataFolder("imgfond/adresse_imgfond/".$id_img_fond.".txt")));
	else
		$url = webUserDataFolder("imgfond/0.jpg");
	
	if(!$juste_url)
		return "<img src='".$url."' whidth='".$whidth."' height='".$height."' id='".$id."' />";
	else
		return $url;
}

//Fonction permettant de définir le niveau de visibilité de l'avatar de l'utilisateur
//Valeur de retour : 
//					 1 - L'utilisateur et ses amis uniquement
//					 2 - L'utilisateur, ses amis et les personnes connectées
//					 3 - L'utilisateur, ses amis, les personnes connectées et non connectées -> Tout le monde
function get_niveau_visibilite_avatar($id, $urlsite = "./")
{
	//On définit qu'il est visible par tout le monde
	$niveau_visibilite_avatar = 3;
	
	//On vérifie si ce niveau de visibilité a été personalisé par l'utilisateur
	$adresse_fichier = relativeUserDataFolder("avatars/adresse_avatars/limit_view_".$id.".txt");
	if(file_exists($adresse_fichier))
	{
		//Récupération du contenu du fichier
		$content_file = file_get_contents($adresse_fichier);
		
		//Contrôle du contenu du fichier (on ne prendra en compte sa valeur que si elle ne présente un intérêt
		if( $content_file == 1 OR $content_file == 2 )
			$niveau_visibilite_avatar = $content_file; //On enregistre la nouvelle valeur
	}
	
	//Renvoi du résultat
	return $niveau_visibilite_avatar;
}

//Fonction de modification du niveau de visibilité de l'avatar
function modifie_niveau_visibilite_avatar($id_personne, $nouveau_niveau, $urlsite = "./")
{
	//Définition de l'adresse du fichier
	$adresse_fichier = relativeUserDataFolder("avatars/adresse_avatars/limit_view_".$id_personne.".txt");
	
	//Contrôle du nouveau niveau
	if($nouveau_niveau == 3)
	{
		//Niveau égal à trois -> Suppression du fichier
		if(file_exists($adresse_fichier))
			//Suppression du message
			return unlink($adresse_fichier);
		else
			//Rien à faire, retour positif
			return true;
	}
	elseif($nouveau_niveau == 2 OR $nouveau_niveau == 1)
	{
		//Edition du fichier et retour (positif ou négatif suivant la réponse)
		return file_put_contents($adresse_fichier, $nouveau_niveau);
	}
	else
		//Erreur
		return false;
}

//Fonction d'ajout de texte
function ajouttexte($idpersonne, $texte, $bdd, $niveau_visibilite = 2, $type="texte", $path = "", $annee_fin = 0, $mois_fin = 0, $jour_fin = 0, $url_page="", $titre_page = "", $description_page = "", $image_page = "")
{
	$sql = 'INSERT INTO texte (ID_personne, date_envoi, texte, niveau_visibilite, type, path, annee_fin, mois_fin, jour_fin, url_page, titre_page, description_page, image_page) VALUES (?, NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) ';
	
	//Exécution de la requete
	$insertion = $bdd->prepare($sql);
	$insertion->execute(array($idpersonne, corrige_echapement($texte), $niveau_visibilite, $type, $path, $annee_fin, $mois_fin, $jour_fin, $url_page, $titre_page, $description_page, $image_page));
	
	//Et la notification (uniquement si nécessaire => Si le post n'a pas un niveau égal à 3)
	if(!visibilite_privee($niveau_visibilite))
		sendnotification($_SESSION['ID'], "a ajout&eacute; un texte sur sa page.", $bdd, "page:".$_SESSION['ID']);
	else
		sendnotification($_SESSION['ID'], "a ajout&eacute; un texte sur sa page.", $bdd, "page:".$_SESSION['ID'], "", "texte", list_personnes_groupes($niveau_visibilite, $bdd));
}

//Fonction d'ajout de texte sur la page d'un amis
//Paramètres de la fonction
//$idpersonne : la personne qui ENVOI le texte
//$idamis la personne à qui est DESTINE le texte
//-----------------------------------------
//DB SQL
//ID_personne : la personne à qui est DESTINE le texte
//ID_amis : la personne qui ENVOI le texte
function ajouttexte_amis($idpersonne, $idamis, $texte, $bdd, $niveau_visibilite = 2, $type = "texte", $path = "", $annee_fin = 0, $mois_fin = 0, $jour_fin = 0, $url_page="", $titre_page = "", $description_page = "", $image_page = "")
{
	$sql = 'INSERT INTO texte (ID_personne, ID_amis, date_envoi, texte, niveau_visibilite, type, path, annee_fin, mois_fin, jour_fin, url_page, titre_page, description_page, image_page) VALUES (?, ?, NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) ';

	//Exécution de la requete
	$insertion = $bdd->prepare($sql);
	$insertion->execute(array($idamis, $idpersonne, corrige_echapement($texte), $niveau_visibilite, $type, $path, $annee_fin, $mois_fin, $jour_fin, $url_page, $titre_page, $description_page, $image_page));
	
	//La notification UNIQUEMENT SI LE POST EST OUVERT AUX AMIS
	if(!visibilite_privee($niveau_visibilite))
	{
		//And the notification
		$infopersonne = cherchenomprenom($idamis, $bdd);
		sendnotification($_SESSION['ID'], "a ajout&eacute; un texte sur la page de ".$infopersonne['prenom'].' '.$infopersonne['nom'].".", $bdd,  "page:".$infopersonne['ID'], $idamis);
	}
	else
	{
		//And the notification
		$infopersonne = cherchenomprenom($idamis, $bdd);
		sendnotification($_SESSION['ID'], "a ajout&eacute; un texte sur la page de ".$infopersonne['prenom'].' '.$infopersonne['nom'].".", $bdd,  "page:".$infopersonne['ID'], $idamis, "texte", list_personnes_groupes($niveau_visibilite, $bdd));
	}
	
	//Inclusion de la configuration
	include('inc/config/config.php');
	
	$info_destinataire = cherchenomprenom($idamis, $bdd);
	//Vérification de l'autorisation d'envoi de mails
	if($active_envoi_mail == "oui" && $info_destinataire['autorise_mail'] == 1)
	{
		//Envoi d'un message au demandé
		$info_amis = cherchenomprenom($idpersonne, $bdd);
		$send_mail = true;
		$sujet = "Nouveau texte sur votre page";
		$description_rapide = "Un de vos amis a post&eacute; un texte sur votre page.";
		$nom_destinataire = $info_destinataire['prenom']." ".$info_destinataire['nom'];
		$adresse_mail_destinataire = $info_amis['mail'];
		$texte_message = "
		<h3 class='titre'>Nouveau texte sur votre page</h3>
		<p>Un de vos amis a post&eacute; un texte sur votre page.</p>
		<p>Voici le texte:</p>
		<table align='center'>
			<tr><td>
				".avatar($idamis, $urlsite)."</td><td>
				<a href='".$urlsite."?id=".$idpersonne."'>".$info_amis['prenom']."</a><br />
				".$info_amis['nom']."
			</td></tr>
			<tr><td colspan='2'>
				".$texte."
			</td></tr>
		</table>
		<p><a href='".$urlsite."'>Connectez-vous</a> pour acc&eacute;der &agrave; toute les notifications de Comunic.</a></p>
		";
		
		//Envoi du message
		include('inc/envoi_mail.php');
	}

}

//Fonction de demande en amis
//Fonction nécessaire : liste_amis
function demandeamis($idpersonne, $iddemandeur, $bdd)
{
	//On vérifie si une demande n'a pas déjà été postée
	$liste_verification = liste_amis($idpersonne, $bdd, 0);
	
	//On vérifie
	foreach($liste_verification as $verifier)
	{
		if($verifier == $iddemandeur)
		{
			//On affiche un message d'erreur
			echo "<script>alert('La personne a déja été demandée en amis.');</script>";
			
			//On bloque le tout
			$bloque = 1;
		}
	}
	
	//On vérifie si un bloquage a été effectué
	if(!isset($bloque))
	{
		//On poste la demande
		$sql = "INSERT INTO amis (ID_personne, ID_amis) VALUES (?, ?)";
		
		//Exécution de la requete
		$insertionamis = $bdd->prepare($sql);
		$insertionamis->execute(array($idpersonne, $iddemandeur));
	
		//Inclusion de la configuration
		include('inc/config/config.php');
		
		//Vérification de l'autorisation d'envoi de mails
		$info_destinataire_demande = cherchenomprenom($idpersonne, $bdd);
		if($active_envoi_mail == "oui" && $info_destinataire_demande['autorise_mail'] == 1)
		{
			//Envoi d'un message au demandé
			$info_demandeur = cherchenomprenom($iddemandeur, $bdd);
			$send_mail = true;
			$sujet = "Demande d'amis de ".$info_demandeur['prenom']." ".$info_demandeur['nom'];
			$description_rapide = "Vous avez une nouvelle demande d'amis sur Comunic";
			$nom_destinataire = $info_destinataire_demande['prenom']." ".$info_destinataire_demande['nom'];
			$adresse_mail_destinataire = $info_destinataire_demande['mail'];
			$texte_message = "
			<h3 class='titre'>Nouvelle demande d'amis</h3>
			<p>Une nouvelle demande d'amis a &eacute;t&eacute; post&eacute;e sur votre compte.</p>
			<p>Voici la personne qui vous a demand&eacute; en amis:</p>
			<a href='".$urlsite."?id=".$iddemandeur."'>
				<table align='center'>
					<tr><td>
					".avatar($iddemandeur, $urlsite)."
					</td><td>
						".$info_demandeur['prenom']."<br />
						".$info_demandeur['nom']."<br />
					</td>
					</tr>
				</table></a>
			<p><a href='".$urlsite."'>Connectez-vous</a> pour acc&eacute;der &agrave; toute les notifications de Comunic.</a></p>
			";
			
			//Envoi du message
			include('inc/envoi_mail.php');
		}
	}
}

//Fonction permettant de vérifier si une demande à devenir ami a déjà été faite
function isset_demande_amis($idpersonne, $iddemandeur, $bdd)
{
	//On vérifie si une demande n'a pas déjà été postée
	$liste_verification = liste_amis($idpersonne, $bdd, 0);
	
	//On vérifie
	foreach($liste_verification as $verifier)
	{
		if($verifier == $iddemandeur)
		{
			return true;
		}
	}
	
	return false;
}

//Fonction de recherche de tous les textes
function affichertextes($id, $bdd, $lignedepart = 0, $limit = 10, $niveau_visibilite = 2, $tous_texte_personne = false, $post_precis = false, $liste_groupes = array(), $type = "all")
{
	//Complément de source pour les groupes
	$complement_source_groupes = "";
	foreach($liste_groupes as $ajouter)
	{
		$complement_source_groupes .= " OR (niveau_visibilite LIKE '%|".$ajouter."')";
		$complement_source_groupes .= " OR (niveau_visibilite LIKE '%|".$ajouter."|%')";
	}
	
	//Complément de source pour les types de post
	$complement_source_type = "";
	if($type != "all")
		$complement_source_type = "AND type = '".$type."' ";
	
	//Recherche des textes de la personne (en fonction de de tous ou non)
	if(isset($_SESSION['ID']))
	{
		if($post_precis != false)
		{
			$sql = "SELECT * FROM texte WHERE (ID_personne = ".$id.") ".$complement_source_type." AND (((niveau_visibilite <= ?) OR (niveau_visibilite LIKE ?)) ".$complement_source_groupes.") AND ID = ".$post_precis*1;
		}
		elseif(!$tous_texte_personne)
			$sql = "SELECT * FROM texte WHERE (ID_personne = ".$id.") ".$complement_source_type." AND ((((niveau_visibilite <= ?) OR (niveau_visibilite LIKE ?)) ".$complement_source_groupes.") OR ID_amis = ".(isset($_SESSION['ID']) ? $_SESSION['ID'] : "'null'").") ORDER BY ID DESC LIMIT ".$lignedepart.", ".$limit;
		else
			$sql = "SELECT * FROM texte WHERE (ID_personne = ".$id.") ".$complement_source_type."  AND (((niveau_visibilite <= ?) OR (niveau_visibilite LIKE ?) ".$complement_source_groupes.") OR ID_amis = ".(isset($_SESSION['ID']) ? $_SESSION['ID'] : "'null'").") ORDER BY ID DESC LIMIT ".$lignedepart.", ".$limit;
	}
	else
	{
		if($post_precis != false)
		{
			$sql = "SELECT * FROM texte WHERE ID_personne = ".$id." ".$complement_source_type." AND niveau_visibilite = 1 AND ID = ".$post_precis*1;
		}
		elseif(!$tous_texte_personne)
			$sql = "SELECT * FROM texte WHERE ID_personne = ".$id." ".$complement_source_type." AND niveau_visibilite = 1 ORDER BY ID DESC LIMIT ".$lignedepart.", ".$limit;
		else
			$sql = "SELECT * FROM texte WHERE ID_personne = ".$id." ".$complement_source_type."  AND niveau_visibilite = 1 ORDER BY ID DESC LIMIT ".$lignedepart.", ".$limit;
	}
	
	//Exécution de la requete
	$textes = $bdd->prepare($sql);
	$textes->execute(array($niveau_visibilite, $niveau_visibilite."%"));
	
	//Affichage des résultats
	$retour = array();
	while($afficherresultats = $textes->fetch())
	{
		$retour[] = $afficherresultats;
	}
	
	//On renvoi les résultats
	return $retour;
	
	//Fermeture de la requete
	$textes->closeCursor();
}

//Fonction qui permet d'aimer ou de ne plus aimer
function aimeaimeplus($actionid, $idtextelike, $idpersonne, $bdd, $type = "texte")
{
	//Recherche de la cause d'appel de cette fonction
	$sql = "SELECT * FROM aime WHERE (ID_type = ?) && (ID_personne = ?) && (type = ?)";
	$requete = $bdd->prepare($sql);
	$requete->execute(array($idtextelike, $idpersonne, $type));
	
	$tour = "0";
	
	while($executionrequete = $requete->fetch())
	{
		$tour = "1";
	}
	
	if($actionid == 2)
	{
		$action = "Je n'aime plus";
	}
	else
	{
		$action = "J'aime";
	}
	
	//Fermeture de la requete de recherche
	$requete->closeCursor();
	
	//Si la requete est d'aimer, ajouter une entrée dans la table aimer
	if($action == "J'aime")
	{
		//On vérifie avant si la personne n'aime pas déjà
		if($tour != 0)
		{
			//On affiche un message d'erreur: la personne aime déjà le texte
			echo '<script type="text/javascript">alert("Vous aimez déjà ce contenu! Il n\'est pas autorisé d\'aimer plusieurs fois un contenu.");</script>';
		}
		else
		{
			//Requete d'insertion
			$sql = "INSERT INTO aime (ID_type, ID_personne, Date_envoi, type) VALUES (?, ?, NOW(), ?)";
			$insertion = $bdd->prepare($sql);
			$insertion->execute(array($idtextelike, $idpersonne, $type));
		}
	}
	
	//Si la requete est de ne plus aimer, supprimer l'entrée de la table
	if ($action == "Je n'aime plus")
	{
		$sql = "DELETE FROM aime WHERE (ID_type = ?) && (ID_personne = ?) && (type = ?)";
		$suppression = $bdd->prepare($sql);
		$suppression->execute(array($idtextelike, $idpersonne, $type));
	}
}

//Fonction de requete de recherche des aime
function requeteaime($idtexte, $bdd, $type = "texte")
{
	//On compte ensuite les j'aime pour le texte
	$sqlaime = ("SELECT * FROM aime WHERE ID_type =  ? && type= ?");
	$requeteaime = $bdd->prepare($sqlaime);
	$requeteaime->execute(array($idtexte, $type));
	
	$retour['personnesaiment'] = 0;
	$retour['vousaimez'] = 0;
	
	if(isset($_SESSION['ID']))
	{
		$idpourlescom = $_SESSION['ID'];
	}
	else
	{
		$idpourlescom = 'x';
	}
	
	while($afficheraime = $requeteaime->fetch())
	{
		if($afficheraime['ID_personne'] != $idpourlescom)
		{
			$retour['personnesaiment']++;
		}
		else
		{
			$retour['vousaimez'] = 1;
			$retour['personnesaiment']++;
		}
	}
	
	//On renvoi le résultat
	return $retour;
	
	//Fermeture de la requete
	$requeteaime->closeCursor();
}

//Fonction permettant de supprimer tous les "aimes" d'un type pour un id
function delete_aimes_type_id($idcontent, $type, $bdd)
{
	//Exécution de la requête
	$sql = "DELETE FROM aime WHERE (ID_type = ?) && (type = ?)";
	$requete = $bdd->prepare($sql);
	$requete->execute(array($idcontent, $type));
}

//Fonction d'affichage du bouton j'aime pour un commentaire
function bouton_aime_commentaire($idcommentaire, $idtexte, $bdd)
{
	//Recherche du nombre de j'aime
	$nombre_aime = requeteaime($idcommentaire, $bdd, "commentaire");
	
	//Développement uniquement
	//print_r($nombre_aime);
	
	echo "&nbsp;&nbsp;<img src='".path_img_asset()."aime";
	echo ($nombre_aime['vousaimez'] != 0 ? "" : "_vide");
	echo ".png'";
	
	// Uniquement si l'utilisateur est connecté
	if(isset($_SESSION['ID']))
	{
		echo " onClick='like_comment(".$idcommentaire.", ".$idtexte.",".$nombre_aime['vousaimez'].");'";
	}
	
	
	echo " id='like_comment_".$idcommentaire."' />&nbsp;";
	
	echo $nombre_aime["personnesaiment"];
}

//Fonction de suppression de commentaires
function suppcom($id, $bdd)
{
	//On récupère les informations dans la base de données
	$infos_commentaire = select_sql("commentaires", "ID = ?", $bdd, array($id));
	
	//On supprime les mentions "j'aime" du commentaire
	delete_aimes_type_id($id, "commentaire", $bdd);
	
	//On vérifie si une image est associée au commentaire
	if(preg_match("<file:>", $infos_commentaire[0]['image_commentaire']))
	{
		$fichier = relativeUserDataFolder(str_replace('file:', '', $infos_commentaire[0]['image_commentaire']));
	
		//Suppression du fichier
		if(!preg_match('<.php>', $fichier))
			unlink($fichier);
		else
			die("Major error found ! Deletion stopped !");
		
		echo "One image deleted.";
	}
	
	//Exécution de la suppression
	$sql = "DELETE FROM commentaires WHERE ID = ".$infos_commentaire[0]['ID'];
	
	//Exécution de la requete
	$suppcommentaire = $bdd->query($sql);
	
	//On redirige vers la meme page pour éviter les collisions
	$retour = "Suppression terminée, redirection en cours....<br />";
	$retour .= '<meta http-equiv="refresh" content="0">';
	
	//On renvoie le résultat
	return $retour;
}

//Fonction d'affichage des commentaires
function affichecommentaire($idtexte, $bdd)
{
	//Affichage des commentaires
	$sql = 'SELECT * FROM commentaires WHERE ID_texte = '.$idtexte.' ORDER BY ID';
	
	//Exécution de la requete
	$commentaires = $bdd->query($sql);
	
	//Enregistrement des commentaires dans une variable
	$retour = array();
	while($affichercommentaires = $commentaires->fetch())
	{
		//On corrige la longueur des mots des commentaires
		$affichercommentaires['commentaire'] = corrige_longueur_mots_commentaires($affichercommentaires['commentaire']);
		
		$retour[] = $affichercommentaires;
	}
	
	//On renvoi le résultat
	return $retour;
	
	//On ferme la requete
	$commentaires->closeCursor();
}

//Fonction de suppression de texte
function deletetexte($idtexte, $texte, $bdd, $other_info = array())
{
	//!!! OBSOLETE -> DANGER SYSTEME !!! On vérifie si il ne s'agit pas d'une image (ancienne -> ancien système)
	/*$chaine = '/imgcomunicpostbyuser/';
	if(preg_match($chaine, $texte))
	{
		//On récupère le nom de l'image pour la supprimer...
		$nomimg = str_replace('imgcomunicpostbyuser', '', $texte);
		$nomimg = strstr($nomimg, 'endof', true);
		
		//On supprime l'image
		unlink('imgpost/'.$nomimg.'.jpg');
	}*/
	
	//On vérifie si il ne s'agit d'une image (plus récente -> nouveau système)
	//On vérifie si les informations ont été données
	if(count($other_info) != 0)
	{
		//On vérifie si il s'agit d'une image
		if($other_info['type'] == "image" || $other_info['type'] == "pdf")
		{
			//Dans ce cas on supprime l'image
			unlink(relativeUserDataFolder($other_info['path']));
		}
	}
	
	//On supprime les aimes des commentaires
	$commentaires = affichecommentaire($idtexte, $bdd);
	
	//Suppression des aimes des commentaires
	foreach($commentaires as $info_commentaire)
	{
		//On supprime les 'aimes' du commentaire
		delete_aimes_type_id($info_commentaire['ID'], "commentaire", $bdd);
	}
	
	//On supprime le texte et les commentaires associés
	$sql1 = "DELETE FROM texte WHERE ID = ".$idtexte;
	$sql2 = "DELETE FROM commentaires WHERE ID_texte = ".$idtexte;
	$sql3 = "DELETE FROM aime WHERE ID_type = ".$idtexte." AND type = 'texte'";
	
	//Exécution de la requete
	$supptxt1 = $bdd->query($sql1);
	$supptxt2 = $bdd->query($sql2);
	$supptxt3 = $bdd->query($sql3);
}

//Fonction d'ajout de commentaire
function ajoutcommentaire($idpersonne, $idtexte, $commentaire, $bdd)
{
	//Préparation du contrôle de la présence d'une image
	$image = "";
	
	if(isset($_FILES['image']))
	{
		if($_FILES['image']['error'] == 0)
		{
			ob_start();
			if($infos_image = getimagesize($_FILES['image']['tmp_name']))
			{
				ob_end_clean();
					
				if(is_array($infos_image))
				{
					//Exemple de réponse
					/* Array ( 
						[0] => 1280 
						[1] => 1024 
						[2] => 3 
						[3] => 
						width="1280" 
						height="1024" 
						[bits] => 8 
						[mime] => image/png
					) */
					
					//On ne continue que si le type d'image est correcte
					if(in_array($infos_image['mime'], array('image/png', 'image/gif', 'image/jpeg')))
					{
						//On récupère les dimensions de l'image
						$width = $infos_image[0];
						$height = $infos_image[1];
							
						//On diminue les dimensions de l'image tant qu'elles sont supérieures à la limite
						while($width > 350 OR $height > 550)
						{
							$width = $width*0.9;
							$height = $height*0.9;
						}
							
						//On simplifie les valeurs trouvées
						$width = floor($width);
						$height = floor($height);
						
						//On redimensionne l'image
						// Fichier et nouvelle taille
						$filename = $_FILES['image']['tmp_name'];
						
						// Création de la nouvelle image
						$thumb = imagecreatetruecolor($width, $height);
						
						if($infos_image['mime']=='image/jpeg') 
						{
							// Création des instances d'image (jpeg)
							$source = imagecreatefromjpeg($filename);
						}
						elseif($infos_image['mime']=='image/png') 
						{
							// Création des instances d'image (png)
							$source = imagecreatefrompng($filename);
						}
						elseif($infos_image['mime']=='image/gif') 
						{
							// Création des instances d'image (gif)
							$source = imagecreatefromgif($filename);
						}
						else
						{
							die("<p>Type d'image incompatible avec ce service.</p>");
						}
						
						// Redimensionnement
						imagecopyresized($thumb, $source, 0, 0, 0, 0, $width, $height, $infos_image[0], $infos_image[1]);
						
						//Détermination du nom de l'image
						$nom_img_commentaire = sha1(time().$_SESSION['ID'].sha1($commentaire).$_SERVER['REMOTE_ADDR']);
						
						//Checking user's folder path
						checkPersonnalFolder(relativeUserDataFolder("imgcommentaire/"), $_SESSION['ID']);

						//Check if an image with the same name already exists
						while(file_exists(relativeUserDataFolder("imgcommentaire/".$_SESSION['ID']."/".$nom_img_commentaire.".png")))
							$nom_img_commentaire = crypt($nom_img_commentaire);
						
						$nom_img_commentaire = "imgcommentaire/".$_SESSION['ID']."/".$nom_img_commentaire.".png";
						
						//Ecriture du fichier
						imagepng($thumb, relativeUserDataFolder($nom_img_commentaire));
						
						//On indique où se trouve dans le fichier dans la bdd
						$image = "file:".$nom_img_commentaire;
					}
				}
			}
			else
				ob_end_clean();
				
		}
	}
		
	//Contrôle de la validité du commentaire (nécessite verifie_validite_ajout)
	if(verifie_validite_ajout($commentaire, true) || $image != "")
	{
		//Insertion du commentaire
		$sql = "INSERT INTO commentaires (ID_personne, ID_texte, date_envoi, commentaire, image_commentaire) VALUES (".$idpersonne.", ".$idtexte.", NOW(), ?, ?)";
		$insertion = $bdd->prepare($sql);
		$insertion->execute(array(corrige_echapement($commentaire), $image));
	}
	else
	{
		//Message d'erreur
		echo "<script>alert('Commentaire invalide !');</script>";
	}
}

//Fonction de recherche des informations d'une personne
function cherchenomprenom($ID, $bdd = false, $type = 'ID', $messageerreur = "<p>Erreur! Le profil demand&eacute; n'existe pas.</p>")
{
	//On vérifie si il s'agit ou non du fil d'actualité
	if($ID != "fil" && $bdd)
	{
		//Recherche du nom de la personne
		$sql = "SELECT * FROM utilisateurs WHERE ".$type." = ?";
		$nompersonne = $bdd->prepare($sql);
		$nompersonne->execute(array($ID));
		
		//Affichage du nom de la personne
		ob_start();
		if(!$infos_user = $nompersonne->fetch())
		{
			ob_end_clean();
			echo $messageerreur;
			die();
		}
		ob_end_clean();
		
		//Fermeture de la requete
		$nompersonne->closeCursor();
	}
	elseif($ID == "fil")
	{
		//Informations factices
		/* Echantillon d'exemple ---
		
			[ID] => 1
			[nom] => Hubert
			[prenom] => Pierre
			[date_creation] => 2013-11-10 09:17:56
			[mail] => pierrot42100@yahoo.fr
			[password] => dsqfqdfqsgq5
			[affiche_chat] => 0
			[public] => 1
			[pageouverte] => 1,
			[bloquecommentaire]
		--- */
			
			$infos_user = array(
				"ID" => "fil",
				"nom" => "d'actualite",
				"prenom" => "Fil",
				"date_creation" => "0000-00-00 00:00:00",
				"mail" => "no-reply@communiquons.org",
				"password" => "",
				"public" => 1,
				"site_web" => "",
				"page_verifiee" => 0,
				"autoriser_post_amis" => 1,
				"liste_amis_publique" => 0,
				"bloquecommentaire" => 0
			);
	}
	else
	{
		?><p>Informations manquantes pour acc&eacute;der &agrave; la base de donn&eacute;es. Ce n'est pas de votre faute, cependant cette erreur implique l'arr&ecirc;t du chargement de la page. 
		Essayez d'actualiser la page pour essayer de corriger le probl&egrave;m.</p><?php
		die();
	}
		//Détermination du nom complet de la personne
		$infos_user['nom_complet'] = $infos_user['prenom']." ".$infos_user['nom'];
		return $infos_user;
	
}

//Fonction permettant de vérifier si une page est publique ou non
function detectepagepublique($idpersonne, $bdd)
{
	//On vérifie si la page est publique
	$sql = 'SELECT * FROM utilisateurs WHERE ID = ?';
	$requeteverification = $bdd->prepare($sql);
	$requeteverification->execute(array($idpersonne));
	
	//On prépare les variables pour l'étapge suivante
	$autorisationspeciale = 0;
	
	//On vérifie maintenant
	if($verifier = $requeteverification->fetch())
	{
			if(($verifier['public'] == 1) && ($verifier['pageouverte'] == 1))
			{
				//On donne alors une autorisation spéciale
				$autorisationspeciale = 1;
			}
		//Fermeture de la requete
		$requeteverification->closeCursor();
	}
	
	//On renvoi le résultat suivant si la page est publique ou non
	if($autorisationspeciale == 1)
	{
		return true;
	}
	else
	{
		return false;
	}
}

//Fonction qui permet de vérifier si une personne est un ami ou non
function detectesilapersonneestamie($personneconnecte, $secondepersonne, $bdd)
{
	//On vérifie si c'est un amis
	$sql = "SELECT * FROM amis WHERE (ID_personne = ?) && (ID_amis = ?) && (actif = 1)";
		
	//Exécution de la requete
	$requeteamis = $bdd->prepare($sql);
	$requeteamis->execute(array($personneconnecte, $secondepersonne));
	
	//On renvoi le résultat suivant si la personne est amie ou non
	if(!$verifieramis = $requeteamis->fetch())
	{
		return false;
	}
	else
	{
		return true;
	}

	//Fermeture de la requete
	$requeteamis->closeCursor();
}


/**
 * Fonction d'envoi d'image en ligne (nouvelle version)
 *
 * @param 	Int 		$idpersonne 		The ID of sender
 * @param 	String 		$nomimage 			Image description
 * @param 	Object 		$bdd 				BDD object
 * @param 	Int 		$idamis 			Optionnal - Specify the ID of a friend if it is one whom send image
 * @param 	Int 		$niveau_visibilite 	Optional - Specify if  specific visibility level has been defined
 * @return 	Boolean							True or false depending of the success of the operation
 */
function envoiimage($idpersonne, $nomimage, $bdd, $idamis = 0, $niveau_visibilite = 2)
{
	//On vérifie si il y a eu une erreur
	if($_FILES['image']['error'] == 0)
	{
		//On vérifie si un dossier a été allouée à la personne, création automatique le cas échéant
		$folder_user = checkPersonnalFolder(relativeUserDataFolder("imgpost/"), $_SESSION['ID']);
		$folder_user = str_replace(relativeUserDataFolder(), "", $folder_user);

		//On recherche le nom de l'image
		$imagePath = $folder_user.(isset($_SERVER['UNIQUE_ID']) ? $_SERVER['UNIQUE_ID'] : sha1($_SERVER['HTTP_USER_AGENT']))
		.$_SESSION['ID'].$_SERVER['REQUEST_TIME'].".jpg"; //Nom introuvable
		
		// On peut valider l'image de fond et la stocker définitivement
		if(move_uploaded_file($_FILES['image']['tmp_name'], relativeUserDataFolder($imagePath)))
		{
		
			//Préparation de la requete
			$contenu = $nomimage;
			
			//On enregistre l'image dans la base
			$sql = 'INSERT INTO texte (ID_personne, date_envoi, texte, ID_amis, niveau_visibilite, type, size, file_type, path) VALUES ('.$idpersonne.', NOW(), ?, ?, ?, ?, ?, ?, ?) ';
				
			//Exécution de la requete
			$insertion = $bdd->prepare($sql);
			$insertion->execute(array($contenu, $idamis, $niveau_visibilite, "image", $_FILES['image']['size'], $_FILES['image']['type'], $imagePath));
			
			return true;
		}
		else
			echo "<p>Une erreur a survenue durant l'envoi de l'image, veuillez r&eacute;essayer...</p>";
	}
	else
	{
		echo "<p>Une erreur a survenue durant l'envoi de l'image.</p>";
	}
}

//Fonction de vérification de l'existence d'une demande de nouveaux amis
function issetdemandesamis($idpersonne, $bdd)
{
	//On vérifie si il y a des demandes d'amis non acceptés
	$sql = "SELECT * FROM amis WHERE (ID_personne = ".$idpersonne.") && (actif = 0)";
	
	//Exécution de la requete
	$verificationnouveauxamis = $bdd->query($sql);
	
	//Calcul du résultat
	if($test = $verificationnouveauxamis->fetch())
	{
		return true;
	}
	else
	{
		return false;
	}
	
	//Fermeture de la requete
	$verificationnouveauxamis->closeCursor();
}

//Fonction de correction des caractères spéciaux
function corrige_caracteres_speciaux($source)
{
	//On corrige les caractères spéciaux
	$source = str_replace("è", "&egrave;", $source);
	$source = str_replace("é", "&eacute;", $source);
	$source = str_replace("ê", "&ecirc;", $source);
	$source = str_replace("ë", "&euml;", $source);
	$source = str_replace("î", "&icirc;", $source);
	$source = str_replace("à", "&agrave;", $source);
	$source = str_replace('ç', '&ccedil;', $source);
	$source = str_replace('ù', '&ugrave;', $source);
	$source = str_replace('â', '&acirc;', $source);
	$source = str_replace('ô', '&ocirc;', $source);
	$source = str_replace('û', '&ucirc;', $source);
	$source = str_replace('’', '&rsquo;', $source);
	
	//On renvoi le résultat
	return $source;
}

//Fonction de décorrection des caractères spéciaux
function decorrige_caracteres_speciaux($source)
{
	//On corrige les caractères spéciaux
	$source = str_replace("&egrave;", "è",$source);
	$source = str_replace("&eacute;", "e", $source);
	$source = str_replace("&ecirc;", "ê", $source);
	$source = str_replace("&euml;", "ë", $source);
	$source = str_replace("&icirc;", "î", $source);
	$source = str_replace("&agrave;", "à", $source);
	$source = str_replace('&ccedil;', 'ç', $source);
	$source = str_replace('&ugrave;', 'ù', $source);
	$source = str_replace('&acirc;', 'â', $source);
	$source = str_replace('&ocirc;', 'ô', $source);
	$source = str_replace('&ucirc;', 'û', $source);
	$source = str_replace('&rsquo;', '’', $source);
	
	//On renvoi le résultat
	return $source;
}

//Fonction d'enregistrement de post de chat
function postchat($idpersonne, $message, $bdd)
{
	//On enregistre le chat
	$query = $bdd->prepare("INSERT INTO chat(message, ID_personne, Date_envoi) VALUES(?, ?, NOW())");
	$query->execute(array($message, $idpersonne));
}

//Fonction de récupération du contenu du chat
function recuperecontenuchat($bdd, $nbpost = 10)
{
	$sql = "SELECT * FROM chat ORDER BY ID DESC LIMIT 0,".$nbpost;
	$requetechat = $bdd->query($sql);
	
	//Enregistrement des résultats
	$retour = array();
	while($afficherchat = $requetechat->fetch())
	{
		$retour[] = $afficherchat;
	}
	
	//Renvoi du résultat
	return $retour;
}

//Fonction permettant de vérifier si le chat doit être automatiquement ouvert
function verifierouvertureautomatiquechat($idpersonne, $bdd)
{
	//On vérifie si le chat doit être automatiquement ouvert
	$sql = "SELECT * FROM utilisateurs WHERE ID = ".$idpersonne;
	
	//Exécution de la requete
	$verifierchat = $bdd->query($sql);
	
	//On vérifie et on affiche la source javascript en fonction des parametres
	$verfier = $verifierchat->fetch();
	
	if($verfier['affiche_chat'] == 1)
	{
		return true;
	}
	else
	{
		return false;
	}
	
	//Fermeture de la requete
	$verifierchat->closeCursor();
}

//Fonction permettant de connecter l'utilisateur
function connnecteruser($mail, $motdepasse, $bdd, $cryptage_necessaire = true, $active_login = true, $return_infos_user = false)
{
	if($cryptage_necessaire)
	{
		//Hachage du mot de passe
		$motdepasse = sha1($motdepasse);
		$motdepasse = crypt($motdepasse, $motdepasse);
	}
	
	//Préparation de la requete
	$sql = "SELECT * FROM utilisateurs WHERE (mail = ?) && (password = ?) ";
	
	//Execution de la requete
	$requete = $bdd->prepare($sql);
	$requete->execute(array($mail, $motdepasse));
	
	if($traiter = $requete->fetch())
	{
		//On connecte alors l'utilisateur
		if($active_login)
			//Ouverture de la session
			$_SESSION['ID'] = $traiter['ID'];
		
		//On vérifie si il faut retourner les informations de l'utilisateur
		if(!$return_infos_user)
			return true;
		else
			return $traiter;
	}
	else
	{
		return false;
	}
	
	//Fermeture de la requete
	$requete->closeCursor();
}

//Fonction permettant de voir si l'utilisateur est connecté
function verifieconnnexionutilisateur()
{
	//On cache les erreurs
	ob_start();
	//session_start();
	ob_end_clean();
	
	//Si l'utilisateur est connecté, on renvoi true sinon on renvoi false
	if(isset($_SESSION['ID']))
	{
		return true;
	}
	else
	{
		return false;
	}
}

//Fonction qui adapte les textes pour afficher les images
function adaptetexteimage($texte, $basesite = './')
{
	$texte = str_replace('endofimgcomunicpostbyuser', '.jpg" />', $texte);
	$texte = str_replace('imgcomunicpostbyuser', '<img width="100" height="100" src="'.$basesite.'/user_data/imgpost/', $texte); 
	$texte = str_replace('endofnameofimg', '</center>', $texte);
	$texte = str_replace('nameofimg', '<center>', $texte);
	
	//On retourne le réslutat
	return $texte;
}

//Fonction de recherche de personnes
function searchuser($nom, $bdd, $limite = 10)
{
	//Mettons "" à la place de "%"
	$nom = str_replace("%", "", $nom);
	
	//Continuons de protéger les personnes et la table
	$nom = str_replace("'", '"', $nom);
	
	//Rendons flexible la requete
	$nom = str_replace(' ', '%', $nom);
	$nom = "%".$nom."%";
	
	//Nous pouvons maintenant faire la recherche
	//Requête SQL
	$sql = "SELECT * FROM utilisateurs WHERE (nom LIKE ?) || (prenom LIKE ?) || (CONCAT(prenom, '%', nom) LIKE ? ) || (CONCAT(prenom, ' ', nom) LIKE ? ) || (mail LIKE ?) ORDER BY prenom LIMIT ".$limite;
	
	//Exécution de la requete SQL
	$recherche = $bdd->prepare($sql);
	$recherche->execute(array($nom, $nom, $nom, $nom, $nom));
	
	//Enregistrement des résultats
	$retour = array();
	while($afficherrecherche = $recherche->fetch())
	{
		$retour[] = $afficherrecherche;
	}
	
	//Fermeture de la requete
	$recherche->closeCursor();
	
	//On retourne le résultat
	return $retour;
}

//Fonction qui permet de rechercher les messages d'un utilisateur
function recherchermessageutilisateur($idpersonne, $bdd, $id = '?')
{
	if($id == '?')
	{
		//On recherche les messages de l'utilisateur
		//Et on les renvoies dans un tableau
		$sql = 'SELECT * FROM messagerie WHERE ID_destinataire = '.$idpersonne;
		$requete = $bdd->query($sql);
	}
	else
	{
		//Si on recherche un message précis, on adapte la requête
		$sql = 'SELECT * FROM messagerie WHERE ID_destinataire = ? && ID = ?';
		$requete = $bdd->prepare($sql);
		$requete->execute(array($idpersonne, $id));
	}
	
	//On prépare l'enregistrement des résultats
	$retour = array('lu'=>array(), 'nonlu'=>array());
	
	//On enregistre les résultats
	while($enregistrer = $requete->fetch())
	{
		if($enregistrer['lu'] == 0)
		{
			$retour['nonlu'][] = $enregistrer;
		}
		else
		{
			$retour['lu'][] = $enregistrer;
		}
	}
	
	//On retourne les résultats
	return $retour;
	
	//On ferme la requete
	$requete->closeCursor();
}

//Fonction de récupération de la liste des pages publiques
function get_page_publique($bdd){
	//On récupère la liste de toute les pages publiques
	$sql = "SELECT * FROM utilisateurs WHERE pageouverte = 1 ORDER BY prenom";
	
	//Exécution de la requete
	$requete = $bdd->query($sql);
	
	//On enregistre les résultats
	$liste = array();
	
	while($enregistre = $requete->fetch())
	{
		$liste[] = $enregistre;
	}
	
	//On renvoi le résultat
	return($liste);
	
	//On ferme la requête
	$requete->closeCursor();
}

//Fonction permettant l'affichage des smiley dans les textes:
function affiche_smile($source, $urlsite = "./", $liste_smiley = false)
{
	//Inclusion de la liste (si nécessaire
	if(!$liste_smiley)
		include('inc/liste_smile.php');
		
	//Traitement de la liste
	foreach($liste_smiley as $afficher)
	{
		//On affiche les smiley
		$source = str_ireplace($afficher[0], ' <img src="'.$urlsite.$afficher[1].'" title="'.$afficher[2].'" /> ', $source);
	}
	
	
	//On renvoi le résultat
	return($source);
}

//Fonction de protection contre le code source
function bloquebalise($source, $type="tout")
{
	//On protège la source
	$source = str_replace('<', '&lt;', $source);
	$source = str_replace('>', '&gt;', $source);
	
	//Fonction sépciale réservée aux commentaires
	if($type == "commentaire")
	{
		//Anulation de la correction pour <span class='corrige_longueur_mots'></span> par <span></span>
		$source = str_replace("&lt;span class='corrige_longueur_mots'&gt;&lt;/span&gt;", "<span></span>", $source);
	}
	
	//On renvoi le résultat
	return($source);
}

//Fonction qui définit et inclus le bon fichier de langue
function detecteinstallelangue()
{
	/*if(isset($_COOKIE['langue']))
	{
		if(file_exists('lang/'.$_COOKIE['langue'].'.php'))
		{
			//Inclusion du fichier de langue
			include('lang/'.$_COOKIE['langue'].'.php');
		}
		else
		{
			//Sinon on inclus la langue française
			include('lang/fr.php');
		}
	}
	else
	{
		//On détecte la langue
		if(isset($_SERVER['HTTP_ACCEPT_LANGUAGE']))
		{
			if(preg_match('/fr/', $_SERVER['HTTP_ACCEPT_LANGUAGE']))
			{
				//On définit le cookie pour la France
				setcookie('langue', 'fr', time() + 365*24*3600);
				
				//Et on charge la lague francaise
				include('lang/fr.php');
			}
			else
			{
				//Langue par défaut: Anglais
				setcookie('langue', 'en');
				
				//Et on charge la langue anglaise
				include('lang/en.php');
			}
		}
		else
		{
			//Langue par défaut: Anglais
			setcookie('langue', 'en');
			
			//Et on charge la langue anglaise
			include('lang/en.php');
		}
	}*/
	include(websiteRelativePath('inc/lang/fr.php'));
	//Renvoi des textes de la langue
	return $lang;
}

//Fonction qui permet de choisir sa langue
function choisirlangue($langue = 'fr') 
{
	//On vérifie que la langue est correcte
	if(str_replace('.', '', $langue) != $langue)
		die("Erreur"); //Sécurité
	
	//On définit la langue de l'utilisateur
	setcookie('langue', $langue, time()+60*60*24*30);
}

//Fonction qui permet l'échappement des guillemets ' en HTML
function echap_guillemet_html($source)
{
	//On échappe les guillemetes
	$source = str_replace("'", "&prime;", $source);
	
	//On renvoi le résultat
	return($source);
}

//Fonction de bloquage du javascript et du CSS
function bloque_javascript_css($source)
{
	//On bloque le javascript
	$source = str_ireplace("<script", "<!--<script", $source);
	$source = str_ireplace("/script>", "/script>-->", $source);
	$source = str_ireplace("/ script>", "/script>-->", $source);
	
	//On bloque le CSS
	$source = str_ireplace("<style", "<!--<style", $source);
	$source = str_ireplace("/style>", "/style>-->", $source);
	$source = str_ireplace("/ style>", "/style>-->", $source);
	
	//Blockage au sein des balises
	$source = str_ireplace("onClick=", "unknow=", $source);
	$source = str_ireplace("onLoad=", "unknow=", $source);
	$source = str_ireplace("onBlur=", "unknow=", $source);
	$source = str_ireplace("onMouseOver=", "unknow=", $source);
	$source = str_ireplace("onMouseOut=", "unknow2=", $source);
	//$source = str_ireplace("style=", "unknow=", $source);
	
	//Adaption pour TinyMCE
	$source = str_ireplace('unknow="this.src', 'onMouseOver="this.src', $source); 
	$source = str_ireplace('unknow2="this.src', 'onMouseOut="this.src', $source); 
	
	//On bloque les balises meta
	$source = str_ireplace("<meta", "&lt;meta", $source);
	
	//On renvoi le résultat
	return($source);
}

//Fonction de protection de la création de compte
function trouve_caractere_tableau($tableau_a_verifier, $caractere = '<')
{	
	//On annonce que l'on a pour le moment il n'y a pas de problème de sécurité
	$probleme_securite = false;
	
	//On analyse les variables
	foreach ($tableau_a_verifier as $tester)
	{
		if(preg_match('/'.$caractere.'/', $tester))
		{
			//On enregistre qu'il y a un problème de sécurités
			$probleme_securite = true;
		}
	}
	
	//On renvoi le résultat
	return $probleme_securite;
}

//Fonction qui permet de lister l'ensemble des amis d'une personne
// $id: identifiant de la personne
//$bdd: Connexion à la base de donnée (PDO)
//$actif: indique si l'ami doit être actif ou non
function liste_amis($id, $bdd, $actif = 1)
{
	//On prépare le tableau final
	$listeamis = array();
	
	//Requete de recherche d'amis
	$sql = "SELECT * FROM amis WHERE ID_personne = ".$id." && actif = ".$actif;
		
	//Exécution de la requete
	$requeteamis = $bdd->query($sql);
	
	//Enregistrement des résultats
	while($enregistrer = $requeteamis->fetch())
	{
		//On enregistre l'amis
		$listeamis[] = $enregistrer['ID_amis'];
	}
	
	//Fermeture de la base de données
	$requeteamis->closeCursor();
	
	//On renvoi le résultat
	return $listeamis;
}

//Fonction d'adaptation des textes pour les images
function adapte_texte_image($source, $pathimage = "./") 
{
	$source = str_replace('endofimgcomunicpostbyuser', '.jpg" />', $source);
	$source = str_replace('imgcomunicpostbyuser', '<img height="200" src="user_data/imgpost/', $source); 
	$source = str_replace('endofnameofimg', '</center>', $source);
	$source = str_replace('nameofimg', '<center>', $source);
	
	//On renvoi le résultat
	return $source;
}

//Fonction de correction des échappements
function corrige_echapement($source)
{
	//On corrige les échappements
	$source = str_replace("\'", "'", $source);
	$source = str_replace('\"', '"', $source);

	//On renvoi le résultat
	return($source);
}

//Private Chat
//This function should be used only for the private chat
//Info: 
	//Parametres :
		//$id : ID of the connected personn
		//$idother : ID of the other personn
		//$bdd : Connexion PDO to the DB
		//$nbposts (optional, default 10) : Number of posts
	//This function need these functions :
		//bloquebalise : The HTML isn't allowed in the private chat...
		//affiche_smile: but the users can decide to show there emotions!
//Infos of get_content_private_chat ended
function get_content_private_chat ($id, $idother, $bdd, $nbposts = 10, $id_last_post = "*", $urlsite = "./")
{
	//On vérifie si il s'agit d'un rafraîchissement ou non
	if($id_last_post == "*")
	{
		//First, SQL
		$sql = "SELECT * FROM chatprive WHERE (ID_personne = ? && ID_destination = ?) || (ID_personne = ? && ID_destination = ?) ORDER BY ID DESC LIMIT 0,".$nbposts;
		
		//Send request
		$get_content = $bdd->prepare($sql);
		$get_content->execute(array($id, $idother, $idother, $id));
	}
	else
	{
		//First, SQL
		$sql = "SELECT * FROM chatprive WHERE ((ID_personne = ? && ID_destination = ?) || (ID_personne = ? && ID_destination = ?)) AND ID > ? ORDER BY ID DESC ";
		
		//Send request
		$get_content = $bdd->prepare($sql);
		$get_content->execute(array($id, $idother, $idother, $id, $id_last_post));
	}
	
	//Save results in $content_chat
	$content_chat = array();
	while($save = $get_content -> fetch())
	{
		//We stop HTML code
		$save['contenu'] = affiche_smile(bloquebalise(wordwrap(str_replace(')', ') ', $save['contenu']), 30, " ", true)), $urlsite);
		
		//We save the entry
		$content_chat[] = $save;
	}
	
	//Send result
	return $content_chat;
	
	//Close request
	$get_content->closeCursor();
}

//Save a post for the private chat
function save_private_chat_message($id, $idother, $bdd, $message = "A basic message")
{
	$query = $bdd->prepare("INSERT INTO chatprive(ID_personne, ID_destination, date_envoi, contenu) VALUES(?, ?, NOW(), ?)");
	$query->execute(array($id, $idother, $message));
}

//List all the personns which have already  send chat to the connected personn
function list_all_person_private_chatted($id, $bdd)
{
	//SQL -> create request
	$sql = "SELECT * FROM chatprive WHERE (ID_destination = ?)";
	
	//SQL -> send request
	$request = $bdd->prepare($sql);
	$request->execute(array($id));
	
	//Saving results in $results
	$results = array();
	while($save = $request->fetch())
	{
		//Check if result isn't already saved
		if(!isset($results[$save['ID_personne']]))
		{
			//Save result
			$results[$save['ID_personne']] = "true";
		}
	}
	
	//Send results
	return $results;
	
	//SQL -> close request
	$request->closeCursor();
	
}

//We send a notification to all the friends of the user
function sendnotification($id, $message, $bdd, $adresse="", $idamis = "", $type = "", $list_friend = false)
{
	//On vérifie si la liste est déjà fournie
	if(!$list_friend)
		//We search the friend of the personn
		$list_friend = liste_amis($id, $bdd);
	
	//We insert in notification the message
	foreach($list_friend as $insert_friend)
	{
		//On vérifie déjà que les deux personnes sont amies
		$ok = true;
		if($idamis != "")
		{
			if(!detectesilapersonneestamie($idamis, $insert_friend, $bdd))
			{
				//On arrête l'envoi 
				$ok = false;
			}
			
			if($idamis == $insert_friend)
			{					
				//On autorise l'envoi
				$ok = true;
			}
		}
		
		if($ok)
		{
			//On vérifie que la personne est abonnée aux notifications de l'utilisateur
			if(personne_abonnee_notification_ami($insert_friend, $id, $bdd))
			{
				//On vérifie si une notification similaire existe déjà
				$sql = "SELECT COUNT(*) AS nb_notifications FROM notification WHERE ID_personne = ? AND adresse = ? AND type = ? AND vu = 0";
				$requete = $bdd->prepare($sql);
				$requete->execute(array($insert_friend, $adresse, $type));
				
				//Récupération des informations
				if($infos = $requete->fetch())
				{
					if($infos['nb_notifications'] == 0)
					{
						//We INSERT now
						$sql = "INSERT INTO notification (ID_personne, ID_createur, date_envoi, message, adresse, type) VALUES (?, ?, NOW(), ?, ?, ?)";
						
						//Send request
						$insert = $bdd->prepare($sql);
						$insert->execute(array($insert_friend, $id, $message, $adresse, $type));
					}
				}
				else
				{
					echo "<!-- Error -->";
				}
				
				//Fermeture de la requête
				$requete->closeCursor();
			}
		}
	}
}

//Similaire mais pour un seul utilisateur
function sendnotification_one_user($id, $iddestination, $message, $bdd, $adresse="")
{
	//Prépartion de l'exécution de la fonction
	$retour = false;
	
	//On vérifie si une notification similaire existe déjà
	$sql = "SELECT COUNT(*) AS nb_notifications FROM notification WHERE ID_personne = ? AND ID_createur = ? AND message = ? AND adresse = ? AND vu = 0";
	$requete = $bdd->prepare($sql);
	$requete->execute(array($iddestination, $id, $message, $adresse));
	
	//Récupération des informations
	if($infos = $requete->fetch())
	{
		if($infos['nb_notifications'] == 0)
		{
			//We INSERT now
			$sql = "INSERT INTO notification (ID_personne, ID_createur, date_envoi, message, adresse) VALUES (?, ?, NOW(), ?, ?)";
			
			//Send request
			$insert = $bdd->prepare($sql);
			$insert->execute(array($iddestination, $id, $message, $adresse));
			
			//On confirme l'envoi
			$retour = true;
		}
	}
	
	//Fermeture de la requête
	$requete->closeCursor();
	
	//Renvoi du résultat
	return $retour;
}

//We search the notifications of the actual user
function searchnotification($id, $bdd, $limit = 10, $lignedepart = 0, $vu="none", $order = "DESC", $update_vu = 0)
{
	//SQL -> creating request
	if($vu == "none")
	$sql = "SELECT * FROM notification WHERE ID_personne = ? ORDER BY ID ".$order." LIMIT ".$lignedepart.", ".$limit;
	else
	$sql = "SELECT * FROM notification WHERE (ID_personne = ? AND vu = ".$vu.") ORDER BY ID ".$order." LIMIT ".$lignedepart.", ".$limit;
	
	//SQL -> sending request
	$request = $bdd->prepare($sql);
	$request->execute(array($id));
	
	//Saving results
	$results = array();
	while($save = $request->fetch())
	{
		//Save result
		$results[] = $save;
		
		if($update_vu == 1)
		{
			//On met la notification en vu
			$sql = "UPDATE notification SET vu = 1 WHERE ID = ".$save['ID'];
			
			//Exécution de la requête
			$update = $bdd->query($sql);
			
		}
	}
	
	//Sending results
	return $results;
	
	//SQL -> close request
	$request -> closeCursor();
}

//Function for send message
function sendmessage($id_expediteur, $id_destinataire, $sujet, $message, $bdd)
{
	//On enregistre le message
	$sql = "INSERT INTO messagerie (ID_expediteur, ID_destinataire, objet, message, date_envoi) VALUEs (?, ?, ?, ?, NOW())";
	
	//Exécution de la requête
	$insertion = $bdd->prepare($sql);
	
	if($insertion->execute(array($id_expediteur, $id_destinataire, $sujet, $message)))
	{
		return true;
	}
	else
	{
		return false;
	}
}

//Fonction de recherche d'informations sur un sujet
function info_sujet($id, $bdd)
{
	//Requete de recherche des informations sur le sujet
	$sql = 'SELECT * FROM forum_sujet WHERE ID = ?';
	
	//Exécution de la requete
	$requete = $bdd->prepare($sql);
	$requete->execute(array($id));
	
	//Enregistrement des résultats
	$info = $requete->fetch();
	
	//Fermeture de la requete
	$requete->closeCursor();
	
	//On renvoi le résultat
	return $info;
}

//Fonction de recherche d'informations sur les réponses d'un sujet
function info_reponses($id, $bdd)
{
	//Requete de recherche des informations sur le sujet
	$sql = 'SELECT * FROM forum_reponse WHERE ID_sujet = ?';
	
	//Exécution de la requete
	$requete = $bdd->prepare($sql);
	$requete->execute(array($id));
	
	//Enregistrement des résultats
	$info = array();
	while($enregistrer = $requete->fetch())
	{
		$info[] = $enregistrer;
	}
	
	//Fermeture de la requete
	$requete->closeCursor();
	
	//On renvoi le résultat
	return $info;
}

//Défini un message en vu
function metslemessageenvu($idmessage, $bdd)
{
	//On défini un message en tant que vu
	$sql = "UPDATE messagerie SET lu = 1 WHERE ID = ".$idmessage;
	
	//Exécution de la requête
	$update = $bdd->query($sql);
}

//Suppression d'un message depuis son ID
function suppmessagefromid($id_personne, $id_message, $bdd)
{
	//Requête de suppression d'un message
	$sql = "DELETE FROM messagerie WHERE ID = ? AND ID_destinataire = ?";
	
	//Exécution de la requête
	$requete = $bdd->prepare($sql);
	$requete->execute(array($id_message, $id_personne));
}

//Enregistrement de l'activité de l'utilisateur
function update_last_activity($id, $bdd, $time = "undefined")
{
	//On enregistre time() si nécessaire
	if($time == "undefined")
	{
		$time = time();
	}
	
	//On met à jour la dernière activité de l'utilisateur
	$sql = "UPDATE utilisateurs SET last_activity = ? WHERE ID = ?";
	
	//Exécution de la requête
	$requete = $bdd->prepare($sql);
	$requete->execute(array($time, $id));
}

//Fonction qui permet de retirer la corrections des caractères spéciaux
function retire_adapte_caracters_speciaux($source)
{
	//On retire les adaptations
	$source = str_replace("&egrave;", "e", $source);
	$source = str_replace("&eacute;", "e", $source);
	$source = str_replace("&ecirc;", "e", $source);
	$source = str_replace("&euml;", "e", $source);
	$source = str_replace("&icirc;", "i", $source);
	$source = str_replace("&agrave;", "a", $source);
	$source = str_replace('&ccedil;', 'c', $source);
	$source = str_replace('&ugrave; ', 'u', $source);
	$source = str_replace('&ocirc; ', 'o', $source);
	$source = str_replace('&acirc; ', 'a', $source);
	$source = str_replace('&ucirc; ', 'u', $source);
	
	//On renvoi le réslutat
	return($source);
}

//Fonction de correction des erreurs d'accents dûes aux scripts javascript
function corrige_accent_javascript($source)
{
	//On corrige les erreures javascript
	$source = str_replace("Ã©", "é", $source);
	$source = str_replace("Ã¨", "è", $source);
	$source = str_replace("Ãª", "ê", $source);
	$source = str_replace("à§", "ç", $source);
	$source = str_replace("Ã", "à", $source);
	$source = str_replace("Ã¹", "ù", $source);
	$source = str_replace("à´", "ô", $source);
	$source = str_replace("â€™", "’", $source);
	$source = str_replace("à§", "ç", $source);
	$source = str_replace("à®", "î", $source);
	//$source = str_replace("à¢", "â", $source);
	$source = str_replace("â‚¬", "€", $source);
	
	//On renvoi le résultat
	return($source);
}

//Fonction de retirement de correction des erreurs d'accents dûes aux scripts javascript
function decorrige_accent_javascript($source)
{
	//On retire les corrections javascript
	$source = str_replace("é", "Ã©", $source);
	$source = str_replace("è", "Ã¨", $source);
	$source = str_replace("ê", "Ãª", $source);
	$source = str_replace("ç", "à§", $source);
	$source = str_replace("à", "Ã", $source);
	$source = str_replace("ù", "Ã¹", $source);
	//$source = str_replace("ô", "à´", $source);
	//$source = str_replace("î", "à®", $source);
	//$source = str_replace("â", "à¢", $source);
	$source = str_replace("€", "â‚¬", $source);
	
	//On renvoi le résultat
	return($source);
}

//Fonction déterminant si une notification existe
function notification_exists($id_createur, $id_destination, $message, $adresse, $bdd)
{
	//Prépartion de l'exécution de la fonction
	$retour = false;
	
	//On vérifie si une notification similaire existe déjà
	$sql = "SELECT COUNT(*) AS nb_notifications FROM notification WHERE ID_personne = ? AND ID_createur = ? AND message = ? AND adresse = ? AND vu = 0";
	$requete = $bdd->prepare($sql);
	$requete->execute(array($id_destination, $id_createur, $message, $adresse));
	
	//Récupération des informations
	if($infos = $requete->fetch())
	{
		if($infos['nb_notifications'] == 0)
			//Il n'y en a pas
			$retour = false;
		else
			//Il y en a
			$retour = true;
	}
	else
		//On considère qu'il n'y en a pas
		$retour = false;
	
	//Fermeture de la requête
	$requete->closeCursor();
	
	//Renvoi du résultat
	return $retour;
}

//Fonction mettant en 'vu' le chat d'une personne
function mettre_en_vu_private_chat($id, $id_emetteur, $bdd)
{
	//Mise à jour de la base de données
	$sql = "UPDATE chatprive SET vu = 1 WHERE ID_destination = ? AND ID_personne = ?";
	
	//Exécution de la requête
	$requete = $bdd->prepare($sql);
	$requete->execute(array($id, $id_emetteur));
	
	//On supprime les notifications relatives au chat privé pour cette personne
	$sql = "DELETE FROM notification WHERE ID_personne = ? AND ID_createur = ? AND message = ?";
	$suppression = $bdd->prepare($sql);
	$suppression->execute(array($_SESSION['ID'], $id_emetteur, "vous a envoy&eacute; un message dans le chat priv&eacute;."));
}

//Fonction permettant de vérifier si un post de chat privé d'une personne n'a pas été vu
function verifier_si_post_chat_prive_non_vu($id, $id_emetteur, $bdd)
{
	//First, SQL
	$sql = "SELECT * FROM chatprive WHERE ID_personne = ? && ID_destination = ? && vu = 0 LIMIT 0, 1";
	
	//Send request
	$get_content = $bdd->prepare($sql);
	$get_content->execute(array($id_emetteur, $id));
	
	if($afficher = $get_content->fetch())
	{
		return true;
	}
	else
	{
		return false;
	}
	
	//Fermeture de la requête
	$get_content->closeCursor();
}

//Fonction de vérification de l'existence de nouveaux messages pour l'utilisateur
function verifier_nouveaux_messages_utilisateur($id, $bdd)
{
	//First, SQL
	$sql = "SELECT * FROM messagerie WHERE ID_destinataire = ? && lu = 0 LIMIT 0, 1";
	
	//Send request
	$check_content = $bdd->prepare($sql);
	$check_content->execute(array($id));
	
	if($afficher = $check_content->fetch())
	{
		return true;
	}
	else
	{
		return false;
	}
	
	//Fermeture de la requête
	$check_content->closeCursor();
}

//Fonction permettant de récupérer un extrait de source ayant un commencement et une fin
function getsourceprecise($source, $debut, $fin = '\n')
{
	preg_match_all('#'.$debut.'(.*?)'.$fin.'#is', $source, $resultat, PREG_PATTERN_ORDER);
	
	//On renvoi le résultat
	return $resultat[0];
}

//Fonction d'affichage de lien
function afficher_lien($source)
{
	//On prépare l'ajout des liens
	$source = " ".$source." ";
	$source = str_replace("<", " <", $source);
	$listetype = array("http://", "ftp://", "https://");
	
	foreach($listetype as $debut)
	{
		//On récupère la liste des liens
		$liste = getsourceprecise($source, " ".$debut, " ");
		
		//On modifie les liens
		foreach($liste as $modifier)
		{
			//On modifie les liens avec un str_replace
			$source = str_replace($modifier, "<a href='".$modifier."' title='Ouvrir la page' target='_blank'>".$modifier."</a>", $source);
		}
	}
	
	//On renvoi le résultat
	return $source;
}

//Fonction d'affichage de la source nécessaire pour ajouter des smiles dans les commentaires
function source_ajout_smiley($id_texte)
{
	echo "<img src='".path_img_asset('smiley/smile_gris.gif')."' class='bouton_ajout_smiley' onClick='affiche_liste_smile(\"addcommentaire".$id_texte."\");' />";
}

//Fonction permettant de définir si une personne est connectée ou non
function determine_si_personne_connecte($id, $bdd)
{
	//Récupération des informations de la personne
	$informations = cherchenomprenom($id, $bdd);
	
	//Récupération du temps actuel
	$time = time();
	$time = $time-35;
	
	//Renvoi du résultat
	return ($time > $informations['last_activity'] ? false : true);
}

//Fonction de correction de la longueur mots des commentaires
function corrige_longueur_mots_commentaires($source)
{
	//Séparation de tous les mots
	//$liste_mots = getsourceprecise($source, " ", " "); //Non fiable
	
	//Insertion de retours à la ligne automatiques
	$source = wordwrap($source, 100, " ", true);

	//Renvoi du résultat
	return($source);
}

//Fonction de récupération de la liste de toute les pages
function get_liste_pages($id_personne, $bdd)
{
	//Requête de recherche
	$sql = "SELECT * FROM pages WHERE ID_personne = ?";
	
	//Exécution de la requête
	$requete = $bdd->prepare($sql);
	$requete->execute(array($id_personne));
	
	//Préparation de l'enregistrement
	$enregistrement = array();
	
	//Enregistrement des résultats
	while($enregistrer = $requete->fetch())
	{
		//Enregistrement de la page
		$enregistrement[] = $enregistrer;
	}
	
	//Fermeture de la requete
	$requete->closeCursor();
	
	//Renvoi du résultat
	return $enregistrement;
}

//Fonction permettant de vérifier la validité d'un post ou d'un texte
function verifie_validite_ajout($source, $exception = false)
{
	//On enlève les balises de paragraphe
	$source = str_ireplace('<p>', "", $source);
	$source = str_ireplace('</p>', "", $source);
	
	//On enlève les balises de mise en page
	$source = str_ireplace('<i>', "", $source);
	$source = str_ireplace('</i>', "", $source);
	$source = str_ireplace('<strong>', "", $source);
	$source = str_ireplace('</strong>', "", $source);
	$source = str_ireplace('<b>', "", $source);
	$source = str_ireplace('</b>', "", $source);
	$source = str_ireplace('<s>', "", $source);
	$source = str_ireplace('</s>', "", $source);
	$source = str_ireplace('</a>', "", $source);
	
	//On compte le nombre de caractères différents
	$tableau_caracteres = count_chars($source, 3);
	
	//Vérification du nombre
	if(strlen($tableau_caracteres) > 2)
	{
		//Le post est valide
		$valide = true;
	}
	else
	{
		//Le post est invalide
		$valide = false;
	}
	
	//Il y a deux exceptions (si autorisées)
	if($exception)
	{
		$valide = ($source == ":)" || $source == ":(" ? true : $valide);
	}
	
	
	//Retour du résultat
	return $valide;
}

//Fonction affichant le formulaire permettant choisir quel est le niveau de visibilité du contenu
function choisir_niveau_visibilite($niveau = 1)
{
	//Il y a trois niveaux :
	// 1 - Tout le monde (disponible uniquement si la page est ouverte)
	// 2 - Uniquement les amis
	// 3 - Uniquement la personne ayant posté le texte (et si il s'agit d'une page amie, le propriétaire de la page également)
	
	?><div class='choix_niveau_visibilite'>
		<ul>
			<li>
				<label>
					<input type='radio' name='niveau_visibilite' value='3' />
					<?php echo code_inc_img(path_img_asset('user.png')); ?>
					<span class="label">Moi</span>
				</label>
			</li>
			<li>
				<label>
					<input type='radio' name='niveau_visibilite' value='2' checked />
					<?php echo code_inc_img(path_img_asset('users_3.png')); ?>
					<span class="label">Mes amis</span>
				</label>
			</li>
			<li>
				<label>
					<input type='radio' name='niveau_visibilite' value='1' />
					<?php echo code_inc_img(path_img_asset('users_5.png')); ?>
					<span class="label">Tout le monde</span>
				</label>
			</li>
			<!--<li><label><input type='radio' name='niveau_visibilite' value='3' onchange="affiche_formulaire_groupes(this);" /><img src='img/small/cog.png' /> <span class="label">Personnalis&eacute;</span> </label></li>-->
		</ul>
	</div><?php
}

//Fonction affichant le menu permettant de changer le niveau de visibilité
function affiche_menu_change_niveau_visibilite($niveau_actuel, $id, $peut_modifier_niveau = false)
{
	//On établit l'ID du menu de changement du niveau de visibilité
	$id_menu_changement_niveau_visibilite = "change_niveau_visibilite_".$id;
	
	//On commence par afficher (systématiquement) le niveau actuel
	echo "<img id='".$id_menu_changement_niveau_visibilite."_img'";
	if($niveau_actuel == "1")
		echo " src='".path_img_asset('users_5.png')."' title='Visible par tous le monde' />";
	elseif($niveau_actuel == "2")
		echo " src='".path_img_asset('users_3.png')."' title='Visible par moi et mes amis' />";
	elseif($niveau_actuel == "3")
		echo " src='".path_img_asset('user.png')."' title='Visible par moi uniquement' />"; 
	else
		echo " src='".path_img_asset('cog.png')."' title='Personnalis&eacute;' />";
		
	//Si l'utilisateur est autorisé, on lui affiche le menu de modification
	if($peut_modifier_niveau)
	{
		//On met une flèche pour proposer à l'utilisateur de changer le niveau de visibilité
		echo " <img src='".path_img_asset('bullet_arrow_right.png')."' title='Changer le niveau de visibilit&eacute;' onClick='show_hide_id(\"".$id_menu_changement_niveau_visibilite."\", \"visible\");' />";
		
		//On envoi le code source de changement du niveau
		echo "<span id='".$id_menu_changement_niveau_visibilite."' class='change_niveau_visibilite'>";
			echo "<img src='".path_img_asset('user.png')."' title='Visible par moi uniquement' onClick='change_niveau_visibilite_post(".$id." ,3)' /> ";
			echo "<img src='".path_img_asset('users_3.png')."' title='Visible par moi et mes amis' onClick='change_niveau_visibilite_post(".$id." ,2)' /> ";
			echo "<img src='".path_img_asset('users_5.png')."' title='Visible par tous le monde' onClick='change_niveau_visibilite_post(".$id." ,1)' /> ";
			echo "<img src='".path_img_asset('small/cross.png')."' title='Fermer ce menu' onClick='this.parentNode.style.visibility=\"hidden\"' /> ";
		echo "</span>";
		
		//On masque maintenant le menu de changement de niveau
		echo "<style type='text/css'>#".$id_menu_changement_niveau_visibilite." { visibility: hidden; }</style>";
	}
}

//Fonction permettant de redimensionner et d'enregistrer une nouvelle image
function redimensionnne_enregistre_image($x1, $x2, $y1, $y2, $adresse_source_image, $adresse_destination_image, $width, $height, $resize = "yes", $resize_width = 128, $resize_height = 128)
{
	//Sécurité
	$x1 = $x1*1;
	$x2 = $x2*1;
	$y1 = $y1*1;
	$y2 = $y2*1;

	//Récupération des inofrmations initiales sur l'image
	$info_image = getimagesize($adresse_source_image);
				
	if($info_image['mime']=='image/jpeg') 
	{
		// Création des instances d'image (jpeg)
		$src = imagecreatefromjpeg($adresse_source_image);
	}
	elseif($info_image['mime']=='image/png') 
	{
		// Création des instances d'image (png)
		$src = imagecreatefrompng($adresse_source_image);
	}
	elseif($info_image['mime']=='image/gif') 
	{
		// Création des instances d'image (gif)
		$src = imagecreatefromgif($adresse_source_image);
	}
	else
	{
		die("<p>Type d'image incompatible avec ce service.</p>");
	}
	
	//Création de l'image de destination
	$dest = imagecreatetruecolor($width, $height);

	// Copie (varie en fonction de la nécessité de redimensionner ou pas l'image)
	if($resize == "yes")
		imagecopyresized($dest, $src, 0, 0, $x1, $y1, $resize_width, $resize_height, $x2-$x1, $y2-$y1); //Copie avec redimensionnement
	else
		imagecopy($dest, $src, 0, 0, $x1, $y1, $x2-$x1, $y2-$y1); //Copie avec redimensionnement
	
	//Suppression de l'ancienne image
	if(file_exists($adresse_source_image))
	{
		//Suppression de l'image
		unlink($adresse_source_image);
	}
	
	// Affichage et libération de la mémoire
	//header('Content-Type: image/gif');
	imagejpeg($dest, $adresse_destination_image);

	imagedestroy($dest);
	imagedestroy($src);
}

//Fonction permettant d'établir si un répertoire désigne un utilisateur de Comunic
function folder_is_an_user($repertoire, $bdd)
{
	//Requête de recherche
	$sql = "SELECT * FROM utilisateurs WHERE sous_repertoire = ?";
	$requete = $bdd->prepare($sql);
	$requete->execute(array($repertoire));
	
	//Analyse
	if($analyser = $requete->fetch())
		return $analyser; //Le résultat est positif
	else
		return false; //Le résultat est négatif
		
	//Fermeture de la requête
	$requete->closeCursor();
}

//Fonction permettant de vérifier si un répertoire est libre, valide et autorisé
function verifie_validite_sous_repertoire($nom, $bdd)
{
	$valide = true;
	
	//Liste des caractères incorrects
	$list_invalid_chars = array(
		"/",
		"&",
		"?",
		"^",
		"\\",
		"$",
		"£",
		"*",
		"]",
		"'",
		'"',
		"{",
		"~",
		".php",
		".htm",
		".jpg",
		".gif",
		".png",
		".exe",
		".gif",
		".tar",
		".txt",
		".bat",
		"../",
		".cgi",
		".py",
		".ht"
	);
	
	//Liste noire
	$black_list = array(
		"admin",
		"help",
		"img",
		"css",
		"js",
		"audio",
		"index.php",
		"action.php",
		"lang",
		"winpe",
		"upload",
		"doc",
		"fonts",
		"comunic",
		"communiquons",
		"service.pierre"
	);
	
	//On commence par vérifier qu'il ne possède pas de caractères comprommettants et qu'il n'est pas vide
	$valide = ($nom == "" ? false : $valide);
	foreach($list_invalid_chars as $verifier)
		$valide = (str_replace($verifier, "", $nom) != $nom ? false : $valide);
	
	//On vérifie maintenant si il n'est pas sur la liste noire
	foreach($black_list as $verifier)
		$valide = ($nom == $verifier ? false : $valide);
		
	//On vérifie si il est disponible
	$valide = (folder_is_an_user($nom, $bdd) ? false : $valide);
	
	//On renvoi le résultat
	return $valide;
	
}

//Fonctions de vidéo
//Fonction permettant de lister l'ensemble des vidéos de l'utilisateur
function liste_videos_user($id, $bdd)
{
	$sql = "SELECT * FROM galerie_video WHERE ID_user = ?";
	
	//Exécution de la requête
	$requete = $bdd->prepare($sql);
	$requete->execute(array($id));
	
	//Enregistrement des résulats
	$liste = array();
	while($enregistrer = $requete->fetch())
	{
		$liste[] = $enregistrer;
	}
	
	//Fermeture de la requête
	$requete->closeCursor();
	
	//Renvoi du résultat
	return $liste;
}

//Fonction d'affichage de vidéo (ancienne version) ! La nouvelle version n'étant pas fonctionelle, on utilise celle-là à la place !
function affiche_video($liste_url, $preload="metadata", $width="640", $height="264", $image_par_defaut="none", $controls = "controls", $autoload_videojs = true, $id_video = false)
{
	?><video id="<?php echo (!$id_video ? sha1($width.$height.$liste_url[0][0]) : $id_video); ?>" class="video-js vjs-default-skin" <?php echo $controls; ?> preload="<?php echo $preload; ?>" width="<?php echo $width; ?>" height="<?php echo $height; ?>" <?php 
		   if($image_par_defaut != "none") echo ' poster="'.$image_par_defaut.'"'; //Image par défaut
		   if($autoload_videojs) echo ' data-setup="{}"'; //Chargement de VideoJS automatique
		   echo ' >'; //Fermeture de la balise
		   
			//Listage des URL de source
			foreach($liste_url as $afficher)
			{
				echo "<source src='".webUserDataFolder($afficher[0])."' type='".$afficher[1]."' />";
			}
		?>
		<p class="vjs-no-js">Afin de pouvoir visionner cette vid&eacute;o, veuillez activer Javasript ou mettre votre navigateur &agrave; jour.</a></p>
	</video><?php
}

//Fonction de conversion du nombre d'octets vers le nombre de MO.
function convertis_octets_vers_mo($valeur)
{
	//Conversion de la valeur
	$valeur = $valeur/1024; //Convertion en KO
	$valeur = $valeur/1024; //Convertion en MO
	$valeur = round($valeur, 2); //Arrondis de la valeur à deux chiffres après la virgule

	//Renvoi du résultat
	return $valeur;
}

//Fonction renvoyant l'ID de la vidéo la plus récente de l'utilisateur
function id_video_plus_recente($id, $bdd)
{
	//Requête de recherche de la vidéo la plus récente
	$sql = "SELECT * FROM galerie_video WHERE ID_user = ? ORDER BY ID DESC LIMIT 1";
	
	//Exécution de la requête
	$requete = $bdd->prepare($sql);
	$requete->execute(array($id));
	
	//Vérification et envoi du résultat
	if($afficher = $requete->fetch())
	{
		//Fermeture du curseur
		$requete->closeCursor();
		
		//Renvoi du résultat
		return $afficher['ID'];
	}
	
	//Fermeture du curseur
	$requete->closeCursor();
	
	//Message d'erreur
	die("Il n'y a pas de vid&eacute;o disponible pour cet utilisateur. <a href='index.php'>Retour</a>");
}

//Fonction permettant de vérifier l'existence d'une vidéo
function isset_video($id_video, $id_user, $bdd)
{
	//Requête de recherche de la vidéo
	$sql = "SELECT * FROM galerie_video WHERE ID = ? && ID_user = ?";
	
	//Exécution de la requête
	$requete = $bdd->prepare($sql);
	$requete->execute(array($id_video*1, $id_user*1)); //Inclusion de sécurité
	
	//Vérification et envoi du résultat
	if($afficher = $requete->fetch())
	{
		//Fermeture du curseur
		$requete->closeCursor();
		
		//Renvoi du résultat
		return true;
	}
	
	//Fermeture du curseur
	$requete->closeCursor();
	
	//Retour négatif
	return false;	
}

//Fonction d'ajout de vidéo
function add_movie($idpersonne, $texte, $idvideo, $bdd, $niveau_visibilite = 2, $amis = 0)
{
	$sql = 'INSERT INTO texte (ID_personne, date_envoi, idvideo, type, ID_amis, texte, niveau_visibilite) VALUES (?, NOW(), ?, ?, ?, ?, ?) ';
	
	//Exécution de la requete
	$insertion = $bdd->prepare($sql);
	$insertion->execute(array($idpersonne, $idvideo, "video", $amis, corrige_echapement($texte), $niveau_visibilite));
	
	//Et la notification (uniquement si nécessaire => Si le post n'a pas un niveau égal à 3)
	if(!visibilite_privee($niveau_visibilite))
	{
		if($amis == 0) //Envoi de la notification
			sendnotification($_SESSION['ID'], "a ajout&eacute; une vid&eacute;o personnelle sur sa page.", $bdd, "page:".$_SESSION['ID'], "", "video");
		else //Si c'est un ami
		{
			$infopersonne = cherchenomprenom($idpersonne, $bdd);
			sendnotification($_SESSION['ID'], "a ajout&eacute; une vid&eacute;o personnelle sur la page de ".$infopersonne['prenom'].' '.$infopersonne['nom'].".", $bdd,  "page:".$infopersonne['ID'], $infopersonne['ID'], "video");
		}
	}
	else
	{
		if($amis == 0) //Envoi de la notification
			sendnotification($_SESSION['ID'], "a ajout&eacute; une vid&eacute;o personnelle sur sa page.", $bdd, "page:".$_SESSION['ID'], "", "video", list_personnes_groupes($niveau_visibilite, $bdd));
		else //Si c'est un ami
		{
			$infopersonne = cherchenomprenom($idpersonne, $bdd);
			sendnotification($_SESSION['ID'], "a ajout&eacute; une vid&eacute;o personnelle sur la page de ".$infopersonne['prenom'].' '.$infopersonne['nom'].".", $bdd,  "page:".$infopersonne['ID'], $infopersonne['ID'], "video", list_personnes_groupes($niveau_visibilite, $bdd));
		}
	}
}

//Fonction de récupération des informations d'une vidéo
function get_info_video($id, $bdd)
{
	//Requête de recherche
	$sql = "SELECT * FROM galerie_video WHERE ID = ?";
	
	//Exécution de la requête
	$requete = $bdd->prepare($sql);
	$requete->execute(array($id*1)); //Sécurité implémentée
	
	//Récupération du résultat
	$retour = $requete->fetch();
	
	//Fermeture de la requête
	$requete->closeCursor();
	
	//Renvoi du résultat
	return $retour;
}

//Fonction permettant de lister l'ensemble des posts relatifs à une vidéo
function list_ensemble_posts_relatif_a_video($id, $bdd)
{
	//Requête de recherche
	$sql = "SELECT * FROM texte WHERE idvideo = ?";
	
	//Exécution de la requete
	$requete = $bdd->prepare($sql);
	$requete->execute(array($id));
	
	//Enregistrement des résultats
	$liste = array();
	while($enregistrer = $requete->fetch())
	{
		$liste[] = $enregistrer;
	}
	
	//Fermeture de la requête
	$requete->closeCursor();
	
	//Renvoi du résultat
	return $liste;
}

//Fonction permettant de vérifier si un compte existe
function isset_account($id, $bdd)
{
	//Requête de recherche
	$sql = "SELECT * FROM utilisateurs WHERE ID = ?";
	$requete = $bdd->prepare($sql);
	$requete->execute(array($id*1)); //Sécurité incorporée
	
	//Vérification
	if($verifier = $requete->fetch())
		$retour = true;
	else
		$retour = false;
		
	//Fermeture de la requête
	$requete->closeCursor();
	
	//Renvoi du résultat
	return $retour;
}

//Fonction permettant de récupérer la liste des types de contact
function get_list_type_contact($bdd)
{
	//Requête de recherche
	$sql = "SELECT * FROM sujet_contact";
	$requete = $bdd->query($sql);
	
	//Enregistrement des résultats
	$liste = array();
	while($enregistrer_type_contact = $requete->fetch())
	{
		$liste[] = $enregistrer_type_contact;
	}
	
	//Fermeture de la requête
	$requete->closeCursor();
	
	//Renvoi du résultat
	return $liste;
}

//Fonction de cryptage de mot de passe selon la stratégie de mot de passe utilisateur de Comunic
function crypt_password($password)
{
	//Cryptage du mot de passe et renvoi du résultat
	$password = sha1($password);
	return crypt($password, $password); 
}

//Fonction permettant d'ajouter l'URL du site sur un block de texte
function add_url_site($source, $urlsite)
{
	//Conversion et renvoi du résultat
	return str_replace("%URL_SITE%", $urlsite, $source);
}

//Transforme une date issue d'un datepicker en date normalisée pour la base de données
function normalise_datepicker($date)
{
	//Transformation de la date en tableau
	$array_date = explode(".", $date);
	
	//Vérification de sécurité
	if(count($array_date) != 3)
		die("Date incorrecte!");
			
	return $array_date[2]."-".$array_date[1]."-".$array_date[0]." 00:00:00";
}

//Fonction permettant de déterminer si une personne est abonnée aux notifications d'une autre
function personne_abonnee_notification_ami($ami, $personne_connectee, $bdd)
{
	//On récupère les informations sur la table ami
	$sql = "SELECT * FROM amis WHERE (ID_personne = ?) && (ID_amis = ?) && (actif = 1)";
	$requeteamis = $bdd->prepare($sql);
	$requeteamis->execute(array($ami, $personne_connectee));
	
	//On renvoi le résultat suivant si la personne est amie ou non
	if(!$info_ami = $requeteamis->fetch())
	{
		//Fermeture de la requete
		$requeteamis->closeCursor();
		
		//La personne ne peut pas être abonnée: elle n'est pas amie
		return false;
	}
	
	//Fermeture de la requete
	$requeteamis->closeCursor();
	
	//On définit maintenant si la personne est amie ou pas
	return ($info_ami['abonnement'] == 0 ? false : true);
}

//Fonction d'enregistrement d'une image encodée en base64 vers un fichier
function base64_to_jpeg($base64_string, $output_file) {
	$ifp = fopen($output_file, "wb"); 

	$data = explode(',', $base64_string);

	fwrite($ifp, base64_decode($data[1])); 
	fclose($ifp); 

	return $output_file; 
}

//Fonction de nettoyage des anciennes notifications
function nettoie_anciennes_notifications($id, $bdd)
{
	//Récupération des informations sur l'utilisateur
	$info_user = cherchenomprenom($id, $bdd);
	
	//On vérifie que le nettoyage automatique des anciennes notifications est activé
	if($info_user['nettoyage_automatique_notifications'] == 1)
	{
		//On crée la date avant laquelle les notifications seront supprimées
		$array_date = getdate();
		
		//On récupère la date en seconde en dessous de laquelle les notifications seront supprimées
		$time = strtotime("-".$info_user['mois_nettoyage_automatique_notifications']." month -".$info_user['jour_nettoyage_automatique_notifications']." days -".$info_user['heure_nettoyage_automatique_notifications']." hours");
		
		//On convertit la date en format normalisé
		$date = date("Y-m-d H:i:s", $time);
		
		//On supprime les notifications antérieurs à cette date MAINTENANT
		$sql = "DELETE FROM notification WHERE date_envoi <= ? AND ID_personne = ?";
		
		//Exécution de la requête
		$requete = $bdd->prepare($sql);
		$requete->execute(array($date, $id));
	}
}

//Fonction retournant le prénom suivi du nom d'un utilisateur
function return_nom_prenom_user($id, $bdd)
{
	//On recherche le prénom et le nom de l'utilisateur
	$info_user = cherchenomprenom($id, $bdd);
	
	//On renvoi le résultat
	return $info_user['prenom']." ".$info_user['nom'];
}

//Fonction optimisant la fonction return_nom_prenom_user en évitant de faire des requêtes inutiles
function optimise_search_info_users($id, $bdd, $liste = array())
{
	if(!isset($liste[$id]['table_utilisateurs']) || !isset($liste[$id]['avatar_32_32']))
	{
		$liste[$id]['table_utilisateurs'] = cherchenomprenom($id, $bdd);
		$liste[$id]['avatar_32_32'] = avatar($id, "./", 32, 32);
	}
	
	//Renvoi de la liste
	return $liste;
}

//Fonction d'affichage de la vidéo d'un texte
function affiche_video_texte($id, $bdd)
{
	//Récupération des informations de la vidéo
	$info_video = get_info_video($id, $bdd);
	
	//Préparation de l'affichage des vidéos
	echo "<div class='video_contener'>";
	
		//Affichage de la vidéo
		affiche_video(array(array($info_video['URL'], $info_video['file_type'])));
	
	//Fermeture de l'affichage de la vidéo
	echo "</div>";
}


//Fonction d'affichage de l'image d'un texte
function affiche_image_texte($info, $afficher_infos = false)
{
	//On vérifie qu'il s'agit d'une image
	if($info["type"] == "image")
	{
		//Affichage de l'image
		echo '<a class="fancybox" rel="group" href="';
		echo webUserDataFolder($info['path']);
		echo '"><img height="200" src="';
		echo webUserDataFolder($info['path']);
		echo '" alt="" /></a> <br />';
		
		//Si nécessaire on affiche les données techniques de l'image
		if($afficher_infos)
		{
			echo "Taille de l'image: ".convertis_octets_vers_mo($info['size'])." Mo;";
			echo " Type d'image: ".$info['file_type'].";";
			echo " Chemin d'acc&egrave;s: <a href='".webUserDataFolder($info['path'])."'>".webUserDataFolder($info['path'])."</a><br />";
		}
	}
}

//Fonction de suppression d'une vidéo
function delete_movie($idvideo, $iduser, $bdd)
{
	//Récupération des informations
	$sql = "SELECT * FROM galerie_video WHERE ID = ? AND ID_user = ?";
		
	//Exécution de la requête
	$requete = $bdd->prepare($sql);
	$requete->execute(array($idvideo, $iduser));
		
	if(!$analyser = $requete->fetch())
		die("Vid&eacute;o non trouv&eacute;.");
		
	//Fermeture de la requête
	$requete->closeCursor();
		
	//Suppression des posts relatifs à la video
	$list_posts_relatif = list_ensemble_posts_relatif_a_video($_GET['delete'], $bdd); //Récupération de la liste
	foreach($list_posts_relatif as $enregistrer)
	{
		//Suppression du post
		deletetexte($enregistrer['ID'], $enregistrer['texte'], $bdd);
	}
		
	//Suppression du fichier
	unlink(relativeUserDataFolder($analyser['URL']));
		
	//Suppression de l'entrée dans la base de données
	$sql = "DELETE FROM galerie_video WHERE ID = ? AND ID_user = ?";
		
	//Exécution de la requete
	$requete = $bdd->prepare($sql);
	$requete->execute(array($_GET['delete'], $_SESSION['ID']));
		
}

//Fonction permettant de définir si une personne est autorisée à visualiser une page ou pas et avec quel niveau de visibilité
function is_allowed_to_view_page($idpage, $bdd)
{
	//Récupération des informations sur la page
	$info_page = cherchenomprenom($idpage, $bdd);
	
	//On vérifie si la personne n'est pas connectée
	if(!isset($_SESSION['ID']))
	{
		if($info_page['pageouverte'] == 0)
			return false; //Personne non autorisée
		else
			return 1; //Personne autorisée à voire les posts publiques uniquements
	}
	
	//La personne est connectée
	//On vérifie si c'est la page de la persone
	if($_SESSION['ID'] == $idpage)
		return 3; //La personne peut voire tout les posts
	
	//On vérifie si les persones sont amies
	if(!detectesilapersonneestamie($_SESSION['ID'], $idpage, $bdd))
	{
		//On vérifie si la page est publique
		if($info_page['public'] == 1)
			return 1; //La personne est autorisée à visualiser les posts publics
		else
			return false; //La personne n'est pas autorisée à visualiser les posts publiques
	}
	
	//La personne est autorisée à visualiser les posts de niveau 2 (avec les amis)
	return 2;
}

//Fonction renvoyant le code source pour afficher une vidéo YouTube
function code_video_youtube($adresse)
{
	//Préparation de la source
	$source = "";
	
	//Génération de la source
	$source .= '<iframe width="420" height="315" src="https:/';
	$source .= '/www.youtube.com/embed/';
	$source .= $adresse;
	$source .= '?rel=0" frameborder="0" allowfullscreen></iframe> <br />';
	
	//Renvoi du code source
	return $source;
}

//Fonction convertissant une date type "NOW()" en timestamp
//Exemple de date : 2013-11-16 14:30:00
function to_timestamp($source)
{
	//Création d'un premier tableau
	$array_1 = explode(" ", $source);
	if(count($array_1) != 2) return $source; //Rien à faire
	
	//Création du second tableau
	$array_2 = explode("-", $array_1[0]);
	if(count($array_2) != 3) return $source; //Rien à faire
	
	//Création du troisième tableau
	$array_3 = explode(":", $array_1[1]);
	if(count($array_2) != 3) return $source; //Rien à faire
	
	//Création et renvoi du résultat
	return mktime($array_3[0], $array_3[1], $array_3[2], $array_2[1], $array_2[2], $array_2[0]);
}

//Fonction permettant d'adapter la date pour l'affichages d'une page
function adapte_date($source, $timestamp = 0)
{
	if($timestamp == 0) 
		$timestamp = to_timestamp($source)-3600; //Exemple de date : 2013-11-16 14:30:00
		
	$now = time();
	$difference = $now-$timestamp;
	
	if($difference == 0) //Il y a moins d'une seconde
		return "Il y a quelques instants";
	elseif($difference == 1) //Il y a une seconde
		return "Il y a une seconde";
	elseif($difference <= 60) //Inférieur à une minute
		return "Il y a ".$difference." secondes";
	elseif($difference <= 3600) //Inférieur à une heure
	{
		$nb_minutes = floor($difference/60);
		if($nb_minutes == 1)
			return "Il y a une minute";
		else
			return "Il y a ".$nb_minutes." minutes";
	}
	elseif($difference <= 86400) //Inférieur à un jour
	{
		$nb_heures = floor(($difference/60)/60);
		if($nb_heures == 1)
			return "Il y a une heure";
		else
			return "Il y a ".$nb_heures." heures";
	}
	elseif($difference <= 2678400) //Inférieur à un mois
	{
		$nb_jours = floor((($difference/60)/60)/24);
		if($nb_jours == 1)
			return "Il y a un jour";
		else
			return "Il y a ".$nb_jours." jours";
	}
	elseif($source != "") //On affiche le jour précis
	{
		//On se sert du timestamp : $timestamp
		$datas_date = date( "w|j|n|Y|H|i|s" , $timestamp);
		$array_date = explode('|', $datas_date);
		
		//On vérifie que la date est correcte
		if(count($array_date) != 7)
			return $date; //Rien à faire
		
		//Définition des données
		$days = array("dimanche", "lundi", "mardi", "mercredi", "jeudi", "vendredi", "samedi");
		$months = array("janvier", "f&eacute;vrier", "mars", "avril", "mai", "juin", "juillet", "ao&ucirc;t", "septembre", "octobre", "novembre", "d&eacute;cembre");
		
		
		//Renvoi du résultat
		return "Le ".$days[$array_date[0]]." ".$array_date[1]." ".$months[$array_date[2]-1]." ".$array_date[3]." &agrave; ".$array_date[4].":".$array_date[5].":".$array_date[6];
	}
	else //Rien à faire, on renvoi la date
		return $source;
}
	
//Fonction permettant de récupérer la source des commentaires dans un tableau
function getsourcecommentaire_html($source, $type, $fin = '\n')
{
	preg_match_all('#'.$type.'(.*?)'.$fin.'#is', $source, $resultat, PREG_PATTERN_ORDER);
	
	//On renvoi le résultat
	return $resultat[0];
}

//Fonction de compilation de code source (nécessite : getsourcecommentaire_html)
function compile_code_source($source, $allow_comment_slash_slash = false, $free_hosting = false)
{
	if($allow_comment_slash_slash == false)
	{
		$source = str_replace(":/"."/", ':adaptecompilewithcomunicforsecurityofhttppleasedontremove', $source); //Protection http://
		$source = str_replace(getsourcecommentaire_html($source, '/'.'/'), '', $source);
		$source = str_replace(':adaptecompilewithcomunicforsecurityofhttppleasedontremove', ":/"."/", $source); //Retire protection http://
	}
	$source = str_replace("\n", ' ', $source);
	$source = str_replace(getsourcecommentaire_html($source, '<!'.'--', '-'.'->'), '', $source);
	//$source = str_replace(getsourcecommentaire_html($source, '/*', '*/'), '', $source);
	//$source = str_replace(getsourcecommentaire_html($source, '', ''), '', $source);
	$source = str_replace('	', ' ', $source);
	
	//Correction des problèmes Javascript
	$source = str_replace(' xhr.send(null);', "\n xhr.send(null);", $source);
	
	//On enlève les espaces en trop
	for ($i = 0; 15>$i; $i++)
	{
		$source = str_replace('  ', ' ', $source);
	}
	
	//Dernières corrections... ! Non opérationnel !
	//$source = str_replace('> <', '><', $source);
	//$source = str_replace('; ', ';', $source);
	
	//On adapte si il s'agit de Comunic
	if($free_hosting == true)
	{
		//Adaptation de la page
		$source = str_replace('chat.js', 'comunicrapid.js', $source);
		$source = str_replace('chat.css', 'comunicrapid.css', $source);
		$source = str_replace('chat.php', 'comunicrapid.php', $source);
	}
	
	//Renvoi de la source
	return $source;
}

//Fonction permettant de lister l'ensemble des groupes d'une personne
function list_groupes_personnes($id_personne, $bdd)
{
	//Requête de recherche
	$sql = "SELECT * FROM groupe_personnes WHERE ID_personne = ?";
	
	//Exécution de la requête
	$requete = $bdd->prepare($sql);
	$requete->execute(array($id_personne*1));
	
	//Enregistrement des résultats
	$retour = array();
	while($enregistrer = $requete->fetch())
	{
		$retour[] = $enregistrer;
	}
	
	//Fermeture de la requête
	$requete->closeCursor();
	
	//Renvoi du résultat
	return $retour;
}

//Print_r d'un tableau avec <pre> intégré
function print_r_pre($tableau)
{
	echo "<pre>";
	print_r($tableau);
	echo "</pre>";
}

//Fonction permettant de rechercher l'appartenance à un groupe
function search_appartenance_groupes($id_user, $id_user_connected, $bdd, $open = true)
{
	//On vérifie si l'on doit chercher tous les groupes ou non
	if(!$open)
	{
		//Requête de recherche
		$sql = "SELECT ID FROM groupe_personnes WHERE ID_personne = ? AND ((liste_ID = ?) OR (liste_ID LIKE ?) OR (liste_ID LIKE ?) OR (liste_ID LIKE ?))";
		$requete = $bdd->prepare($sql);
		$requete->execute(array($id_user, $id_user_connected, "%|".$id_user_connected, $id_user_connected."|%", "%|".$id_user_connected."|%"));
	}
	else
	{
		//Requête de recherche
		$sql = "SELECT ID FROM groupe_personnes WHERE (liste_ID = ?) OR (liste_ID LIKE ?) OR (liste_ID LIKE ?) OR (liste_ID LIKE ?)";
		$requete = $bdd->prepare($sql);
		$requete->execute(array($id_user_connected, "%|".$id_user_connected, $id_user_connected."|%", "%|".$id_user_connected."|%"));
	}
	
	//Enregistrement des résultats
	$retour = array();
	while($enregistrer = $requete->fetch())
	{
		$retour[] = $enregistrer['ID'];
	}
	
	//Fermeture de la requête
	$requete->closeCursor();
	
	//Retour des résultat
	return $retour;
}

//Fonction permettant de dire si la vidéo est partiellement (ou totalement) restreinte
function visibilite_privee($niveau_visibilite)
{
	if($niveau_visibilite == 3)
		return true;
	elseif(preg_match("<3>", $niveau_visibilite))
		return true;
	else
		return false; //La vidéo est publique
}

//Fonction permettant de finir la compilation de source
function fin_mise_en_cache()
{
	$source = ob_get_contents();
	ob_end_clean();
	echo compile_code_source($source);
}

//Fonction permettant de définir si une page en cache est disponible ou non pour ce fichier
function is_page_cached($nom_page)
{
	if(file_exists("cache/".sha1($nom_page)))
		return true;
	else
		return false;
	
}

//Fonction d'écriture du fichier dans le cache
function write_cache($file, $source)
{
	//Adaptation du nom de fichier si nécessaire
	if(!preg_match("<cache/>", $file))
		$file = "cache/".sha1($file);
		
	//Ecriture du fichier dans le cache
	file_put_contents($file, $source);
}

//Fonction permettant d'afficher un message d'erreur
function affiche_message_erreur($message, $metro_enabled = false)
{
	if(!$metro_enabled)
		echo '<span class="metro"><p class="padding20 bg-darkRed fg-white"><span class="icon-warning"> &nbsp; '.$message.'</span></p></span>';
	else
		echo '<p class="padding20 bg-darkRed fg-white"><span class="icon-warning"> &nbsp; '.$message.'</span></p>';
}

//Fonction d'affichage d'un compte à rebours
function affiche_compte_rebours($infos_texte)
{
	//Détermination de la date de fin
	//$date_fin = date("D M d Y H:i:s", strtotime($infos_texte['annee_fin']."-".$infos_texte['mois_fin']."-".$infos_texte['jour_fin']))." UTC"; //Sun Aug 05 2035 12:00:00 GMT+0200
	//$date_fin = date("c", strtotime($infos_texte['annee_fin']."-".$infos_texte['mois_fin']."-".$infos_texte['jour_fin']))." GMT+0200"; //Sun Aug 05 2035 12:00:00 GMT+0200
	$date_fin = strtotime($infos_texte['annee_fin']."-".$infos_texte['mois_fin']."-".$infos_texte['jour_fin']);
	//$date_fin = time();
	$id_counter = 'time_'.$infos_texte['ID'];
	
	echo '<timers>'.$id_counter.'|'.$date_fin.'</timers><div class="metro"><div class="countdown" id="'.$id_counter.'" data-role="countdown" data-stoptimer="'.$date_fin.'" data-style-background="bg-cyan" style="font-size: 60px"></div></div>';

	?><script>launch_countdown(<?php echo $date_fin; ?>, '<?php echo $id_counter; ?>');</script><?php
}

//Fonction d'affichage d'un lien vers une page externe
function affiche_lien_webpage($infos_texte)
{
	if($infos_texte['type'] == "webpage_link")
	{
		if($infos_texte['url_page'] != "")
		{
			//Titre de la page
			if($infos_texte['titre_page'] != "default")
				$titre_page = $infos_texte['titre_page'];
			else
				$titre_page = "Page Web";
			
			//Description de la page
			if($infos_texte['description_page'] != "default")
				$description_page = $infos_texte['description_page'];
			else
				$description_page = "Page web externe";
			
			//Image de la page
			if($infos_texte['image_page'] != "default")
				$image_page = $infos_texte['image_page'];
			else
				$image_page = path_img_asset('world.png');
			
			?><div class="link_webpage">
				<a target="_blank" href="<?php echo $infos_texte['url_page']; ?>">
					<img class="image_webpage" src="<?php echo $image_page; ?>" />
					<div class="titre_webpage"><?php echo $titre_page; ?></div>
					<div class="url_webpage"><?php echo $infos_texte['url_page']; ?></div>
					<div class="description_webpage"><?php echo $description_page; ?></div>
				</a>
			</div>
			<?php
		}
	}
}

//Fonction d'affichage d'un lien vers un PDF
function affiche_lien_pdf($infos_texte)
{
	if($infos_texte['type'] == "pdf")
	{
		if($infos_texte['path'] != "")
		{
			//Titre de la page
			?><div class="link_pdf">
				<a target="_blank" href="<?php echo webUserDataFolder($infos_texte['path']); ?>">
					<?php echo code_inc_img(path_img_asset('file_extension_pdf.png'), "T&eaucte;l&eacute;charger le PDF"); ?>
					Fichier PDF
				</a>
			</div>
			<?php
		}
	}
}

//Fonction permettant de définir toutes les personnes autorisées à voir un post
function list_personnes_groupes($valeur, $bdd)
{
	//On importe la liste dans un tableau
	$array = explode("|", $valeur);
	
	//On supprime la première valeur (valeur à validité générale)
	unset($array[0]);
	
	//Préparation du retour
	$retour = array();
	
	//On ne continue que si la liste n'est pas vide
	if(count($array) != 0)
	{
		//On traite maintenant la liste
		$sql = "SELECT * FROM groupe_personnes WHERE ID = ? ";
		
		foreach($array as $ajouter)
		{
			$sql .= "OR ID = ? ";
		}
		
		$array[0] = $array[1];
		
		//Exécution de la requête
		$requete = $bdd->prepare($sql);
		$requete->execute($array);
		
		//Affichage des résultats
		while($afficher = $requete->fetch())
		{
			//Récupération de la liste des membres du groupe
			$liste_membres = explode('|', $afficher['liste_ID']);
			$liste_membres[] = $afficher['ID_personne'];
			
			//Traitement de la liste des membres
			foreach($liste_membres as $traiter)
			{
				//On vérifie si le membre a déjà été ajouté
				if(!in_array($traiter, $retour, true))
					$retour[] = $traiter; //Ajout de l'entrée
			}
		}
		
		//Fermeture de la requête
		$requete->closeCursor();
	}
	
	return $retour;
}

//Fonction permettant d'afficher qu'une page est vérifiée
function message_checked_page()
{
	echo "</td><td><img style='vertical-align: middle;' src='img/tick.png' title='Page v&eacute;rifi&eacute;e' />";
}

//Fonction permettant de rapporter une erreur durant l'exécution du comunic
function report_error($nom_erreur, $raison = "La raison n'a pas &eacute;t&eacute; sp&eacute;cifi&eacute;e.", $details = array())
{
	//Inclusion de la configuration
	include('inc/config/config.php');
	
	
	//Envoi du message
	//Vérification de l'autorisation d'envoi de mails
	if($active_envoi_mail == "oui")
	{
		//Envoi du message
		$send_mail = true;
		$sujet = "[Erreur Comunic] Erreur lors de l'exécution d'une page dans Comunic"; //Sujet
		$description_rapide = "Une erreure fatale est arrivee dans une requete pour Comunic.";
		$nom_destinataire = "Dev de Comunic";
		$adresse_mail_destinataire = $mail_envoi_erreur_sql;
		$message = "<h2 style='text-align: center'>Erreur dans une page Comunic</h2>
		<p>Bonjour, ce message vous a &eacute;t&eacute; adress&eacute; suite &agrave; une erreur fatale dans un script PHP. L'erreur PHP est la suivante : <i>".$nom_erreur."</i></p>
		<p>La raison de l'erreur est la suivante: ".$raison."</p>
		<p>Les informations compl&eacute;mentaires sont les suivantes :</p>
		<table>
			<tr><td><b>$"."_POST</b></td></tr>";
				//Parcours de la variable $_POST
				foreach($_POST as $nom=>$valeur)
					$message.= "<tr><td>".$nom." :</td><td>".$valeur."</td></tr>";
					
			$message .= "<tr><td><b>$"."_GET</b></td></tr>";
				//Parcours de la variable $_GET
				foreach($_GET as $nom=>$valeur)
					$message.= "<tr><td>".$nom." :</td><td>".$valeur."</td></tr>";
				
			$message .= "<tr><td><b>$"."_SERVER</b></td></tr>";
				//Parcours de la variable $_SERVER
				foreach($_SERVER as $nom=>$valeur)
					$message.= "<tr><td>".$nom." :</td><td>".$valeur."</td></tr>";
			$message .= "<tr><td><b>$"."_SESSION</b></td></tr>";
			
				//Parcours de la variable $_SESSION
				foreach($_SESSION as $nom=>$valeur)
					$message.= "<tr><td>".$nom." :</td><td>".$valeur."</td></tr>";
			$message .= "<tr><td><b>$"."details</b></td></tr>";
			
				//Parcours de la variable $details
				foreach($details as $nom=>$valeur)
					$message.= "<tr><td>".$nom." :</td><td>".$valeur."</td></tr>";
		$message .= "</table>"; //Message
		$texte_message = $message;
		
		//Envoi du message
		include(websiteRelativePath('inc/envoi_mail.php'));
		
		echo "<!-- MailAdmin sent -->";
	}
	
}

//Fonction permettant de déterminer si une personne a déjà participé à un sondage ou non
//Retourne false si la personne n'a pas voté
//Retourne l'ID du choix si la personne a voté
function vote_personne_sondage($id_personne, $id_sondage, $bdd)
{
	//Récupération des informations dans la base de données
	$infos_vote_sondage = select_sql("sondage_reponse", "ID_utilisateurs = ? AND ID_sondage = ?", $bdd, array($id_personne, $id_sondage));
	
	//On détermine si la personne a voté ou non
	if(count($infos_vote_sondage) == 0)
	{
		//Retour négatif
		return false;
	}
	else
	{
		//Retour de l'ID du choix
		return $infos_vote_sondage[0]['ID_sondage_choix'];
	}
}

//Fonction d'affichage d'erreur
function echo_erreur($erreur)
{
	echo "<p><b>Erreur : </b> ".$erreur."</p>";
}

//Récupération d'un sondage par l'ID du texte correspondant
function get_sondage_by_text_id($id_texte, $bdd)
{
	//Récupération des informations sur le sondage
	$infos_sondage = select_sql("sondage", "ID_texte = ?", $bdd, array($id_texte));
	
	//Si il n'y a pas de sondage => Erreur
	if(count($infos_sondage) == 0)
	{
		//Rapport d'erreur
		report_error('', 'Un sondage devrait &ecirc;tre affich&eacute;, mais celui-ci est introuvable dans la BDD. Erreur dans le fichier view_textes.php (inc).');
		
		//On casse la chaîne après erreur
		affiche_message_erreur("Une erreur a survenue lors de r&eacute;cup&eacute;ration d'informations relatives au post (Err Get Info Sond0.). Passage au texte suivant");
		echo "</td></tr>";
		
		return false;
	}
	
	return $infos_sondage;
}

//Fonction permettant de vérifier si un dossier appartient bien à un utilisateur
function checkPersonnalFolder($container_path, $id_user) {
	//Détermination du chemin
	$path = $container_path.$id_user."/";
	
	//On vérifie l'existence du dossier
	if(!is_dir($path))
	{
		if(!mkdir($path))
		{
			//Rapport d'erreur
			report_error("Error in checkPersonnalFolder()", "Impossible de cr&eacute;er un dossier personnel d'utilisateur", $details = array("path" => $path));
			
			//Erreur fatale
			echo "<p>Une erreur fatale a survenue (checkPersonnalFolder in creating personnal folder). Veuillez r&eacute;essayer...</p>";
			exit();
		}
	}
	
	//On vérifie l'existence d'un fichier index.php de sécurité
	if(!file_exists($path."index.html"))
	{
		$file_content = "<html><title>Acess Forbiden</title><h1>Access Forbiden.</h1><a href='/'>Go back to main page.</a></html>";
		$file_name = $path."index.html";
		
		if(!file_put_contents($file_name, $file_content))
		{
			//Rapport d'erreur
			report_error("Erreur in checkPersonnalFolder()", "Impossible de cr&eacute;er le fichier de s&eacute;curit&eacute; <i>index.html</i>", $details = array(
				"file" => $file_name,
				"content" => $file_content));
			
			//Erreur fatale
			echo "<p>Une erreur fatale a survenue (checkPersonnalFolder in creating security file). Veuillez r&eacute;essayer...</p>";
			exit();
		}
	}
	
	//Renvoi du chemin du dossier
	return $path;
}

//End Of File
//Merci d'utiliser le Service Pierre avec Comunic ! :)