<?php
/**
 * Project's main function file
 * For Comunic project use only
 * All rights reserved
 *
 * @author Pierre HUBERT
 * @date Began by the end of 2013
*/


//Avertissement : ce fichier n�cessite l'inclusion de la configuration (config.php)

//Affichage de l'avatar
//Recherche de l'avatar de la personne
function avatar($idavatar, $urlsite="./", $whidth=32, $height=32, $id="", $juste_avatar = false)
{	
	//On d�finit l'adresse de l'avatar
	$adresse_avatar = "0.jpg";
	
	if(file_exists(relativeUserDataFolder("avatars/adresse_avatars/".$idavatar.".txt")))
	{
		//Contr�le du niveau de visibilit�
		$niveau_visibilite_avatar = (get_niveau_visibilite_avatar($idavatar, $urlsite));
		
		//On d�finit que c'est n'est pas bon.
		$ok = false;
		
		//On contr�le l'avatar
		$ok = ($niveau_visibilite_avatar == 3 ? true : $ok); //On v�rifie si l'avatar est ouvert
		
		//On v�rifie si ce n'est pas encore bon et si l'utilisateur est connect�
		if(!$ok AND isset($_SESSION['ID']))
		{
			$ok = (($niveau_visibilite_avatar == 2) AND (isset($_SESSION['ID'])) ? true : $ok); //On v�rifie si l'avatar est ouvert et si l'utilisateur est connect�
			$ok = ($idavatar == $_SESSION['ID'] ? true : $ok); //On v�rifie si il s'agit de l'avatar de l'utilisateur actuel
			
			//On v�rifie si ce n'est pas encore bon et si les deux personnes sont amies
			//Inclusion de la base de donn�es
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
	
		//Renvoi du r�sultat
		if(!$juste_avatar)
			return "<img src='".webUserDataFolder()."avatars/".$adresse_avatar."' whidth='".$whidth."' height='".$height."' id='".$id."' />";
		else
			return webUserDataFolder()."avatars/".$adresse_avatar;
}

//Affichage de l'image de fond
//Recherche de l'image de fond de la personne
function imgfond($id_img_fond, $urlsite="./", $whidth=32, $height=32, $id="", $juste_url = false)
{
	//On v�rifie si un compl�ment est disponible
	if(file_exists(relativeUserDataFolder("imgfond/adresse_imgfond/".$id_img_fond.".txt")))
		$url =  webUserDataFolder("imgfond/".file_get_contents(relativeUserDataFolder("imgfond/adresse_imgfond/".$id_img_fond.".txt")));
	else
		$url = webUserDataFolder("imgfond/0.jpg");
	
	if(!$juste_url)
		return "<img src='".$url."' whidth='".$whidth."' height='".$height."' id='".$id."' />";
	else
		return $url;
}

//Fonction permettant de d�finir le niveau de visibilit� de l'avatar de l'utilisateur
//Valeur de retour : 
//					 1 - L'utilisateur et ses amis uniquement
//					 2 - L'utilisateur, ses amis et les personnes connect�es
//					 3 - L'utilisateur, ses amis, les personnes connect�es et non connect�es -> Tout le monde
function get_niveau_visibilite_avatar($id, $urlsite = "./")
{
	//On d�finit qu'il est visible par tout le monde
	$niveau_visibilite_avatar = 3;
	
	//On v�rifie si ce niveau de visibilit� a �t� personalis� par l'utilisateur
	$adresse_fichier = relativeUserDataFolder("avatars/adresse_avatars/limit_view_".$id.".txt");
	if(file_exists($adresse_fichier))
	{
		//R�cup�ration du contenu du fichier
		$content_file = file_get_contents($adresse_fichier);
		
		//Contr�le du contenu du fichier (on ne prendra en compte sa valeur que si elle ne pr�sente un int�r�t
		if( $content_file == 1 OR $content_file == 2 )
			$niveau_visibilite_avatar = $content_file; //On enregistre la nouvelle valeur
	}
	
	//Renvoi du r�sultat
	return $niveau_visibilite_avatar;
}

//Fonction de modification du niveau de visibilit� de l'avatar
function modifie_niveau_visibilite_avatar($id_personne, $nouveau_niveau, $urlsite = "./")
{
	//D�finition de l'adresse du fichier
	$adresse_fichier = relativeUserDataFolder("avatars/adresse_avatars/limit_view_".$id_personne.".txt");
	
	//Contr�le du nouveau niveau
	if($nouveau_niveau == 3)
	{
		//Niveau �gal � trois -> Suppression du fichier
		if(file_exists($adresse_fichier))
			//Suppression du message
			return unlink($adresse_fichier);
		else
			//Rien � faire, retour positif
			return true;
	}
	elseif($nouveau_niveau == 2 OR $nouveau_niveau == 1)
	{
		//Edition du fichier et retour (positif ou n�gatif suivant la r�ponse)
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
	
	//Ex�cution de la requete
	$insertion = $bdd->prepare($sql);
	$insertion->execute(array($idpersonne, corrige_echapement($texte), $niveau_visibilite, $type, $path, $annee_fin, $mois_fin, $jour_fin, $url_page, $titre_page, $description_page, $image_page));
	
	//Et la notification (uniquement si n�cessaire => Si le post n'a pas un niveau �gal � 3)
	if(!visibilite_privee($niveau_visibilite))
		sendnotification($_SESSION['ID'], "a ajout&eacute; un texte sur sa page.", $bdd, "page:".$_SESSION['ID']);
	else
		sendnotification($_SESSION['ID'], "a ajout&eacute; un texte sur sa page.", $bdd, "page:".$_SESSION['ID'], "", "texte", list_personnes_groupes($niveau_visibilite, $bdd));
}

//Fonction d'ajout de texte sur la page d'un amis
//Param�tres de la fonction
//$idpersonne : la personne qui ENVOI le texte
//$idamis la personne � qui est DESTINE le texte
//-----------------------------------------
//DB SQL
//ID_personne : la personne � qui est DESTINE le texte
//ID_amis : la personne qui ENVOI le texte
function ajouttexte_amis($idpersonne, $idamis, $texte, $bdd, $niveau_visibilite = 2, $type = "texte", $path = "", $annee_fin = 0, $mois_fin = 0, $jour_fin = 0, $url_page="", $titre_page = "", $description_page = "", $image_page = "")
{
	$sql = 'INSERT INTO texte (ID_personne, ID_amis, date_envoi, texte, niveau_visibilite, type, path, annee_fin, mois_fin, jour_fin, url_page, titre_page, description_page, image_page) VALUES (?, ?, NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) ';

	//Ex�cution de la requete
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
	//V�rification de l'autorisation d'envoi de mails
	if($active_envoi_mail == "oui" && $info_destinataire['autorise_mail'] == 1)
	{
		//Envoi d'un message au demand�
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
//Fonction n�cessaire : liste_amis
function demandeamis($idpersonne, $iddemandeur, $bdd)
{
	//On v�rifie si une demande n'a pas d�j� �t� post�e
	$liste_verification = liste_amis($idpersonne, $bdd, 0);
	
	//On v�rifie
	foreach($liste_verification as $verifier)
	{
		if($verifier == $iddemandeur)
		{
			//On affiche un message d'erreur
			echo "<script>alert('La personne a d�ja �t� demand�e en amis.');</script>";
			
			//On bloque le tout
			$bloque = 1;
		}
	}
	
	//On v�rifie si un bloquage a �t� effectu�
	if(!isset($bloque))
	{
		//On poste la demande
		$sql = "INSERT INTO amis (ID_personne, ID_amis) VALUES (?, ?)";
		
		//Ex�cution de la requete
		$insertionamis = $bdd->prepare($sql);
		$insertionamis->execute(array($idpersonne, $iddemandeur));
	
		//Inclusion de la configuration
		include('inc/config/config.php');
		
		//V�rification de l'autorisation d'envoi de mails
		$info_destinataire_demande = cherchenomprenom($idpersonne, $bdd);
		if($active_envoi_mail == "oui" && $info_destinataire_demande['autorise_mail'] == 1)
		{
			//Envoi d'un message au demand�
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

//Fonction permettant de v�rifier si une demande � devenir ami a d�j� �t� faite
function isset_demande_amis($idpersonne, $iddemandeur, $bdd)
{
	//On v�rifie si une demande n'a pas d�j� �t� post�e
	$liste_verification = liste_amis($idpersonne, $bdd, 0);
	
	//On v�rifie
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
	//Compl�ment de source pour les groupes
	$complement_source_groupes = "";
	foreach($liste_groupes as $ajouter)
	{
		$complement_source_groupes .= " OR (niveau_visibilite LIKE '%|".$ajouter."')";
		$complement_source_groupes .= " OR (niveau_visibilite LIKE '%|".$ajouter."|%')";
	}
	
	//Compl�ment de source pour les types de post
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
	
	//Ex�cution de la requete
	$textes = $bdd->prepare($sql);
	$textes->execute(array($niveau_visibilite, $niveau_visibilite."%"));
	
	//Affichage des r�sultats
	$retour = array();
	while($afficherresultats = $textes->fetch())
	{
		$retour[] = $afficherresultats;
	}
	
	//On renvoi les r�sultats
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
	
	//Si la requete est d'aimer, ajouter une entr�e dans la table aimer
	if($action == "J'aime")
	{
		//On v�rifie avant si la personne n'aime pas d�j�
		if($tour != 0)
		{
			//On affiche un message d'erreur: la personne aime d�j� le texte
			echo '<script type="text/javascript">alert("Vous aimez d�j� ce contenu! Il n\'est pas autoris� d\'aimer plusieurs fois un contenu.");</script>';
		}
		else
		{
			//Requete d'insertion
			$sql = "INSERT INTO aime (ID_type, ID_personne, Date_envoi, type) VALUES (?, ?, NOW(), ?)";
			$insertion = $bdd->prepare($sql);
			$insertion->execute(array($idtextelike, $idpersonne, $type));
		}
	}
	
	//Si la requete est de ne plus aimer, supprimer l'entr�e de la table
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
	
	//On renvoi le r�sultat
	return $retour;
	
	//Fermeture de la requete
	$requeteaime->closeCursor();
}

//Fonction permettant de supprimer tous les "aimes" d'un type pour un id
function delete_aimes_type_id($idcontent, $type, $bdd)
{
	//Ex�cution de la requ�te
	$sql = "DELETE FROM aime WHERE (ID_type = ?) && (type = ?)";
	$requete = $bdd->prepare($sql);
	$requete->execute(array($idcontent, $type));
}

//Fonction d'affichage du bouton j'aime pour un commentaire
function bouton_aime_commentaire($idcommentaire, $idtexte, $bdd)
{
	//Recherche du nombre de j'aime
	$nombre_aime = requeteaime($idcommentaire, $bdd, "commentaire");
	
	//D�veloppement uniquement
	//print_r($nombre_aime);
	
	echo "&nbsp;&nbsp;<img src='".path_img_asset()."aime";
	echo ($nombre_aime['vousaimez'] != 0 ? "" : "_vide");
	echo ".png'";
	
	// Uniquement si l'utilisateur est connect�
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
	//On r�cup�re les informations dans la base de donn�es
	$infos_commentaire = select_sql("commentaires", "ID = ?", $bdd, array($id));
	
	//On supprime les mentions "j'aime" du commentaire
	delete_aimes_type_id($id, "commentaire", $bdd);
	
	//On v�rifie si une image est associ�e au commentaire
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
	
	//Ex�cution de la suppression
	$sql = "DELETE FROM commentaires WHERE ID = ".$infos_commentaire[0]['ID'];
	
	//Ex�cution de la requete
	$suppcommentaire = $bdd->query($sql);
	
	//On redirige vers la meme page pour �viter les collisions
	$retour = "Suppression termin�e, redirection en cours....<br />";
	$retour .= '<meta http-equiv="refresh" content="0">';
	
	//On renvoie le r�sultat
	return $retour;
}

//Fonction d'affichage des commentaires
function affichecommentaire($idtexte, $bdd)
{
	//Affichage des commentaires
	$sql = 'SELECT * FROM commentaires WHERE ID_texte = '.$idtexte.' ORDER BY ID';
	
	//Ex�cution de la requete
	$commentaires = $bdd->query($sql);
	
	//Enregistrement des commentaires dans une variable
	$retour = array();
	while($affichercommentaires = $commentaires->fetch())
	{
		//On corrige la longueur des mots des commentaires
		$affichercommentaires['commentaire'] = corrige_longueur_mots_commentaires($affichercommentaires['commentaire']);
		
		$retour[] = $affichercommentaires;
	}
	
	//On renvoi le r�sultat
	return $retour;
	
	//On ferme la requete
	$commentaires->closeCursor();
}

//Fonction de suppression de texte
function deletetexte($idtexte, $texte, $bdd, $other_info = array())
{
	//!!! OBSOLETE -> DANGER SYSTEME !!! On v�rifie si il ne s'agit pas d'une image (ancienne -> ancien syst�me)
	/*$chaine = '/imgcomunicpostbyuser/';
	if(preg_match($chaine, $texte))
	{
		//On r�cup�re le nom de l'image pour la supprimer...
		$nomimg = str_replace('imgcomunicpostbyuser', '', $texte);
		$nomimg = strstr($nomimg, 'endof', true);
		
		//On supprime l'image
		unlink('imgpost/'.$nomimg.'.jpg');
	}*/
	
	//On v�rifie si il ne s'agit d'une image (plus r�cente -> nouveau syst�me)
	//On v�rifie si les informations ont �t� donn�es
	if(count($other_info) != 0)
	{
		//On v�rifie si il s'agit d'une image
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
	
	//On supprime le texte et les commentaires associ�s
	$sql1 = "DELETE FROM texte WHERE ID = ".$idtexte;
	$sql2 = "DELETE FROM commentaires WHERE ID_texte = ".$idtexte;
	$sql3 = "DELETE FROM aime WHERE ID_type = ".$idtexte." AND type = 'texte'";
	
	//Ex�cution de la requete
	$supptxt1 = $bdd->query($sql1);
	$supptxt2 = $bdd->query($sql2);
	$supptxt3 = $bdd->query($sql3);
}

//Fonction d'ajout de commentaire
function ajoutcommentaire($idpersonne, $idtexte, $commentaire, $bdd)
{
	//Pr�paration du contr�le de la pr�sence d'une image
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
					//Exemple de r�ponse
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
						//On r�cup�re les dimensions de l'image
						$width = $infos_image[0];
						$height = $infos_image[1];
							
						//On diminue les dimensions de l'image tant qu'elles sont sup�rieures � la limite
						while($width > 350 OR $height > 550)
						{
							$width = $width*0.9;
							$height = $height*0.9;
						}
							
						//On simplifie les valeurs trouv�es
						$width = floor($width);
						$height = floor($height);
						
						//On redimensionne l'image
						// Fichier et nouvelle taille
						$filename = $_FILES['image']['tmp_name'];
						
						// Cr�ation de la nouvelle image
						$thumb = imagecreatetruecolor($width, $height);
						
						if($infos_image['mime']=='image/jpeg') 
						{
							// Cr�ation des instances d'image (jpeg)
							$source = imagecreatefromjpeg($filename);
						}
						elseif($infos_image['mime']=='image/png') 
						{
							// Cr�ation des instances d'image (png)
							$source = imagecreatefrompng($filename);
						}
						elseif($infos_image['mime']=='image/gif') 
						{
							// Cr�ation des instances d'image (gif)
							$source = imagecreatefromgif($filename);
						}
						else
						{
							die("<p>Type d'image incompatible avec ce service.</p>");
						}
						
						// Redimensionnement
						imagecopyresized($thumb, $source, 0, 0, 0, 0, $width, $height, $infos_image[0], $infos_image[1]);
						
						//D�termination du nom de l'image
						$nom_img_commentaire = sha1(time().$_SESSION['ID'].sha1($commentaire).$_SERVER['REMOTE_ADDR']);
						
						//Checking user's folder path
						checkPersonnalFolder(relativeUserDataFolder("imgcommentaire/"), $_SESSION['ID']);

						//Check if an image with the same name already exists
						while(file_exists(relativeUserDataFolder("imgcommentaire/".$_SESSION['ID']."/".$nom_img_commentaire.".png")))
							$nom_img_commentaire = crypt($nom_img_commentaire);
						
						$nom_img_commentaire = "imgcommentaire/".$_SESSION['ID']."/".$nom_img_commentaire.".png";
						
						//Ecriture du fichier
						imagepng($thumb, relativeUserDataFolder($nom_img_commentaire));
						
						//On indique o� se trouve dans le fichier dans la bdd
						$image = "file:".$nom_img_commentaire;
					}
				}
			}
			else
				ob_end_clean();
				
		}
	}
		
	//Contr�le de la validit� du commentaire (n�cessite verifie_validite_ajout)
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
	//On v�rifie si il s'agit ou non du fil d'actualit�
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
		//D�termination du nom complet de la personne
		$infos_user['nom_complet'] = $infos_user['prenom']." ".$infos_user['nom'];
		return $infos_user;
	
}

//Fonction permettant de v�rifier si une page est publique ou non
function detectepagepublique($idpersonne, $bdd)
{
	//On v�rifie si la page est publique
	$sql = 'SELECT * FROM utilisateurs WHERE ID = ?';
	$requeteverification = $bdd->prepare($sql);
	$requeteverification->execute(array($idpersonne));
	
	//On pr�pare les variables pour l'�tapge suivante
	$autorisationspeciale = 0;
	
	//On v�rifie maintenant
	if($verifier = $requeteverification->fetch())
	{
			if(($verifier['public'] == 1) && ($verifier['pageouverte'] == 1))
			{
				//On donne alors une autorisation sp�ciale
				$autorisationspeciale = 1;
			}
		//Fermeture de la requete
		$requeteverification->closeCursor();
	}
	
	//On renvoi le r�sultat suivant si la page est publique ou non
	if($autorisationspeciale == 1)
	{
		return true;
	}
	else
	{
		return false;
	}
}

//Fonction qui permet de v�rifier si une personne est un ami ou non
function detectesilapersonneestamie($personneconnecte, $secondepersonne, $bdd)
{
	//On v�rifie si c'est un amis
	$sql = "SELECT * FROM amis WHERE (ID_personne = ?) && (ID_amis = ?) && (actif = 1)";
		
	//Ex�cution de la requete
	$requeteamis = $bdd->prepare($sql);
	$requeteamis->execute(array($personneconnecte, $secondepersonne));
	
	//On renvoi le r�sultat suivant si la personne est amie ou non
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
	//On v�rifie si il y a eu une erreur
	if($_FILES['image']['error'] == 0)
	{
		//On v�rifie si un dossier a �t� allou�e � la personne, cr�ation automatique le cas �ch�ant
		$folder_user = checkPersonnalFolder(relativeUserDataFolder("imgpost/"), $_SESSION['ID']);
		$folder_user = str_replace(relativeUserDataFolder(), "", $folder_user);

		//On recherche le nom de l'image
		$imagePath = $folder_user.(isset($_SERVER['UNIQUE_ID']) ? $_SERVER['UNIQUE_ID'] : sha1($_SERVER['HTTP_USER_AGENT']))
		.$_SESSION['ID'].$_SERVER['REQUEST_TIME'].".jpg"; //Nom introuvable
		
		// On peut valider l'image de fond et la stocker d�finitivement
		if(move_uploaded_file($_FILES['image']['tmp_name'], relativeUserDataFolder($imagePath)))
		{
		
			//Pr�paration de la requete
			$contenu = $nomimage;
			
			//On enregistre l'image dans la base
			$sql = 'INSERT INTO texte (ID_personne, date_envoi, texte, ID_amis, niveau_visibilite, type, size, file_type, path) VALUES ('.$idpersonne.', NOW(), ?, ?, ?, ?, ?, ?, ?) ';
				
			//Ex�cution de la requete
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

//Fonction de v�rification de l'existence d'une demande de nouveaux amis
function issetdemandesamis($idpersonne, $bdd)
{
	//On v�rifie si il y a des demandes d'amis non accept�s
	$sql = "SELECT * FROM amis WHERE (ID_personne = ".$idpersonne.") && (actif = 0)";
	
	//Ex�cution de la requete
	$verificationnouveauxamis = $bdd->query($sql);
	
	//Calcul du r�sultat
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

//Fonction de correction des caract�res sp�ciaux
function corrige_caracteres_speciaux($source)
{
	//On corrige les caract�res sp�ciaux
	$source = str_replace("�", "&egrave;", $source);
	$source = str_replace("�", "&eacute;", $source);
	$source = str_replace("�", "&ecirc;", $source);
	$source = str_replace("�", "&euml;", $source);
	$source = str_replace("�", "&icirc;", $source);
	$source = str_replace("�", "&agrave;", $source);
	$source = str_replace('�', '&ccedil;', $source);
	$source = str_replace('�', '&ugrave;', $source);
	$source = str_replace('�', '&acirc;', $source);
	$source = str_replace('�', '&ocirc;', $source);
	$source = str_replace('�', '&ucirc;', $source);
	$source = str_replace('�', '&rsquo;', $source);
	
	//On renvoi le r�sultat
	return $source;
}

//Fonction de d�correction des caract�res sp�ciaux
function decorrige_caracteres_speciaux($source)
{
	//On corrige les caract�res sp�ciaux
	$source = str_replace("&egrave;", "�",$source);
	$source = str_replace("&eacute;", "e", $source);
	$source = str_replace("&ecirc;", "�", $source);
	$source = str_replace("&euml;", "�", $source);
	$source = str_replace("&icirc;", "�", $source);
	$source = str_replace("&agrave;", "�", $source);
	$source = str_replace('&ccedil;', '�', $source);
	$source = str_replace('&ugrave;', '�', $source);
	$source = str_replace('&acirc;', '�', $source);
	$source = str_replace('&ocirc;', '�', $source);
	$source = str_replace('&ucirc;', '�', $source);
	$source = str_replace('&rsquo;', '�', $source);
	
	//On renvoi le r�sultat
	return $source;
}

//Fonction d'enregistrement de post de chat
function postchat($idpersonne, $message, $bdd)
{
	//On enregistre le chat
	$query = $bdd->prepare("INSERT INTO chat(message, ID_personne, Date_envoi) VALUES(?, ?, NOW())");
	$query->execute(array($message, $idpersonne));
}

//Fonction de r�cup�ration du contenu du chat
function recuperecontenuchat($bdd, $nbpost = 10)
{
	$sql = "SELECT * FROM chat ORDER BY ID DESC LIMIT 0,".$nbpost;
	$requetechat = $bdd->query($sql);
	
	//Enregistrement des r�sultats
	$retour = array();
	while($afficherchat = $requetechat->fetch())
	{
		$retour[] = $afficherchat;
	}
	
	//Renvoi du r�sultat
	return $retour;
}

//Fonction permettant de v�rifier si le chat doit �tre automatiquement ouvert
function verifierouvertureautomatiquechat($idpersonne, $bdd)
{
	//On v�rifie si le chat doit �tre automatiquement ouvert
	$sql = "SELECT * FROM utilisateurs WHERE ID = ".$idpersonne;
	
	//Ex�cution de la requete
	$verifierchat = $bdd->query($sql);
	
	//On v�rifie et on affiche la source javascript en fonction des parametres
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
	
	//Pr�paration de la requete
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
		
		//On v�rifie si il faut retourner les informations de l'utilisateur
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

//Fonction permettant de voir si l'utilisateur est connect�
function verifieconnnexionutilisateur()
{
	//On cache les erreurs
	ob_start();
	//session_start();
	ob_end_clean();
	
	//Si l'utilisateur est connect�, on renvoi true sinon on renvoi false
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
	
	//On retourne le r�slutat
	return $texte;
}

//Fonction de recherche de personnes
function searchuser($nom, $bdd, $limite = 10)
{
	//Mettons "" � la place de "%"
	$nom = str_replace("%", "", $nom);
	
	//Continuons de prot�ger les personnes et la table
	$nom = str_replace("'", '"', $nom);
	
	//Rendons flexible la requete
	$nom = str_replace(' ', '%', $nom);
	$nom = "%".$nom."%";
	
	//Nous pouvons maintenant faire la recherche
	//Requ�te SQL
	$sql = "SELECT * FROM utilisateurs WHERE (nom LIKE ?) || (prenom LIKE ?) || (CONCAT(prenom, '%', nom) LIKE ? ) || (CONCAT(prenom, ' ', nom) LIKE ? ) || (mail LIKE ?) ORDER BY prenom LIMIT ".$limite;
	
	//Ex�cution de la requete SQL
	$recherche = $bdd->prepare($sql);
	$recherche->execute(array($nom, $nom, $nom, $nom, $nom));
	
	//Enregistrement des r�sultats
	$retour = array();
	while($afficherrecherche = $recherche->fetch())
	{
		$retour[] = $afficherrecherche;
	}
	
	//Fermeture de la requete
	$recherche->closeCursor();
	
	//On retourne le r�sultat
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
		//Si on recherche un message pr�cis, on adapte la requ�te
		$sql = 'SELECT * FROM messagerie WHERE ID_destinataire = ? && ID = ?';
		$requete = $bdd->prepare($sql);
		$requete->execute(array($idpersonne, $id));
	}
	
	//On pr�pare l'enregistrement des r�sultats
	$retour = array('lu'=>array(), 'nonlu'=>array());
	
	//On enregistre les r�sultats
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
	
	//On retourne les r�sultats
	return $retour;
	
	//On ferme la requete
	$requete->closeCursor();
}

//Fonction de r�cup�ration de la liste des pages publiques
function get_page_publique($bdd){
	//On r�cup�re la liste de toute les pages publiques
	$sql = "SELECT * FROM utilisateurs WHERE pageouverte = 1 ORDER BY prenom";
	
	//Ex�cution de la requete
	$requete = $bdd->query($sql);
	
	//On enregistre les r�sultats
	$liste = array();
	
	while($enregistre = $requete->fetch())
	{
		$liste[] = $enregistre;
	}
	
	//On renvoi le r�sultat
	return($liste);
	
	//On ferme la requ�te
	$requete->closeCursor();
}

//Fonction permettant l'affichage des smiley dans les textes:
function affiche_smile($source, $urlsite = "./", $liste_smiley = false)
{
	//Inclusion de la liste (si n�cessaire
	if(!$liste_smiley)
		include('inc/liste_smile.php');
		
	//Traitement de la liste
	foreach($liste_smiley as $afficher)
	{
		//On affiche les smiley
		$source = str_ireplace($afficher[0], ' <img src="'.$urlsite.$afficher[1].'" title="'.$afficher[2].'" /> ', $source);
	}
	
	
	//On renvoi le r�sultat
	return($source);
}

//Fonction de protection contre le code source
function bloquebalise($source, $type="tout")
{
	//On prot�ge la source
	$source = str_replace('<', '&lt;', $source);
	$source = str_replace('>', '&gt;', $source);
	
	//Fonction s�pciale r�serv�e aux commentaires
	if($type == "commentaire")
	{
		//Anulation de la correction pour <span class='corrige_longueur_mots'></span> par <span></span>
		$source = str_replace("&lt;span class='corrige_longueur_mots'&gt;&lt;/span&gt;", "<span></span>", $source);
	}
	
	//On renvoi le r�sultat
	return($source);
}

//Fonction qui d�finit et inclus le bon fichier de langue
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
			//Sinon on inclus la langue fran�aise
			include('lang/fr.php');
		}
	}
	else
	{
		//On d�tecte la langue
		if(isset($_SERVER['HTTP_ACCEPT_LANGUAGE']))
		{
			if(preg_match('/fr/', $_SERVER['HTTP_ACCEPT_LANGUAGE']))
			{
				//On d�finit le cookie pour la France
				setcookie('langue', 'fr', time() + 365*24*3600);
				
				//Et on charge la lague francaise
				include('lang/fr.php');
			}
			else
			{
				//Langue par d�faut: Anglais
				setcookie('langue', 'en');
				
				//Et on charge la langue anglaise
				include('lang/en.php');
			}
		}
		else
		{
			//Langue par d�faut: Anglais
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
	//On v�rifie que la langue est correcte
	if(str_replace('.', '', $langue) != $langue)
		die("Erreur"); //S�curit�
	
	//On d�finit la langue de l'utilisateur
	setcookie('langue', $langue, time()+60*60*24*30);
}

//Fonction qui permet l'�chappement des guillemets ' en HTML
function echap_guillemet_html($source)
{
	//On �chappe les guillemetes
	$source = str_replace("'", "&prime;", $source);
	
	//On renvoi le r�sultat
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
	
	//On renvoi le r�sultat
	return($source);
}

//Fonction de protection de la cr�ation de compte
function trouve_caractere_tableau($tableau_a_verifier, $caractere = '<')
{	
	//On annonce que l'on a pour le moment il n'y a pas de probl�me de s�curit�
	$probleme_securite = false;
	
	//On analyse les variables
	foreach ($tableau_a_verifier as $tester)
	{
		if(preg_match('/'.$caractere.'/', $tester))
		{
			//On enregistre qu'il y a un probl�me de s�curit�s
			$probleme_securite = true;
		}
	}
	
	//On renvoi le r�sultat
	return $probleme_securite;
}

//Fonction qui permet de lister l'ensemble des amis d'une personne
// $id: identifiant de la personne
//$bdd: Connexion � la base de donn�e (PDO)
//$actif: indique si l'ami doit �tre actif ou non
function liste_amis($id, $bdd, $actif = 1)
{
	//On pr�pare le tableau final
	$listeamis = array();
	
	//Requete de recherche d'amis
	$sql = "SELECT * FROM amis WHERE ID_personne = ".$id." && actif = ".$actif;
		
	//Ex�cution de la requete
	$requeteamis = $bdd->query($sql);
	
	//Enregistrement des r�sultats
	while($enregistrer = $requeteamis->fetch())
	{
		//On enregistre l'amis
		$listeamis[] = $enregistrer['ID_amis'];
	}
	
	//Fermeture de la base de donn�es
	$requeteamis->closeCursor();
	
	//On renvoi le r�sultat
	return $listeamis;
}

//Fonction d'adaptation des textes pour les images
function adapte_texte_image($source, $pathimage = "./") 
{
	$source = str_replace('endofimgcomunicpostbyuser', '.jpg" />', $source);
	$source = str_replace('imgcomunicpostbyuser', '<img height="200" src="user_data/imgpost/', $source); 
	$source = str_replace('endofnameofimg', '</center>', $source);
	$source = str_replace('nameofimg', '<center>', $source);
	
	//On renvoi le r�sultat
	return $source;
}

//Fonction de correction des �chappements
function corrige_echapement($source)
{
	//On corrige les �chappements
	$source = str_replace("\'", "'", $source);
	$source = str_replace('\"', '"', $source);

	//On renvoi le r�sultat
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
	//On v�rifie si il s'agit d'un rafra�chissement ou non
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
	//On v�rifie si la liste est d�j� fournie
	if(!$list_friend)
		//We search the friend of the personn
		$list_friend = liste_amis($id, $bdd);
	
	//We insert in notification the message
	foreach($list_friend as $insert_friend)
	{
		//On v�rifie d�j� que les deux personnes sont amies
		$ok = true;
		if($idamis != "")
		{
			if(!detectesilapersonneestamie($idamis, $insert_friend, $bdd))
			{
				//On arr�te l'envoi 
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
			//On v�rifie que la personne est abonn�e aux notifications de l'utilisateur
			if(personne_abonnee_notification_ami($insert_friend, $id, $bdd))
			{
				//On v�rifie si une notification similaire existe d�j�
				$sql = "SELECT COUNT(*) AS nb_notifications FROM notification WHERE ID_personne = ? AND adresse = ? AND type = ? AND vu = 0";
				$requete = $bdd->prepare($sql);
				$requete->execute(array($insert_friend, $adresse, $type));
				
				//R�cup�ration des informations
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
				
				//Fermeture de la requ�te
				$requete->closeCursor();
			}
		}
	}
}

//Similaire mais pour un seul utilisateur
function sendnotification_one_user($id, $iddestination, $message, $bdd, $adresse="")
{
	//Pr�partion de l'ex�cution de la fonction
	$retour = false;
	
	//On v�rifie si une notification similaire existe d�j�
	$sql = "SELECT COUNT(*) AS nb_notifications FROM notification WHERE ID_personne = ? AND ID_createur = ? AND message = ? AND adresse = ? AND vu = 0";
	$requete = $bdd->prepare($sql);
	$requete->execute(array($iddestination, $id, $message, $adresse));
	
	//R�cup�ration des informations
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
	
	//Fermeture de la requ�te
	$requete->closeCursor();
	
	//Renvoi du r�sultat
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
			
			//Ex�cution de la requ�te
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
	
	//Ex�cution de la requ�te
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
	
	//Ex�cution de la requete
	$requete = $bdd->prepare($sql);
	$requete->execute(array($id));
	
	//Enregistrement des r�sultats
	$info = $requete->fetch();
	
	//Fermeture de la requete
	$requete->closeCursor();
	
	//On renvoi le r�sultat
	return $info;
}

//Fonction de recherche d'informations sur les r�ponses d'un sujet
function info_reponses($id, $bdd)
{
	//Requete de recherche des informations sur le sujet
	$sql = 'SELECT * FROM forum_reponse WHERE ID_sujet = ?';
	
	//Ex�cution de la requete
	$requete = $bdd->prepare($sql);
	$requete->execute(array($id));
	
	//Enregistrement des r�sultats
	$info = array();
	while($enregistrer = $requete->fetch())
	{
		$info[] = $enregistrer;
	}
	
	//Fermeture de la requete
	$requete->closeCursor();
	
	//On renvoi le r�sultat
	return $info;
}

//D�fini un message en vu
function metslemessageenvu($idmessage, $bdd)
{
	//On d�fini un message en tant que vu
	$sql = "UPDATE messagerie SET lu = 1 WHERE ID = ".$idmessage;
	
	//Ex�cution de la requ�te
	$update = $bdd->query($sql);
}

//Suppression d'un message depuis son ID
function suppmessagefromid($id_personne, $id_message, $bdd)
{
	//Requ�te de suppression d'un message
	$sql = "DELETE FROM messagerie WHERE ID = ? AND ID_destinataire = ?";
	
	//Ex�cution de la requ�te
	$requete = $bdd->prepare($sql);
	$requete->execute(array($id_message, $id_personne));
}

//Enregistrement de l'activit� de l'utilisateur
function update_last_activity($id, $bdd, $time = "undefined")
{
	//On enregistre time() si n�cessaire
	if($time == "undefined")
	{
		$time = time();
	}
	
	//On met � jour la derni�re activit� de l'utilisateur
	$sql = "UPDATE utilisateurs SET last_activity = ? WHERE ID = ?";
	
	//Ex�cution de la requ�te
	$requete = $bdd->prepare($sql);
	$requete->execute(array($time, $id));
}

//Fonction qui permet de retirer la corrections des caract�res sp�ciaux
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
	
	//On renvoi le r�slutat
	return($source);
}

//Fonction de correction des erreurs d'accents d�es aux scripts javascript
function corrige_accent_javascript($source)
{
	//On corrige les erreures javascript
	$source = str_replace("é", "�", $source);
	$source = str_replace("è", "�", $source);
	$source = str_replace("ê", "�", $source);
	$source = str_replace("�", "�", $source);
	$source = str_replace("�", "�", $source);
	$source = str_replace("ù", "�", $source);
	$source = str_replace("�", "�", $source);
	$source = str_replace("’", "�", $source);
	$source = str_replace("�", "�", $source);
	$source = str_replace("�", "�", $source);
	//$source = str_replace("�", "�", $source);
	$source = str_replace("€", "�", $source);
	
	//On renvoi le r�sultat
	return($source);
}

//Fonction de retirement de correction des erreurs d'accents d�es aux scripts javascript
function decorrige_accent_javascript($source)
{
	//On retire les corrections javascript
	$source = str_replace("�", "é", $source);
	$source = str_replace("�", "è", $source);
	$source = str_replace("�", "ê", $source);
	$source = str_replace("�", "�", $source);
	$source = str_replace("�", "�", $source);
	$source = str_replace("�", "ù", $source);
	//$source = str_replace("�", "�", $source);
	//$source = str_replace("�", "�", $source);
	//$source = str_replace("�", "�", $source);
	$source = str_replace("�", "€", $source);
	
	//On renvoi le r�sultat
	return($source);
}

//Fonction d�terminant si une notification existe
function notification_exists($id_createur, $id_destination, $message, $adresse, $bdd)
{
	//Pr�partion de l'ex�cution de la fonction
	$retour = false;
	
	//On v�rifie si une notification similaire existe d�j�
	$sql = "SELECT COUNT(*) AS nb_notifications FROM notification WHERE ID_personne = ? AND ID_createur = ? AND message = ? AND adresse = ? AND vu = 0";
	$requete = $bdd->prepare($sql);
	$requete->execute(array($id_destination, $id_createur, $message, $adresse));
	
	//R�cup�ration des informations
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
		//On consid�re qu'il n'y en a pas
		$retour = false;
	
	//Fermeture de la requ�te
	$requete->closeCursor();
	
	//Renvoi du r�sultat
	return $retour;
}

//Fonction mettant en 'vu' le chat d'une personne
function mettre_en_vu_private_chat($id, $id_emetteur, $bdd)
{
	//Mise � jour de la base de donn�es
	$sql = "UPDATE chatprive SET vu = 1 WHERE ID_destination = ? AND ID_personne = ?";
	
	//Ex�cution de la requ�te
	$requete = $bdd->prepare($sql);
	$requete->execute(array($id, $id_emetteur));
	
	//On supprime les notifications relatives au chat priv� pour cette personne
	$sql = "DELETE FROM notification WHERE ID_personne = ? AND ID_createur = ? AND message = ?";
	$suppression = $bdd->prepare($sql);
	$suppression->execute(array($_SESSION['ID'], $id_emetteur, "vous a envoy&eacute; un message dans le chat priv&eacute;."));
}

//Fonction permettant de v�rifier si un post de chat priv� d'une personne n'a pas �t� vu
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
	
	//Fermeture de la requ�te
	$get_content->closeCursor();
}

//Fonction de v�rification de l'existence de nouveaux messages pour l'utilisateur
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
	
	//Fermeture de la requ�te
	$check_content->closeCursor();
}

//Fonction permettant de r�cup�rer un extrait de source ayant un commencement et une fin
function getsourceprecise($source, $debut, $fin = '\n')
{
	preg_match_all('#'.$debut.'(.*?)'.$fin.'#is', $source, $resultat, PREG_PATTERN_ORDER);
	
	//On renvoi le r�sultat
	return $resultat[0];
}

//Fonction d'affichage de lien
function afficher_lien($source)
{
	//On pr�pare l'ajout des liens
	$source = " ".$source." ";
	$source = str_replace("<", " <", $source);
	$listetype = array("http://", "ftp://", "https://");
	
	foreach($listetype as $debut)
	{
		//On r�cup�re la liste des liens
		$liste = getsourceprecise($source, " ".$debut, " ");
		
		//On modifie les liens
		foreach($liste as $modifier)
		{
			//On modifie les liens avec un str_replace
			$source = str_replace($modifier, "<a href='".$modifier."' title='Ouvrir la page' target='_blank'>".$modifier."</a>", $source);
		}
	}
	
	//On renvoi le r�sultat
	return $source;
}

//Fonction d'affichage de la source n�cessaire pour ajouter des smiles dans les commentaires
function source_ajout_smiley($id_texte)
{
	echo "<img src='".path_img_asset('smiley/smile_gris.gif')."' class='bouton_ajout_smiley' onClick='affiche_liste_smile(\"addcommentaire".$id_texte."\");' />";
}

//Fonction permettant de d�finir si une personne est connect�e ou non
function determine_si_personne_connecte($id, $bdd)
{
	//R�cup�ration des informations de la personne
	$informations = cherchenomprenom($id, $bdd);
	
	//R�cup�ration du temps actuel
	$time = time();
	$time = $time-35;
	
	//Renvoi du r�sultat
	return ($time > $informations['last_activity'] ? false : true);
}

//Fonction de correction de la longueur mots des commentaires
function corrige_longueur_mots_commentaires($source)
{
	//S�paration de tous les mots
	//$liste_mots = getsourceprecise($source, " ", " "); //Non fiable
	
	//Insertion de retours � la ligne automatiques
	$source = wordwrap($source, 100, " ", true);

	//Renvoi du r�sultat
	return($source);
}

//Fonction de r�cup�ration de la liste de toute les pages
function get_liste_pages($id_personne, $bdd)
{
	//Requ�te de recherche
	$sql = "SELECT * FROM pages WHERE ID_personne = ?";
	
	//Ex�cution de la requ�te
	$requete = $bdd->prepare($sql);
	$requete->execute(array($id_personne));
	
	//Pr�paration de l'enregistrement
	$enregistrement = array();
	
	//Enregistrement des r�sultats
	while($enregistrer = $requete->fetch())
	{
		//Enregistrement de la page
		$enregistrement[] = $enregistrer;
	}
	
	//Fermeture de la requete
	$requete->closeCursor();
	
	//Renvoi du r�sultat
	return $enregistrement;
}

//Fonction permettant de v�rifier la validit� d'un post ou d'un texte
function verifie_validite_ajout($source, $exception = false)
{
	//On enl�ve les balises de paragraphe
	$source = str_ireplace('<p>', "", $source);
	$source = str_ireplace('</p>', "", $source);
	
	//On enl�ve les balises de mise en page
	$source = str_ireplace('<i>', "", $source);
	$source = str_ireplace('</i>', "", $source);
	$source = str_ireplace('<strong>', "", $source);
	$source = str_ireplace('</strong>', "", $source);
	$source = str_ireplace('<b>', "", $source);
	$source = str_ireplace('</b>', "", $source);
	$source = str_ireplace('<s>', "", $source);
	$source = str_ireplace('</s>', "", $source);
	$source = str_ireplace('</a>', "", $source);
	
	//On compte le nombre de caract�res diff�rents
	$tableau_caracteres = count_chars($source, 3);
	
	//V�rification du nombre
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
	
	//Il y a deux exceptions (si autoris�es)
	if($exception)
	{
		$valide = ($source == ":)" || $source == ":(" ? true : $valide);
	}
	
	
	//Retour du r�sultat
	return $valide;
}

//Fonction affichant le formulaire permettant choisir quel est le niveau de visibilit� du contenu
function choisir_niveau_visibilite($niveau = 1)
{
	//Il y a trois niveaux :
	// 1 - Tout le monde (disponible uniquement si la page est ouverte)
	// 2 - Uniquement les amis
	// 3 - Uniquement la personne ayant post� le texte (et si il s'agit d'une page amie, le propri�taire de la page �galement)
	
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

//Fonction affichant le menu permettant de changer le niveau de visibilit�
function affiche_menu_change_niveau_visibilite($niveau_actuel, $id, $peut_modifier_niveau = false)
{
	//On �tablit l'ID du menu de changement du niveau de visibilit�
	$id_menu_changement_niveau_visibilite = "change_niveau_visibilite_".$id;
	
	//On commence par afficher (syst�matiquement) le niveau actuel
	echo "<img id='".$id_menu_changement_niveau_visibilite."_img'";
	if($niveau_actuel == "1")
		echo " src='".path_img_asset('users_5.png')."' title='Visible par tous le monde' />";
	elseif($niveau_actuel == "2")
		echo " src='".path_img_asset('users_3.png')."' title='Visible par moi et mes amis' />";
	elseif($niveau_actuel == "3")
		echo " src='".path_img_asset('user.png')."' title='Visible par moi uniquement' />"; 
	else
		echo " src='".path_img_asset('cog.png')."' title='Personnalis&eacute;' />";
		
	//Si l'utilisateur est autoris�, on lui affiche le menu de modification
	if($peut_modifier_niveau)
	{
		//On met une fl�che pour proposer � l'utilisateur de changer le niveau de visibilit�
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
	//S�curit�
	$x1 = $x1*1;
	$x2 = $x2*1;
	$y1 = $y1*1;
	$y2 = $y2*1;

	//R�cup�ration des inofrmations initiales sur l'image
	$info_image = getimagesize($adresse_source_image);
				
	if($info_image['mime']=='image/jpeg') 
	{
		// Cr�ation des instances d'image (jpeg)
		$src = imagecreatefromjpeg($adresse_source_image);
	}
	elseif($info_image['mime']=='image/png') 
	{
		// Cr�ation des instances d'image (png)
		$src = imagecreatefrompng($adresse_source_image);
	}
	elseif($info_image['mime']=='image/gif') 
	{
		// Cr�ation des instances d'image (gif)
		$src = imagecreatefromgif($adresse_source_image);
	}
	else
	{
		die("<p>Type d'image incompatible avec ce service.</p>");
	}
	
	//Cr�ation de l'image de destination
	$dest = imagecreatetruecolor($width, $height);

	// Copie (varie en fonction de la n�cessit� de redimensionner ou pas l'image)
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
	
	// Affichage et lib�ration de la m�moire
	//header('Content-Type: image/gif');
	imagejpeg($dest, $adresse_destination_image);

	imagedestroy($dest);
	imagedestroy($src);
}

//Fonction permettant d'�tablir si un r�pertoire d�signe un utilisateur de Comunic
function folder_is_an_user($repertoire, $bdd)
{
	//Requ�te de recherche
	$sql = "SELECT * FROM utilisateurs WHERE sous_repertoire = ?";
	$requete = $bdd->prepare($sql);
	$requete->execute(array($repertoire));
	
	//Analyse
	if($analyser = $requete->fetch())
		return $analyser; //Le r�sultat est positif
	else
		return false; //Le r�sultat est n�gatif
		
	//Fermeture de la requ�te
	$requete->closeCursor();
}

//Fonction permettant de v�rifier si un r�pertoire est libre, valide et autoris�
function verifie_validite_sous_repertoire($nom, $bdd)
{
	$valide = true;
	
	//Liste des caract�res incorrects
	$list_invalid_chars = array(
		"/",
		"&",
		"?",
		"^",
		"\\",
		"$",
		"�",
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
	
	//On commence par v�rifier qu'il ne poss�de pas de caract�res comprommettants et qu'il n'est pas vide
	$valide = ($nom == "" ? false : $valide);
	foreach($list_invalid_chars as $verifier)
		$valide = (str_replace($verifier, "", $nom) != $nom ? false : $valide);
	
	//On v�rifie maintenant si il n'est pas sur la liste noire
	foreach($black_list as $verifier)
		$valide = ($nom == $verifier ? false : $valide);
		
	//On v�rifie si il est disponible
	$valide = (folder_is_an_user($nom, $bdd) ? false : $valide);
	
	//On renvoi le r�sultat
	return $valide;
	
}

//Fonctions de vid�o
//Fonction permettant de lister l'ensemble des vid�os de l'utilisateur
function liste_videos_user($id, $bdd)
{
	$sql = "SELECT * FROM galerie_video WHERE ID_user = ?";
	
	//Ex�cution de la requ�te
	$requete = $bdd->prepare($sql);
	$requete->execute(array($id));
	
	//Enregistrement des r�sulats
	$liste = array();
	while($enregistrer = $requete->fetch())
	{
		$liste[] = $enregistrer;
	}
	
	//Fermeture de la requ�te
	$requete->closeCursor();
	
	//Renvoi du r�sultat
	return $liste;
}

//Fonction d'affichage de vid�o (ancienne version) ! La nouvelle version n'�tant pas fonctionelle, on utilise celle-l� � la place !
function affiche_video($liste_url, $preload="metadata", $width="640", $height="264", $image_par_defaut="none", $controls = "controls", $autoload_videojs = true, $id_video = false)
{
	?><video id="<?php echo (!$id_video ? sha1($width.$height.$liste_url[0][0]) : $id_video); ?>" class="video-js vjs-default-skin" <?php echo $controls; ?> preload="<?php echo $preload; ?>" width="<?php echo $width; ?>" height="<?php echo $height; ?>" <?php 
		   if($image_par_defaut != "none") echo ' poster="'.$image_par_defaut.'"'; //Image par d�faut
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
	$valeur = round($valeur, 2); //Arrondis de la valeur � deux chiffres apr�s la virgule

	//Renvoi du r�sultat
	return $valeur;
}

//Fonction renvoyant l'ID de la vid�o la plus r�cente de l'utilisateur
function id_video_plus_recente($id, $bdd)
{
	//Requ�te de recherche de la vid�o la plus r�cente
	$sql = "SELECT * FROM galerie_video WHERE ID_user = ? ORDER BY ID DESC LIMIT 1";
	
	//Ex�cution de la requ�te
	$requete = $bdd->prepare($sql);
	$requete->execute(array($id));
	
	//V�rification et envoi du r�sultat
	if($afficher = $requete->fetch())
	{
		//Fermeture du curseur
		$requete->closeCursor();
		
		//Renvoi du r�sultat
		return $afficher['ID'];
	}
	
	//Fermeture du curseur
	$requete->closeCursor();
	
	//Message d'erreur
	die("Il n'y a pas de vid&eacute;o disponible pour cet utilisateur. <a href='index.php'>Retour</a>");
}

//Fonction permettant de v�rifier l'existence d'une vid�o
function isset_video($id_video, $id_user, $bdd)
{
	//Requ�te de recherche de la vid�o
	$sql = "SELECT * FROM galerie_video WHERE ID = ? && ID_user = ?";
	
	//Ex�cution de la requ�te
	$requete = $bdd->prepare($sql);
	$requete->execute(array($id_video*1, $id_user*1)); //Inclusion de s�curit�
	
	//V�rification et envoi du r�sultat
	if($afficher = $requete->fetch())
	{
		//Fermeture du curseur
		$requete->closeCursor();
		
		//Renvoi du r�sultat
		return true;
	}
	
	//Fermeture du curseur
	$requete->closeCursor();
	
	//Retour n�gatif
	return false;	
}

//Fonction d'ajout de vid�o
function add_movie($idpersonne, $texte, $idvideo, $bdd, $niveau_visibilite = 2, $amis = 0)
{
	$sql = 'INSERT INTO texte (ID_personne, date_envoi, idvideo, type, ID_amis, texte, niveau_visibilite) VALUES (?, NOW(), ?, ?, ?, ?, ?) ';
	
	//Ex�cution de la requete
	$insertion = $bdd->prepare($sql);
	$insertion->execute(array($idpersonne, $idvideo, "video", $amis, corrige_echapement($texte), $niveau_visibilite));
	
	//Et la notification (uniquement si n�cessaire => Si le post n'a pas un niveau �gal � 3)
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

//Fonction de r�cup�ration des informations d'une vid�o
function get_info_video($id, $bdd)
{
	//Requ�te de recherche
	$sql = "SELECT * FROM galerie_video WHERE ID = ?";
	
	//Ex�cution de la requ�te
	$requete = $bdd->prepare($sql);
	$requete->execute(array($id*1)); //S�curit� impl�ment�e
	
	//R�cup�ration du r�sultat
	$retour = $requete->fetch();
	
	//Fermeture de la requ�te
	$requete->closeCursor();
	
	//Renvoi du r�sultat
	return $retour;
}

//Fonction permettant de lister l'ensemble des posts relatifs � une vid�o
function list_ensemble_posts_relatif_a_video($id, $bdd)
{
	//Requ�te de recherche
	$sql = "SELECT * FROM texte WHERE idvideo = ?";
	
	//Ex�cution de la requete
	$requete = $bdd->prepare($sql);
	$requete->execute(array($id));
	
	//Enregistrement des r�sultats
	$liste = array();
	while($enregistrer = $requete->fetch())
	{
		$liste[] = $enregistrer;
	}
	
	//Fermeture de la requ�te
	$requete->closeCursor();
	
	//Renvoi du r�sultat
	return $liste;
}

//Fonction permettant de v�rifier si un compte existe
function isset_account($id, $bdd)
{
	//Requ�te de recherche
	$sql = "SELECT * FROM utilisateurs WHERE ID = ?";
	$requete = $bdd->prepare($sql);
	$requete->execute(array($id*1)); //S�curit� incorpor�e
	
	//V�rification
	if($verifier = $requete->fetch())
		$retour = true;
	else
		$retour = false;
		
	//Fermeture de la requ�te
	$requete->closeCursor();
	
	//Renvoi du r�sultat
	return $retour;
}

//Fonction permettant de r�cup�rer la liste des types de contact
function get_list_type_contact($bdd)
{
	//Requ�te de recherche
	$sql = "SELECT * FROM sujet_contact";
	$requete = $bdd->query($sql);
	
	//Enregistrement des r�sultats
	$liste = array();
	while($enregistrer_type_contact = $requete->fetch())
	{
		$liste[] = $enregistrer_type_contact;
	}
	
	//Fermeture de la requ�te
	$requete->closeCursor();
	
	//Renvoi du r�sultat
	return $liste;
}

//Fonction de cryptage de mot de passe selon la strat�gie de mot de passe utilisateur de Comunic
function crypt_password($password)
{
	//Cryptage du mot de passe et renvoi du r�sultat
	$password = sha1($password);
	return crypt($password, $password); 
}

//Fonction permettant d'ajouter l'URL du site sur un block de texte
function add_url_site($source, $urlsite)
{
	//Conversion et renvoi du r�sultat
	return str_replace("%URL_SITE%", $urlsite, $source);
}

//Transforme une date issue d'un datepicker en date normalis�e pour la base de donn�es
function normalise_datepicker($date)
{
	//Transformation de la date en tableau
	$array_date = explode(".", $date);
	
	//V�rification de s�curit�
	if(count($array_date) != 3)
		die("Date incorrecte!");
			
	return $array_date[2]."-".$array_date[1]."-".$array_date[0]." 00:00:00";
}

//Fonction permettant de d�terminer si une personne est abonn�e aux notifications d'une autre
function personne_abonnee_notification_ami($ami, $personne_connectee, $bdd)
{
	//On r�cup�re les informations sur la table ami
	$sql = "SELECT * FROM amis WHERE (ID_personne = ?) && (ID_amis = ?) && (actif = 1)";
	$requeteamis = $bdd->prepare($sql);
	$requeteamis->execute(array($ami, $personne_connectee));
	
	//On renvoi le r�sultat suivant si la personne est amie ou non
	if(!$info_ami = $requeteamis->fetch())
	{
		//Fermeture de la requete
		$requeteamis->closeCursor();
		
		//La personne ne peut pas �tre abonn�e: elle n'est pas amie
		return false;
	}
	
	//Fermeture de la requete
	$requeteamis->closeCursor();
	
	//On d�finit maintenant si la personne est amie ou pas
	return ($info_ami['abonnement'] == 0 ? false : true);
}

//Fonction d'enregistrement d'une image encod�e en base64 vers un fichier
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
	//R�cup�ration des informations sur l'utilisateur
	$info_user = cherchenomprenom($id, $bdd);
	
	//On v�rifie que le nettoyage automatique des anciennes notifications est activ�
	if($info_user['nettoyage_automatique_notifications'] == 1)
	{
		//On cr�e la date avant laquelle les notifications seront supprim�es
		$array_date = getdate();
		
		//On r�cup�re la date en seconde en dessous de laquelle les notifications seront supprim�es
		$time = strtotime("-".$info_user['mois_nettoyage_automatique_notifications']." month -".$info_user['jour_nettoyage_automatique_notifications']." days -".$info_user['heure_nettoyage_automatique_notifications']." hours");
		
		//On convertit la date en format normalis�
		$date = date("Y-m-d H:i:s", $time);
		
		//On supprime les notifications ant�rieurs � cette date MAINTENANT
		$sql = "DELETE FROM notification WHERE date_envoi <= ? AND ID_personne = ?";
		
		//Ex�cution de la requ�te
		$requete = $bdd->prepare($sql);
		$requete->execute(array($date, $id));
	}
}

//Fonction retournant le pr�nom suivi du nom d'un utilisateur
function return_nom_prenom_user($id, $bdd)
{
	//On recherche le pr�nom et le nom de l'utilisateur
	$info_user = cherchenomprenom($id, $bdd);
	
	//On renvoi le r�sultat
	return $info_user['prenom']." ".$info_user['nom'];
}

//Fonction optimisant la fonction return_nom_prenom_user en �vitant de faire des requ�tes inutiles
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

//Fonction d'affichage de la vid�o d'un texte
function affiche_video_texte($id, $bdd)
{
	//R�cup�ration des informations de la vid�o
	$info_video = get_info_video($id, $bdd);
	
	//Pr�paration de l'affichage des vid�os
	echo "<div class='video_contener'>";
	
		//Affichage de la vid�o
		affiche_video(array(array($info_video['URL'], $info_video['file_type'])));
	
	//Fermeture de l'affichage de la vid�o
	echo "</div>";
}


//Fonction d'affichage de l'image d'un texte
function affiche_image_texte($info, $afficher_infos = false)
{
	//On v�rifie qu'il s'agit d'une image
	if($info["type"] == "image")
	{
		//Affichage de l'image
		echo '<a class="fancybox" rel="group" href="';
		echo webUserDataFolder($info['path']);
		echo '"><img height="200" src="';
		echo webUserDataFolder($info['path']);
		echo '" alt="" /></a> <br />';
		
		//Si n�cessaire on affiche les donn�es techniques de l'image
		if($afficher_infos)
		{
			echo "Taille de l'image: ".convertis_octets_vers_mo($info['size'])." Mo;";
			echo " Type d'image: ".$info['file_type'].";";
			echo " Chemin d'acc&egrave;s: <a href='".webUserDataFolder($info['path'])."'>".webUserDataFolder($info['path'])."</a><br />";
		}
	}
}

//Fonction de suppression d'une vid�o
function delete_movie($idvideo, $iduser, $bdd)
{
	//R�cup�ration des informations
	$sql = "SELECT * FROM galerie_video WHERE ID = ? AND ID_user = ?";
		
	//Ex�cution de la requ�te
	$requete = $bdd->prepare($sql);
	$requete->execute(array($idvideo, $iduser));
		
	if(!$analyser = $requete->fetch())
		die("Vid&eacute;o non trouv&eacute;.");
		
	//Fermeture de la requ�te
	$requete->closeCursor();
		
	//Suppression des posts relatifs � la video
	$list_posts_relatif = list_ensemble_posts_relatif_a_video($_GET['delete'], $bdd); //R�cup�ration de la liste
	foreach($list_posts_relatif as $enregistrer)
	{
		//Suppression du post
		deletetexte($enregistrer['ID'], $enregistrer['texte'], $bdd);
	}
		
	//Suppression du fichier
	unlink(relativeUserDataFolder($analyser['URL']));
		
	//Suppression de l'entr�e dans la base de donn�es
	$sql = "DELETE FROM galerie_video WHERE ID = ? AND ID_user = ?";
		
	//Ex�cution de la requete
	$requete = $bdd->prepare($sql);
	$requete->execute(array($_GET['delete'], $_SESSION['ID']));
		
}

//Fonction permettant de d�finir si une personne est autoris�e � visualiser une page ou pas et avec quel niveau de visibilit�
function is_allowed_to_view_page($idpage, $bdd)
{
	//R�cup�ration des informations sur la page
	$info_page = cherchenomprenom($idpage, $bdd);
	
	//On v�rifie si la personne n'est pas connect�e
	if(!isset($_SESSION['ID']))
	{
		if($info_page['pageouverte'] == 0)
			return false; //Personne non autoris�e
		else
			return 1; //Personne autoris�e � voire les posts publiques uniquements
	}
	
	//La personne est connect�e
	//On v�rifie si c'est la page de la persone
	if($_SESSION['ID'] == $idpage)
		return 3; //La personne peut voire tout les posts
	
	//On v�rifie si les persones sont amies
	if(!detectesilapersonneestamie($_SESSION['ID'], $idpage, $bdd))
	{
		//On v�rifie si la page est publique
		if($info_page['public'] == 1)
			return 1; //La personne est autoris�e � visualiser les posts publics
		else
			return false; //La personne n'est pas autoris�e � visualiser les posts publiques
	}
	
	//La personne est autoris�e � visualiser les posts de niveau 2 (avec les amis)
	return 2;
}

//Fonction renvoyant le code source pour afficher une vid�o YouTube
function code_video_youtube($adresse)
{
	//Pr�paration de la source
	$source = "";
	
	//G�n�ration de la source
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
	//Cr�ation d'un premier tableau
	$array_1 = explode(" ", $source);
	if(count($array_1) != 2) return $source; //Rien � faire
	
	//Cr�ation du second tableau
	$array_2 = explode("-", $array_1[0]);
	if(count($array_2) != 3) return $source; //Rien � faire
	
	//Cr�ation du troisi�me tableau
	$array_3 = explode(":", $array_1[1]);
	if(count($array_2) != 3) return $source; //Rien � faire
	
	//Cr�ation et renvoi du r�sultat
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
	elseif($difference <= 60) //Inf�rieur � une minute
		return "Il y a ".$difference." secondes";
	elseif($difference <= 3600) //Inf�rieur � une heure
	{
		$nb_minutes = floor($difference/60);
		if($nb_minutes == 1)
			return "Il y a une minute";
		else
			return "Il y a ".$nb_minutes." minutes";
	}
	elseif($difference <= 86400) //Inf�rieur � un jour
	{
		$nb_heures = floor(($difference/60)/60);
		if($nb_heures == 1)
			return "Il y a une heure";
		else
			return "Il y a ".$nb_heures." heures";
	}
	elseif($difference <= 2678400) //Inf�rieur � un mois
	{
		$nb_jours = floor((($difference/60)/60)/24);
		if($nb_jours == 1)
			return "Il y a un jour";
		else
			return "Il y a ".$nb_jours." jours";
	}
	elseif($source != "") //On affiche le jour pr�cis
	{
		//On se sert du timestamp : $timestamp
		$datas_date = date( "w|j|n|Y|H|i|s" , $timestamp);
		$array_date = explode('|', $datas_date);
		
		//On v�rifie que la date est correcte
		if(count($array_date) != 7)
			return $date; //Rien � faire
		
		//D�finition des donn�es
		$days = array("dimanche", "lundi", "mardi", "mercredi", "jeudi", "vendredi", "samedi");
		$months = array("janvier", "f&eacute;vrier", "mars", "avril", "mai", "juin", "juillet", "ao&ucirc;t", "septembre", "octobre", "novembre", "d&eacute;cembre");
		
		
		//Renvoi du r�sultat
		return "Le ".$days[$array_date[0]]." ".$array_date[1]." ".$months[$array_date[2]-1]." ".$array_date[3]." &agrave; ".$array_date[4].":".$array_date[5].":".$array_date[6];
	}
	else //Rien � faire, on renvoi la date
		return $source;
}
	
//Fonction permettant de r�cup�rer la source des commentaires dans un tableau
function getsourcecommentaire_html($source, $type, $fin = '\n')
{
	preg_match_all('#'.$type.'(.*?)'.$fin.'#is', $source, $resultat, PREG_PATTERN_ORDER);
	
	//On renvoi le r�sultat
	return $resultat[0];
}

//Fonction de compilation de code source (n�cessite : getsourcecommentaire_html)
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
	
	//Correction des probl�mes Javascript
	$source = str_replace(' xhr.send(null);', "\n xhr.send(null);", $source);
	
	//On enl�ve les espaces en trop
	for ($i = 0; 15>$i; $i++)
	{
		$source = str_replace('  ', ' ', $source);
	}
	
	//Derni�res corrections... ! Non op�rationnel !
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
	//Requ�te de recherche
	$sql = "SELECT * FROM groupe_personnes WHERE ID_personne = ?";
	
	//Ex�cution de la requ�te
	$requete = $bdd->prepare($sql);
	$requete->execute(array($id_personne*1));
	
	//Enregistrement des r�sultats
	$retour = array();
	while($enregistrer = $requete->fetch())
	{
		$retour[] = $enregistrer;
	}
	
	//Fermeture de la requ�te
	$requete->closeCursor();
	
	//Renvoi du r�sultat
	return $retour;
}

//Print_r d'un tableau avec <pre> int�gr�
function print_r_pre($tableau)
{
	echo "<pre>";
	print_r($tableau);
	echo "</pre>";
}

//Fonction permettant de rechercher l'appartenance � un groupe
function search_appartenance_groupes($id_user, $id_user_connected, $bdd, $open = true)
{
	//On v�rifie si l'on doit chercher tous les groupes ou non
	if(!$open)
	{
		//Requ�te de recherche
		$sql = "SELECT ID FROM groupe_personnes WHERE ID_personne = ? AND ((liste_ID = ?) OR (liste_ID LIKE ?) OR (liste_ID LIKE ?) OR (liste_ID LIKE ?))";
		$requete = $bdd->prepare($sql);
		$requete->execute(array($id_user, $id_user_connected, "%|".$id_user_connected, $id_user_connected."|%", "%|".$id_user_connected."|%"));
	}
	else
	{
		//Requ�te de recherche
		$sql = "SELECT ID FROM groupe_personnes WHERE (liste_ID = ?) OR (liste_ID LIKE ?) OR (liste_ID LIKE ?) OR (liste_ID LIKE ?)";
		$requete = $bdd->prepare($sql);
		$requete->execute(array($id_user_connected, "%|".$id_user_connected, $id_user_connected."|%", "%|".$id_user_connected."|%"));
	}
	
	//Enregistrement des r�sultats
	$retour = array();
	while($enregistrer = $requete->fetch())
	{
		$retour[] = $enregistrer['ID'];
	}
	
	//Fermeture de la requ�te
	$requete->closeCursor();
	
	//Retour des r�sultat
	return $retour;
}

//Fonction permettant de dire si la vid�o est partiellement (ou totalement) restreinte
function visibilite_privee($niveau_visibilite)
{
	if($niveau_visibilite == 3)
		return true;
	elseif(preg_match("<3>", $niveau_visibilite))
		return true;
	else
		return false; //La vid�o est publique
}

//Fonction permettant de finir la compilation de source
function fin_mise_en_cache()
{
	$source = ob_get_contents();
	ob_end_clean();
	echo compile_code_source($source);
}

//Fonction permettant de d�finir si une page en cache est disponible ou non pour ce fichier
function is_page_cached($nom_page)
{
	if(file_exists("cache/".sha1($nom_page)))
		return true;
	else
		return false;
	
}

//Fonction d'�criture du fichier dans le cache
function write_cache($file, $source)
{
	//Adaptation du nom de fichier si n�cessaire
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

//Fonction d'affichage d'un compte � rebours
function affiche_compte_rebours($infos_texte)
{
	//D�termination de la date de fin
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

//Fonction permettant de d�finir toutes les personnes autoris�es � voir un post
function list_personnes_groupes($valeur, $bdd)
{
	//On importe la liste dans un tableau
	$array = explode("|", $valeur);
	
	//On supprime la premi�re valeur (valeur � validit� g�n�rale)
	unset($array[0]);
	
	//Pr�paration du retour
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
		
		//Ex�cution de la requ�te
		$requete = $bdd->prepare($sql);
		$requete->execute($array);
		
		//Affichage des r�sultats
		while($afficher = $requete->fetch())
		{
			//R�cup�ration de la liste des membres du groupe
			$liste_membres = explode('|', $afficher['liste_ID']);
			$liste_membres[] = $afficher['ID_personne'];
			
			//Traitement de la liste des membres
			foreach($liste_membres as $traiter)
			{
				//On v�rifie si le membre a d�j� �t� ajout�
				if(!in_array($traiter, $retour, true))
					$retour[] = $traiter; //Ajout de l'entr�e
			}
		}
		
		//Fermeture de la requ�te
		$requete->closeCursor();
	}
	
	return $retour;
}

//Fonction permettant d'afficher qu'une page est v�rifi�e
function message_checked_page()
{
	echo "</td><td><img style='vertical-align: middle;' src='img/tick.png' title='Page v&eacute;rifi&eacute;e' />";
}

//Fonction permettant de rapporter une erreur durant l'ex�cution du comunic
function report_error($nom_erreur, $raison = "La raison n'a pas &eacute;t&eacute; sp&eacute;cifi&eacute;e.", $details = array())
{
	//Inclusion de la configuration
	include('inc/config/config.php');
	
	
	//Envoi du message
	//V�rification de l'autorisation d'envoi de mails
	if($active_envoi_mail == "oui")
	{
		//Envoi du message
		$send_mail = true;
		$sujet = "[Erreur Comunic] Erreur lors de l'ex�cution d'une page dans Comunic"; //Sujet
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

//Fonction permettant de d�terminer si une personne a d�j� particip� � un sondage ou non
//Retourne false si la personne n'a pas vot�
//Retourne l'ID du choix si la personne a vot�
function vote_personne_sondage($id_personne, $id_sondage, $bdd)
{
	//R�cup�ration des informations dans la base de donn�es
	$infos_vote_sondage = select_sql("sondage_reponse", "ID_utilisateurs = ? AND ID_sondage = ?", $bdd, array($id_personne, $id_sondage));
	
	//On d�termine si la personne a vot� ou non
	if(count($infos_vote_sondage) == 0)
	{
		//Retour n�gatif
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

//R�cup�ration d'un sondage par l'ID du texte correspondant
function get_sondage_by_text_id($id_texte, $bdd)
{
	//R�cup�ration des informations sur le sondage
	$infos_sondage = select_sql("sondage", "ID_texte = ?", $bdd, array($id_texte));
	
	//Si il n'y a pas de sondage => Erreur
	if(count($infos_sondage) == 0)
	{
		//Rapport d'erreur
		report_error('', 'Un sondage devrait &ecirc;tre affich&eacute;, mais celui-ci est introuvable dans la BDD. Erreur dans le fichier view_textes.php (inc).');
		
		//On casse la cha�ne apr�s erreur
		affiche_message_erreur("Une erreur a survenue lors de r&eacute;cup&eacute;ration d'informations relatives au post (Err Get Info Sond0.). Passage au texte suivant");
		echo "</td></tr>";
		
		return false;
	}
	
	return $infos_sondage;
}

//Fonction permettant de v�rifier si un dossier appartient bien � un utilisateur
function checkPersonnalFolder($container_path, $id_user) {
	//D�termination du chemin
	$path = $container_path.$id_user."/";
	
	//On v�rifie l'existence du dossier
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
	
	//On v�rifie l'existence d'un fichier index.php de s�curit�
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