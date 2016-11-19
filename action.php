<?php
	/* Service Pierre 2014 */
	//Démarrage de la session
	session_start();
	
	//Inclusion de la configuration
	include('inc/initPage.php');
	
	//Initialisation de la langue
	$lang = detecteinstallelangue();
	
	if(!isset($_GET['actionid']))
	{
		header('location: index.php');
		exit("erreur 001 : il manque une variable requise !");
	}
	elseif($_GET['actionid'] == "")
	{
		header('location: index.php');
		die("erreur 002 : Une variable requise est vide !");
	}
	
	//Accès à l'ID des actions plus rapide
	$action = $_GET['actionid'];
	
	//Liste des actions :
	/*
		1  -  Vérification de la présence de nouveaux messages pour l'utilisateur
		2  -  Fichier Javascript renvoyant la liste des smiley disponibles
		3  -  Renvoi le nombre de notifications non vues
		4  -  Permet d'envoyer un mail contenant l'ensemble des données personnelles
		5  -  Permet de rediriger vers le lien d'une notification
		6  -  Permet de mettre une notification en vue
		7  -  Permet de vérifier la présence d'une nouvelle demande d'amis
		8  -  Permet l'éditon d'un commentaire par l'intermédiaire d'une requête Ajax
		9  -  Permet de renvoyer le code source d'un CAPATCHA
		10 -  Permet d'envoyer une vidéo
		11 -  Suite de l'envoi de la vidéo
		12 -  Change le nom d'une vidéo
		13 -  Disponible
		14 -  Change le niveau de visibilité d'un post
		15 -  Suppression d'un commentaire
		16 -  Aimer ou pas un commentaire
		17 -  Afficher ou masquer le panneau du chat privé
		18 -  Changer la hauteur du chat
		19 -  Ouvrir/Fermer une session de chat privé
		20 -  Génération du code pour une session de chat
		21 -  Permet d'aimer ou de ne plus aimer un texte ou une page
		22 -  Permet la recherche AJAX d'utilisateurs
		23 -  Permet de supprimer une demande à devenir ami
		24 -  Permet de vider supprimer les anciennes notifications
		25 -  Permet de voir, ajouter ou supprimer l'abonnement aux activités d'un amis
		26 -  Permet de changer d'avatar en utilisant la WebCam
		27 -  Permet de supprimer l'avatar ou l'image de fond
		28 -  Nettoyage du compte de l'utilisateur
		29 -  Affichage des textes de l'utilisateur
		30 -  Valide que la personne a vue le message concernant les cookies
		31 -  Récupération du contenu de l'en-tête de la page
		32 -  Récupération du code source pour l'ajout de post
		33 -  Ajout de Comunic comme moteur de recherche dans les navigateurs
		34 -  Aimer une page web externe
		35 -  Permet d'indiquer que la personne a vu le message relatif au fil d'actualité
		36 -  Affiche toute les conversations récentes dans le chat privé
		37 -  Voter pour un sondage (donner sa réponse)
	*/
	
	//Inclusion de la liste des Smiley
	if($action == "2")
	{
		//Inclusion du fichier d'affichage de la liste
		ob_start();
		include 'inc/smile_javascript.php';
		$source = ob_get_clean();
		
		//Enregistrement du contenu du fichier
		file_put_contents("assets/js/liste_smile.js", $source);
		
		//Redirection
		header('Location: assets/js/liste_smile.js');
		
		//Fermeture du fichier
		die();
	}
	//Permet de renvoyer le code source d'un CAPATCHA
	elseif($action == "9")
	{
		//Intégration de la bibliothèque
		$cryptinstall=path_3rdparty("crypt/cryptographp.fct.php");
		include $cryptinstall;
		
		//Génération et renvoi du code source (avec adaptation)
		ob_start(); //Démarrage du blocage
		dsp_crypt(0,1); //Génération du code source
		$source = ob_get_contents(); //Récupération du code source
		ob_end_clean(); //Fin du blocage
		$source = str_replace(path_3rdparty("crypt/"), url_3rdparty("crypt/"), $source); //Adaptation de la source
		echo $source; //Affichage de la source
		
		//Fermeture du fichier
		die();
	}
	//Renvoi des textes de l'utilisateur
	elseif($action == "29")
	{
		$ok_textes_check = 1;
		include(pagesRelativePath('homeUser/viewTexts/viewTexts.inc.php'));
	}
	//Valide que la personne a vue le message concernant les cookies
	elseif($action == "30")
	{
		//On enregistre le cookie pour un an
		setcookie("ok_message_cookie", "Message vu",  time() + 365*24*3600);
	}
	//Récupère le contenu de l'en-tête d'une page
	elseif($action == "31")
	{
		//Clé de sécurité et inclusion
		$header_page_ok = 1;
		include('inc/header_page.php');
	}
	//Récupère le contenu de l'en-tête d'une page
	elseif($action == "33")
	{
		//Clé de sécurité et inclusion
		$add_comunic_as_search_engine_check = 1;
		include('inc/add_comunic_as_search_engine.php');
	}
	//Aimer une page web externe
	elseif($action == "34")
	{
		//Clé de sécurité et inclusion
		$like_external_page_verification = 1;
		include('inc/like_external_page.php');
	}
	
	//Actions nécessitants une connexion de l'utilisateur
	if(isset($_SESSION['ID']))
	{
		//Vérification de la présence de nouveaux messages
		if($action == "1")
		{
			if(verifier_nouveaux_messages_utilisateur($_SESSION['ID'], $bdd))
			{
				echo "1";
			}
			else
			{
				echo "0";
			}
			
			//Fermeture du fichier
			die();
		}
		//Vérification du nombre de notifications inconnues
		elseif($action == "3")
		{
			//On recherche les notifications
			$sql = "SELECT COUNT(*) AS nb_notifications FROM notification WHERE ID_personne = ? AND vu = ?";
			$requete = $bdd->prepare($sql);
			$requete->execute(array($_SESSION['ID'], 0));
			
			if(!$infos = $requete->fetch())
				die('DB error');
		
			//Fermeture de la requête
			$requete->closeCursor();
			
			//Renvoi du résultat
			echo $infos['nb_notifications'];
			
			//Fermeture du fichier
			die();
		}
		//Envoi des données personnelles
		elseif($action == "4")
		{	
			
			//Envoi du mail
			//Vérification de l'autorisation de l'envoi d'un mail
			if($active_envoi_mail == "oui")
			{
				//Récupération du code source de la page
				ob_start();
				$no_menu = true;
				include('exportdonnees.php');
				$source_donnees = ob_get_contents();
				ob_end_clean();
				
				if(!isset($afficher)) die();
				
				//Envoi d'un message de confirmation
				$send_mail = "";
				$sujet = "Donnees personnels";
				$description_rapide = "Voici vos données personnelles";
				$nom_destinataire = $afficher['prenom']." ".$afficher['nom'];
				$adresse_mail_destinataire = $afficher['mail'];
				$texte_message = $source_donnees;
				
				//Envoi du message
				include('inc/envoi_mail.php');
				
				//Affichage du succès
				include(pagesRelativePath('common/head.php'));
				include(pagesRelativePath('common/pageTop.php'));
				?><p>Le message a &eacute;t&eacute; envoy&eacute;.</p><?php
			}
			else
			{
				?><p>L'envoi de mail est d&eacute;sactiv&eacute;.</p><?php
			}
			
			//Fermeture du fichier
			die();
		}
		//Lien provenant d'une notification
		elseif($action == "5" && isset($_GET['idnotification']))
		{
			//Récupération de la notification
			$sql = "SELECT * FROM notification WHERE (ID_personne = ? AND ID = ?)";
			$recuperation = $bdd->prepare($sql);
			$recuperation->execute(array($_SESSION['ID'], $_GET['idnotification']));
			
			if(!$recuperer = $recuperation->fetch())
			{
				//La notification n'existe pas
				header('Location: index.php');
				
				//Fermeture de la page
				die();
			}
			
			//On met la notification en vu
			$sql = "UPDATE notification SET vu = 1 WHERE ID = ?";
			$update = $bdd->prepare($sql);
			$update->execute(array($recuperer['ID']));
			
			//Sécurité
			if($recuperer['adresse'] != "")
				//Redirection vers la page notifiée
				header('Location: '.$recuperer['adresse']);
			else
				//Redirection verse la page d'acceuil
				header('Location: index.php');
			
			//Fermeture du fichier
			die();
		}
		//Mettre une notification en "vu" puis rediriger vers la page notification.php
		elseif($action == "6" && isset($_GET['idnotification']))
		{
			//Récupération de la notification
			$sql = "SELECT * FROM notification WHERE (ID_personne = ? AND ID = ?)";
			$recuperation = $bdd->prepare($sql);
			$recuperation->execute(array($_SESSION['ID'], $_GET['idnotification']));
			
			if(!$recuperer = $recuperation->fetch())
			{
				//La notification n'existe pas
				header('Location: notification.php');
				
				//Fermeture de la page
				die();
			}
			
			//On met la notification en vu
			$sql = "UPDATE notification SET vu = 1 WHERE ID = ?";
			$update = $bdd->prepare($sql);
			$update->execute(array($recuperer['ID']));
			
			//Redirection vers la page de notification
			header('Location: notification.php');
			
			//Fermeture du fichier
			die();
		}
		//Vérification de la présence de nouvelles demandes d'amis
		elseif($action == "7")
		{
			if(issetdemandesamis($_SESSION['ID'], $bdd))
			{
				echo "1";
			}
			else
			{
				echo "0";
			}
			
			//Fermeture du fichier
			die();
		}
		//Edition d'un commentaire par une requête ajax
		elseif($action == "8")
		{
			//Vérification de l'existence des variables requises
			if(isset($_POST['commentaire']) AND isset($_POST['idtexte']) AND isset($_POST['idcommentaire']))
			{
				if($_POST['commentaire'] != "" AND $_POST['commentaire'] != " ")
				{
					if($_POST['idtexte'] != "")
					{
						//Récupération des informations de la liste des commentaires pour ce texte
						$liste_commentaire = affichecommentaire($_POST['idtexte'], $bdd);
						
						//Vérifions que le commmentaire existe
						$verification_ok = 0;
						foreach($liste_commentaire as $verifier)
						{
							if($verifier['ID_personne'] == $_SESSION['ID'] AND $verifier['ID'] == $_POST['idcommentaire']  AND $verifier['ID_texte'] == $_POST['idtexte']);
							{
								//Vérification ok
								$verification_ok = 1;
							}
						}
						
						if($verification_ok == 0)
							die("404 commentaire"); //Sécurité
							
						//Mise à jour du commentaire
						$sql = "UPDATE commentaires SET commentaire = ? WHERE ID = ? AND ID_personne = ? AND ID_texte = ?";
						
						//Exécution de la requête
						$requete = $bdd->prepare($sql);
						$requete->execute(array($_POST['commentaire'], $_POST['idcommentaire'], $_SESSION['ID'], $_POST['idtexte'])); 
						
						echo "termine";
					}
				}
			}
			
			//Fermeture du fichier
			die();
		}
		//Upload d'une vidéo
		elseif($action == "10")
		{
			//Sécurité
			$verification = 1;
		
			//Inclusion du fichier
			include('inc/upload_video.php');
			
			//Fermeture du fichier
			die();
		}
		//Suite de l'upload de la vidéo
		elseif($action == "11")
		{
			//Sécurité
			$verification = 1;
			
			//Inclusion du fichier
			include('inc/upload_video_suite.php');
			
			//Fermeture du fichier
			die();
		}
		//Changer le nom d'une vidéo
		elseif($action == "12")
		{
			//Vérification de l'existence des variables requises
			if(isset($_POST['nouveau_nom_video']) && isset($_POST['idvideo']))
			{
				if($_POST['nouveau_nom_video'] != "" AND $_POST['nouveau_nom_video'] != " ")
				{
					//Mise à jour de la video
					$sql = "UPDATE galerie_video SET nom_video = ? WHERE ID = ? AND ID_user = ?";
					
					//Exécution de la requête
					$requete = $bdd->prepare($sql);
					$requete->execute(array($_POST['nouveau_nom_video'], $_POST['idvideo'], $_SESSION['ID'])); 
					
					echo "OK. Fini.";
				}
			}
			
			//Fermeture du fichier
			die();
		}
		//Change le niveau de visibilité d'un post
		elseif($action == "14")
		{
			//Sécurité
			$verification_visibilite = 1;
			
			//Appel du fichier externe
			include("inc/change_niveau_visibilite_post.php");
			
			//Fermeture du fichier
			die();
		}
		//Suppression d'un commentaire
		elseif($action == "15")
		{
			//Sécurité
			$verification_supp_commentaire = 1;
			
			//Appel du fichier externe
			include("inc/supp_commentaire_id.php");
			
			//Fermeture du fichier
			die();
		}
		//Aime ou pas un commentaire
		elseif($action == "16")
		{
			//Sécurité
			$verification_edit_like_commentaire = 1;
			
			//Inclusion du fichier
			include("inc/like_comment.php");
			
			//Fermeture du fichier
			die();
		}
		//Afficher/Masquer le panneau du chat privé
		elseif($action == "17")
		{
			//Sécurité
			$verification_show_hide_private_chat = 1;
			
			//Inclusion du fichier
			include("inc/show_hide_private_chat_contener.php");
			
			//Fermeture du fichier
			die();
		}
		//Changer la hauteur du chat
		elseif($action == "18")
		{ 
			$verification_change_auteur_chat = 1; 
			include("inc/change_hauteur_chat.php");
		}
		//Ouvrir/Fermer une session de chat
		elseif($action == "19")
		{
			$ouvre_ferme_session_chat = 1; 
			include("inc/ouvre_ferme_session_chat.php");
		}
		//Récupérer le code source pour une session de chat
		elseif($action == "20")
		{
			$code_session_private_chat = 1;
			include('inc/code_session_private_chat.php');
		}
		//Aimer ou ne plus aimer un texte ou une page
		elseif($action == "21")
		{
			$like_texte_page = 1;
			include('inc/like_texte_page.php');
		}
		//Recherche AJAX d'utilisateurs
		elseif($action == "22")
		{
			$ajax_search_users = 1;
			include('inc/ajax_search_users.php');
		}
		//Requête de suppression de demande à devenir ami
		elseif($action == "23")
		{
			$delete_request_become_friend = 1;
			include('inc/delete_request_become_friend.php');
		}
		//Suppression des anciennes notifications
		elseif($action == "24")
		{
			$delete_old_notifications = 1;
			include('inc/delete_old_notifications.php');
		}
		//Suppression des anciennes notifications
		elseif($action == "25")
		{
			$gere_abonnement_ami = 1;
			include('inc/gere_abonnement_ami.php');
		}
		//Changement d'avatar en utilisant la webcam
		elseif($action == "26")
		{
			$change_avatar_webcam = 1;
			include('inc/change_avatar_webcam.php');
		}
		//Suppression de l'avatar ou de l'image de fond
		elseif($action == "27")
		{
			$delete_avatar_img_background = 1;
			include('inc/delete_avatar_img_background.php');
		}
		//Maintenance du compte
		elseif($action == "28")
		{
			$ok_maintenance = 1;
			include('inc/effectuer_maintenance_account.php');
		}
		//Code source de l'ajout de posts sur une page
		elseif($action == "32")
		{
			include('inc/pages/homeUser/addPost/addpost.inc.php');
		}
		//Message relatif au fil d'actualité bien vu
		elseif($action == "35")
		{
			$message_fil_vu = 1;
			include('inc/message_fil_vu.php');
		}
		//Affiche toute les conversations récentes du le chat privé pour permettre l'ouverture d'une nouvelle
		elseif($action == "36")
		{
			$ok_for_recent_private_chat = 1;
			include('inc/conversations_private_chat.php');
		}
		//Permet de voter pour un sondage (ou de l'annuler)
		elseif($action == "37")
		{
			$ok_to_vote_sondage = 1;
			include('inc/vote_sondage.php');
		}
	}