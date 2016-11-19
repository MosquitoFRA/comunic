<?php
	//Inclusion du script de sécurité
	include('securite.php');
	
	//Initialisate page
	include('inc/initPage.php');
	
	//Récupération des informations de la personne
	$infopersonne = cherchenomprenom($_SESSION['ID'], $bdd);
	
	//Vérification de l'appel de cette page
	if(!isset($_SERVER['HTTP_REFERER']))
	{
		header('Location: index.php');
		die();
	}
	
	//Gestion des PDF
	if(isset($_GET['pdf']))
	{
		$no_menu = true;
		ob_start();
	}
		
	
?><!DOCTYPE html>
<html>
	<head>
		<title>Export des donn&eacute;es personnelles</title>
		<?php if(!isset($no_menu)) include(pagesRelativePath('common/head.php')); ?>
	</head>
	<body>
		<?php if(!isset($no_menu)) include(pagesRelativePath('common/pageTop.php'));
		
		//Contrôle du mot de passe
		//On vérifie si la personne est autorisée
		if(!isset($_SESSION[$_SESSION['ID']."-export-donnee-ok"]))
		{
			//Vérifions que la variable nécessaire existe
			if(!isset($_POST['password']))
				die(affiche_message_erreur('Veuillez saisir votre mot de passe pour entrer dans cette page en <a href="'.$_SERVER['HTTP_REFERER'].'">cliquant ici.</a>'));
			
			//Contrôle du mot de passe
			if(crypt_password($_POST['password']) != $afficher['password'])
				die("Mot de passe invalide! Quitte le script. <a href='".$_SERVER['HTTP_REFERER']."'>Retour</a>");
			else
				//C'est OK
				$_SESSION[$_SESSION['ID']."-export-donnee-ok"] = true;
		}
		
		//On prépare l'affichage
		if(!isset($_GET['page']))
			$page = "general";
		else
			$page = $_GET['page'];
		
		//Préparation des requêtes SQL
		$infos_users = optimise_search_info_users($_SESSION['ID'], $bdd, array());
		
	//Ouverture de la page
	?><h1 class='titre'>Export des donn&eacute;es personnelles</h1><?php
	
	//Menu général
	//Image pour les PDF = img/file_extension_pdf.png
	$liste_menu = array(
		"general" => "Informations g&eacute;n&eacute;rales du compte",
		"chat_publique" => "Posts dans le chat public",
		"chat_prive" => "Posts dans le chat priv&eacute;",
		"notifications" => "Donn&eacute;es des notifications dont vous &ecirc;tes &agrave; l'origine",
		"textes" => "Les textes que vous avez post&eacute;",
		"commentaires" => "Les commentaires que vous avez post&eacute;",
		"amis" => "Vos amis",
		"groupes" => "Vos groupes",
		"aimes" => "Ce que vous aimez",
		"videos" => "Votre galerie de vid&eacute;os",
		"contact_admin" => "Vos contacts avec l'administration",
		"messages" => "Les messages que vous avez envoy&eacute;",
	);
	echo "<ul>";
	foreach($liste_menu as $nom_page=>$description)
	{
		echo "<li><a href='".$_SERVER['PHP_SELF']."?page=".$nom_page."'>".$description."</a>";
		//echo "<a href='".$_SERVER['PHP_SELF']."?page=".$nom_page."&pdf'><img src='img/file_extension_pdf.png' widht='16' height='16' /></a>";
		echo "</li>";
	}
	echo "</ul>";
	
	//Gestion des PDF
	if(isset($_GET['pdf']))
	{
		ob_end_clean();
		ob_start();
	}
	
	
	switch($page)
	{
		case "general":
		?><h2>Informations g&eacute;n&eacute;rales</h2>
		<table border="1" class="export_donnee_table">
			<tr>
				<td>Num&eacute;ro du compte</td><td><?php echo $_SESSION['ID']; ?></td>
			</tr>
			<tr>
				<td>Pr&eacute;nom</td><td><?php echo  $infopersonne['prenom']; ?></td>
			</tr>
			<tr>
				<td>Nom</td><td><?php echo  $infopersonne['nom']; ?></td>
			</tr>
			<tr>
				<td>Adresse Mail</td><td><?php echo  $infopersonne['mail']; ?></td>
			</tr>
			<tr>
				<td>Date de cr&eacute;ation de comtpe</td><td><?php echo  $infopersonne['date_creation']; ?></td>
			</tr>
			<tr>
				<td>Mot de passe (crypt&eacute;)</td><td><?php echo  $infopersonne['password']; ?></td>
			</tr>
			<tr>
				<td>Question 1 de s&eacute;curit&eacute; :</td><td><?php echo $infopersonne['question1']; ?></td>
			</tr>
			<tr>
				<td>R&eacute;ponse 1 de s&eacute;curit&eacute; :</td><td><?php echo $infopersonne['reponse1']; ?></td>
			</tr>
			<tr>
				<td>Question 2 de s&eacute;curit&eacute; :</td><td><?php echo $infopersonne['question2']; ?></td>
			</tr>
			<tr>
				<td>R&eacute;ponse 2 de s&eacute;curit&eacute; :</td><td><?php echo $infopersonne['reponse2']; ?></td>
			</tr>
			<tr>
				<td>Affichage du chat automatique</td><td><?php if($infopersonne['affiche_chat'] == "1") echo "Oui"; else echo "Non"; ?></td>
			</tr>
			<tr>
				<td>Page publique</td><td><?php if($infopersonne['public'] == "1") echo "Oui"; else echo "Non"; ?></td>
			</tr>
			<tr>
				<td>Page ouverte</td><td><?php if($infopersonne['pageouverte'] == "1") echo "Oui"; else echo "Non"; ?></td>
			</tr>
			<tr>
				<td>Avatar</td><td><?php echo avatar($_SESSION['ID'], $urlsite); ?></td>
			</tr>
			<tr>
				<td>Image de fond</td><td><?php echo imgfond($_SESSION['ID'], $urlsite); ?></td>
			</tr>
			<tr>
				<td>Autoriser l'affichage de commentaires</td>
				<td><?php if($infopersonne['bloquecommentaire'] == "1") echo "Non"; else echo "Oui"; ?></td>
			</tr>
			<tr>
				<td>Utiliser l'ancienne version des notifications</td>
				<td><?php if($infopersonne['bloquenotification'] == "1") echo "Non"; else echo "Oui"; ?></td>
			</tr>
			<tr>
				<td>Dans l'ancienne version des notifications, activer le son</td>
				<td><?php if($infopersonne['bloque_son_notification'] == "1") echo "Non"; else echo "Oui"; ?></td>
			</tr>
			<tr>
				<td>Utiliser l'ancienne version du menu</td>
				<td><?php if($infopersonne['old_menu'] == "0") echo "Non"; else echo "Oui"; ?></td>
			</tr>
			<tr>
				<td>Votre volet des amis est-il ouvert ?</td>
				<td><?php if($infopersonne['volet_amis_ouvert'] == "0") echo "Non"; else echo "Oui"; ?></td>
			</tr>
			<tr>
				<td>Vos amis peuvent-ils poster des textes sur votre page ?</td>
				<td><?php if($infopersonne['autoriser_post_amis'] == "0") echo "Non"; else echo "Oui"; ?></td>
			</tr>
			<tr>
				<td>Autorisez-vous Comunic &agrave; vous envoyer des mails ?</td>
				<td><?php if($infopersonne['autorise_mail'] == "0") echo "Non"; else echo "Oui"; ?></td>
			</tr>
			<tr>
				<td>Structurez-vous votre page en plusieurs cat&eacute;gories ? (en d&eacute;veloppement)</td>
				<td><?php if($infopersonne['mode_pages'] == "0") echo "Non"; else echo "Oui"; ?></td>
			</tr>
			<tr>
				<td>Quelle est la couleur de votre menu ?</td>
				<td><?php echo $infopersonne['color_menu']; ?></td>
			</tr>
			<tr>
				<td>Nom de votre r&eacute;pertoire virtuel :</td>
				<td><?php echo $urlsite.$infopersonne['sous_repertoire']; ?></td>
			</tr>
			<tr>
				<td>Disposez-vous d'un acc&egrave;s &agrave; des ressources scholaires ?</td>
				<td><?php if($infopersonne['acces_ecolev2'] == "0") echo "Non"; else echo "Oui"; ?></td>
			</tr>
			<tr>
				<td>Le panneau du chat priv&eacute; est-il ouvert ?</td>
				<td><?php if($infopersonne['view_private_chat'] == "0") echo "Non"; else echo "Oui"; ?></td>
			</tr>
			<tr>
				<td>Quelle est la hauteur actuelle de votre chat priv&eacute; ?</td>
				<td><?php echo $infopersonne['height_private_chat']; ?> pixels</td>
			</tr>
			<tr>
				<td>Avez-vous activ&eacute; le nettoyage automatique des notifications ?</td>
				<td><?php if($infopersonne['nettoyage_automatique_notifications'] == "0") echo "Non"; else echo "Oui"; ?></td>
			</tr>
			<tr>
				<td>Le nettoyeur automatique des notifications doit nettoyer les notifications ayant plus de...</td>
				<td><?php echo $infopersonne['mois_nettoyage_automatique_notifications']." mois ".$infopersonne['jour_nettoyage_automatique_notifications']." jour(s) ".$infopersonne['heure_nettoyage_automatique_notifications']." heure(s)"; ?></td>
			</tr>
			<tr>
				<td>Votre page est-elle v&eacute;rifi&eacute;e ?</td>
				<td><?php if($infopersonne['page_verifiee'] == "0") echo "Non"; else echo "Oui"; ?></td>
			</tr>
			<tr>
				<td>Adresse de votre site web</td>
				<td><?php if($infopersonne['site_web'] == "") echo "Aucun site web d&eacute;fini"; else echo $infopersonne['site_web']; ?></td>
			</tr>
			<tr>
				<td>Votre liste d'amis est-elle publique ?</td>
				<td><?php if($infopersonne['liste_amis_publique'] == "0") echo "Non"; else echo "Oui"; ?></td>
			</tr>
		</table>
		
		<h3>Donn&eacute;es techniques :</h3>
		<pre><?php print_r($infopersonne); ?></pre>
		
		<?php 
			break;
			case "chat_publique":
		?>
		
		<h2>Donn&eacute;es du chat</h2>
		<table>
			<tr><td>Num&eacute;ro</td><td>Date de l'envoi</td><td>Post</td></tr>
			<?php
			//Requete de recherche de post
			$sql='SELECT * FROM chat WHERE ID_personne = '.$_SESSION['ID'];
			
			//Execution de la requete
			$requete = $bdd->query($sql);
			
			//Affichage des résultats
			while ($afficherchat = $requete->fetch())
			{
				?>
					<tr>
						<td><?php echo $afficherchat['ID']; ?></td>
						<td><?php echo $afficherchat['date_envoi']; ?></td>
						<td><?php echo affiche_smile(corrige_caracteres_speciaux(bloquebalise($afficherchat['message']))); ?></td>
					</tr>
				<?php
			}
			
			//Fermeture de la requete
			$requete->closeCursor();
			?>
		</table>
		
		<?php 
			break;
			case "chat_prive":
		?>
		
		<h2>Donn&eacute;es du chat priv&eacute;</h2>
		<table border="1" class="export_donnee_table">
			<tr><td>Num&eacute;ro</td><td>Date d'envoi</td><td>Exp&eacute;diteur</td><td>Destinataire</td><td>Contenu</td></tr><?php
		//Affichage des données du chat privé
		$sql = 'SELECT * FROM chatprive WHERE ID_personne = '.$_SESSION['ID'].' ORDER BY ID';
			
		//Exécution de la requete
		$chatprive = $bdd->query($sql);
		
		//Affichage des résultats
		while($afficher = $chatprive->fetch())
		{
			echo "<tr>";
			echo "<td>";
			echo $afficher['ID'];
			echo "</td>";
			echo "<td>";
			echo $afficher['date_envoi'];
			echo "</td>";
			echo "<td> De vous";
			echo "</td>";
			echo "<td> &agrave; ";
			echo return_nom_prenom_user($afficher['ID_destination'], $bdd);
			echo "</td>";
			echo "<td>";
			echo affiche_smile(corrige_caracteres_speciaux(corrige_accent_javascript(bloquebalise($afficher['contenu']))));
			echo "</td>";
			echo "</tr>";
		}
		
		//Fermeture de la requête
		$chatprive->closeCursor();
		?></table>
		
		<?php 
			break;
			case "notifications":
		?>
		
		<h2>Donn&eacute;es des notifications dont vous &ecirc;tes &agrave; l'origine</h2>
		<table border="1" class="export_donnee_table">
			<tr><td>Num&eacute;ro</td><td>Nom du destinataire</td><td>Date d'envoi</td><td>Contenu de la notification</td><td>Vu ?</td><td>Adresse en cas de clic</td></tr><?php
		//Affichage des données du chat privé
		$sql = 'SELECT * FROM notification WHERE ID_createur = '.$_SESSION['ID'].' ORDER BY ID';
			
		//Exécution de la requete
		$chatprive = $bdd->query($sql);
		
		//Affichage des résultats
		while($afficher = $chatprive->fetch())
		{
			echo "<tr><td>";
			echo $afficher['ID'];
			echo "</td><td>";
			echo return_nom_prenom_user($afficher['ID_personne'], $bdd);
			echo "</td><td>";
			echo $afficher['date_envoi'];
			echo "</td><td>";
			echo $infopersonne['prenom']." ".$infopersonne['nom']." ";
			echo $afficher['message'];
			echo "</td><td>";
			echo ($afficher['vu'] == 1 ? "Oui" : "Non");
			echo "</td><td>";
			echo $afficher['adresse'];
			echo "</td></tr>";
		}
		
		//Fermeture de la requête
		$chatprive->closeCursor();
		?></table>
		
		<?php 
			break;
			case "textes":
		?>
		
		<h2>Textes</h2>
		<?php 
			//On recherche les textes
			$textes = affichertextes($_SESSION['ID'], $bdd, 0, 10000000, 3, true);
			
			//On les affiche
			?><table class="export_donnee_table">
				<tr><td>Num&eacute;ro du texte</td><td>Date de l'envoi</td><td>Texte</td><td>Niveau de visibilit&eacute;</td></tr><?php
			foreach ($textes as $afficher)
			{
				//On affiche le texte
				echo '<tr><td>'.$afficher['ID'].'</td><td>'.$afficher['date_envoi'].'</td><td>';
				
				//On affiche des vidéos si il y en a, ainsi que des images
				if($afficher['type'] == "video")
					affiche_video_texte($afficher['idvideo'], $bdd); //Affichage de la vidéo
				elseif($afficher['type'] == "image")
					affiche_image_texte($afficher, true); //Affichage de l'image
				elseif($afficher['type'] == "youtube")
					echo "<p>ID de la vid&eacute;o Youtube int&eacute;gr&eacute;e au post : ".$afficher['path']."</p>";//Affichage de l'ID de la vidéo
				elseif($afficher['type'] == "count_down")
				{
					echo "<p>Date de fin du compteur &agrave; rebours int&eacute;gr&eacute; au post: ".$afficher['jour_fin']."/".$afficher['mois_fin']."/".$afficher['annee_fin']." </p>"; //Affichage des informations sur le compteur à rebous
				}
				elseif($afficher['type'] == "web_image")
				{
					affiche_image_texte(array('type' => "image", "path" => $afficher['path']), false); //Affichage de l'image
					echo "Adresse de l'image :".$afficher['path']." <br />";
				}
				elseif($afficher['type'] == "pdf")
					affiche_lien_pdf($afficher);
				elseif($afficher['type'] = "webpage_link")
					affiche_lien_webpage($afficher);
				
				//Fin de l'affichage du texte
				echo adaptetexteimage(corrige_caracteres_speciaux(affiche_smile($afficher['texte']))).'</td><td>';
				affiche_menu_change_niveau_visibilite($afficher['niveau_visibilite'], $afficher['ID'], true);
				echo '</td></tr>';
			}
			?></table>
			
		<?php 
			break;
			case "commentaires":
		?>
		
		<h2>Commentaires</h2>
		<?php
			//Affichage des commentaires
			$sql = 'SELECT * FROM commentaires WHERE ID_personne = '.$_SESSION['ID'].' ORDER BY ID';
			
			//Exécution de la requete
			$commentaires = $bdd->query($sql);
			
			//On affiche le résultat
			echo '<table class="export_donnee_table">';
			?><tr><td>Num&eacute;ro</td><td>Numero du texte</td><td>Date d'envoi</td><td>Commentaire</td></tr><?php
			while($affichercom = $commentaires->fetch())
			{
				$commentaire = corrige_caracteres_speciaux($affichercom['commentaire']);
				?><tr>
					<td><?php echo $affichercom['ID']; ?></td>
					<td><?php echo $affichercom['ID_texte']; ?></td>
					<td><?php echo $affichercom['date_envoi']; ?></td>
					<td><?php echo affiche_smile(corrige_caracteres_speciaux(corrige_accent_javascript(bloquebalise($commentaire)))); ?></td>
				</tr><?php
			}
			echo '</table>';
			
			break;
			case "amis":
		
		?>
		<h2>Vos amis</h2>
		<?php
			//Requete de recherche d'amis
			$sql = "SELECT * FROM amis WHERE ID_personne = ".$_SESSION['ID']." ";
			
			//Exécution de la requete
			$requeteamis = $bdd->query($sql);
			
			//Affichage des résultats
			echo "<table class='export_donnee_table'>";
			echo "<tr><td> Nom de l'ami </td><td> Actif </td><td> Abonn&eacute; aux notifications ? </td><td> Droit de poster des textes sur votre page ?</td></tr>";
			while($afficheramis = $requeteamis->fetch())
			{
				?>
				<tr>
					<td><?php
					//Requete de recherche de l'avatar de la personne
					  echo avatar($afficheramis['ID_amis'], "./");
					  
						//Recherche du nom de la personne
						$sql = "SELECT * FROM utilisateurs WHERE ID = ".$afficheramis['ID_amis']." ";
						
						//Exécution de la requete
						$recherchenom = $bdd->query($sql);
						
						//Affichage du résultat
						$affichernom = $recherchenom->fetch();
						echo corrige_caracteres_speciaux($affichernom['prenom']." ".$affichernom['nom']);
						
						//Fermeture de la requete
						$recherchenom->closeCursor();
						
					?></td><td>
					<?php
					//Affichage de l'activité : oui ou non 
					echo($afficheramis['actif'] == 0 ? "Amis inactif." : "Actif");
					?></td><td><?php
					//Affichage de amis 
					echo($afficheramis['abonnement'] == 0 ? "Non" : "Oui");
					?>
					</td><td><?php
					//Affichage de amis 
					echo($afficheramis['autoriser_post_page'] == 0 ? "Non" : "Oui");
					?>
					</td>
				</tr>
				<?php
				
				$amis = 1;
			}
			echo "</table>";
			
			//Fermeture de la requete
			$requeteamis->closeCursor();
			
			if(!isset($amis))
			{
				echo "Vous n'avez pour le moment aucun ami. <br />";
			}
			
			break;
			case "groupes":
			
			?><h2>Vos groupes</h2>
			<table class="export_donnee_table">
				<tr><td>Num&eacute;ro du groupe</td><td>Propri&eacute;taire</td><td>Nom du groupe</td><td>Date d'ajout</td><td>Membres du groupes</td></tr><?php
			
			//Récupération de la liste des groupes
			$liste_groupes = list_groupes_personnes($_SESSION['ID'], $bdd);
			
			foreach($liste_groupes as $afficher_groupe)
			{
				echo "<tr><td>";
				echo $afficher_groupe['ID']."</td><td>";
				
				//Chargement des informations utilisateurs
				$infos_users = optimise_search_info_users($_SESSION['ID'], $bdd, $infos_users);
				echo $infos_users[$afficher_groupe['ID_personne']]['avatar_32_32'].$infos_users[$afficher_groupe['ID_personne']]['table_utilisateurs']['prenom']." ".$infos_users[$afficher_groupe['ID_personne']]['table_utilisateurs']['nom'];
				echo "</td><td>";
				echo $afficher_groupe['nom'];
				echo "</td><td>";
				echo $afficher_groupe['date_ajout'];
				echo "</td><td>";
				
				//Listing des membres du groupes
				$membres_groupe = explode("|", $afficher_groupe['liste_ID']);
				
				//Affichage des membres du groupe
				foreach($membres_groupe as $afficher_personne)
				{
					//Chargement des informations utilisateurs
					$infos_users = optimise_search_info_users($afficher_personne, $bdd, $infos_users);
					echo $infos_users[$afficher_personne]['avatar_32_32'].$infos_users[$afficher_personne]['table_utilisateurs']['prenom']." ".$infos_users[$afficher_personne]['table_utilisateurs']['nom']." &nbsp; ";
				
				}
				
				echo "</td></tr>";
			}
			
			echo "</table>";
			
			break;
			case "aimes":
		?>
		<h2>Ce que vous aimez</h2>
		<table class="export_donnee_table">
			<tr><td>Num&eacute;ro</td><td>Num&eacute;ro du texte/page</td><td>Type</td><td>Date d'ajout</td></tr>
			<?php
				//Requête de recherche
				$sql = "SELECT * FROM aime WHERE ID_personne = ".$_SESSION['ID']." ";
				
				//Exécution de la requete
				$aime = $bdd->query($sql);
				
				//Affichage des résultats
				while($afficher = $aime->fetch())
				{
					echo "<tr><td>".$afficher['ID']."</td><td>".$afficher['ID_type']."</td><td>".$afficher['type']."</td><td>".$afficher['Date_envoi']."</tr>";
				}
			?>
		</table>
		
		<?php
		break;
		case "videos":
		?>
		
		<!-- Export des données de la galerie de vidéos -->
		<h2>Donn&eacute;es de la galerie de vid&eacute;o</h2>
		<table class="export_donnee_table">
			<tr><td>Num&eacute;ro</td><td>Adresse de la vid&eacute;o</td><td>Nom de la vid&eacute;o</td><td>Type de vid&eacute;o</td><td>Taille</td></tr><?php
			//Récupération de la liste des vidéos
			$liste_videos = liste_videos_user($_SESSION['ID'], $bdd);
			
			//Affichage de la liste
			foreach($liste_videos as $afficher)
			{
				echo "<tr><td>";
				echo $afficher['ID'];
				echo "</td><td>";
				echo "<a href='".$afficher['URL']."' title='Ouvrir la vid&eacute;o'>".$afficher['URL']."</a>";
				echo "</td><td>";
				echo corrige_caracteres_speciaux(corrige_accent_javascript($afficher['nom_video']));
				echo "</td><td>";
				echo $afficher['file_type'];
				echo "</td><td>";
				echo convertis_octets_vers_mo($afficher['size'])." Mo";
				echo "</td></tr>";
			}
		?></table>
		<!-- Fin de: Export des données de la galerie de vidéos -->
		
		<?php
		break;
		case "contact_admin":
		?>
		
		<h2>Contact avec l'administration</h2>
		<p><b>Note :</b> Seuls les contacts qui ont &eacute;t&eacute; conserv&eacute;s par l'administration sont affich&eacute;s sur cette page.</p>
		<table class="export_donnee_table">
			<tr><td>Num&eacute;ro</td><td>Raison du contact</td><td>Date d'envoi</td><td>Contenu du message</td><td>Lu par l'administration</td><td>Adresse IP de l'envoi</td><td>Facultatif: Adresse mail</td></tr>
			<?php
				//Récupération de la liste des types de contacts
				$liste_contact = get_list_type_contact($bdd);
			
				//Requête de recherche
				$sql = "SELECT * FROM contact WHERE ID_personne = ".$_SESSION['ID']." ";
				
				//Exécution de la requête
				$contact = $bdd->query($sql);
				
				//Affichage des résultats
				while($afficher = $contact->fetch())
				{
					echo "<tr><td>".$afficher['ID']."</td><td>".$liste_contact[$afficher['ID_type']-1]['nom_fr']."</td><td>".$afficher['date_envoi']."</td><td>".corrige_caracteres_speciaux($afficher['texte'])."</td><td>"; 
					if( $afficher['vu'] == 1) echo "Oui"; else echo "Non";
					echo"</td><td>".$afficher['IP_personne']."</td><td>".$afficher['mail_personne']."</td></tr>";
				}
				
				//Fermeture de la requête
				$contact->closeCursor();
			?>
		</table>
		
		<?php
		break;
		case "messages":
		?>
		
		<h2>Messagerie</h2>
		<i>Ici ne sont affich&eacute;s uniquement les messages que vous avez envoy&eacute;s. En effet, vous n'&ecirc;tes pas l'auteur direct des envois des messages que l'on vous a envoy&eacute;. Si vous souhaitez tout de m&ecirc;me obtenir ces messages, veuillez nous <a href='contact.php'>contacter</a>.</i>
		<table class="export_donnee_table">
			<tr><td>Num&eacute;ro</td><td>Destinataire</td><td>Objet</td><td>Message</td><td>Date de l'envoi</td><td>Lu</td></tr>
			<?php
				//Requête de recherche
				$sql = "SELECT * FROM messagerie WHERE ID_expediteur = ".$_SESSION['ID']." ORDER BY ID DESC";
				
				//Exécution de la recherche
				$messages = $bdd->query($sql);
				
				//Affichage des résultats
				while($afficher_message = $messages->fetch())
				{
					echo "<tr><td>".$afficher_message['ID']."</td>";
					
					//Chargement des informations utilisateurs
					$infos_users = optimise_search_info_users($afficher_message['ID_destinataire'], $bdd, $infos_users);
					
					echo "<td>".$infos_users[$afficher_message['ID_destinataire']]['avatar_32_32'].$infos_users[$afficher_message['ID_destinataire']]['table_utilisateurs']['prenom']." ".$infos_users[$afficher_message['ID_destinataire']]['table_utilisateurs']['nom']."</td>";
					
					
					echo "<td>".corrige_caracteres_speciaux($afficher_message['objet'])."</td>";
					echo "<td>".corrige_caracteres_speciaux($afficher_message['message'])."</td><td>".$afficher_message['date_envoi']."</td><td>".($afficher_message['lu'] == 0 ? "Non" : "Oui")."</td></tr>";
				}
				
				//Fermeture de la recherche
				$messages->closeCursor($sql);
			?>
		</table>
		
	<?php }
		
		//Si demandé, on génère le PDF
		if(isset($_GET['pdf']))
		{
			$liste_pages[0] = ob_get_contents();
			ob_end_clean();
			
			//Traitement des données
			//$liste_pages[0] = str_replace(array('<table', '<tr', '<td', '</td', '</tr', '</table'), "<hide", $liste_pages[0]);
			$liste_pages[0] = str_replace(array('<table'), '<table  border="1" cellspacing="3" cellpadding="4"', $liste_pages[0]);
			
			//echo $liste_pages[0];
			
			$nom_fichier_pdf = "export_donnee.pdf";
			$auteur_pdf = "Comunic";
			$titre_pdf = "Export des donnees personnelles";
			$sujet_pdf = $page;
			$pdf_key_word = "donnees_personnelles, ".$page;
			
			//Génération du PDF
			include('inc/generate_pdf.php');
			
			//On quitte la page
			die();
		}
		
	?>
		
		<p><i>Comunic vous a retranscrit ici toute les informations qu'il poss&eacute;dait sur vous. Cependant, si vous n'&ecirc;tes pas pleinement satisfait, n'h&eacute;sitez pas &agrave; nous <a href='contact.php'>contacter.</a></i></p>
		<hr>
		<?php if(!isset($no_menu)) include(pagesRelativePath('common/pageBottom.php')); ?>
	</body>
</html>