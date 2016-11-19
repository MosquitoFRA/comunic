<?php
//Fonctions du service Pierre
//D�velopp� pour Comunic 
//D�but du d�veloppement : fin 2013
//Tous droits r�serv�s. 
	
//Avertissement :  ce fichier g�n�r� automatiquement est n�cessaire au bon fonctionnement de la base de donn�es et des fonctions.

//Configuration d'acc�s � la base de donn�es
$hotedb = "localhost"; //H�te de la base de donn�es
$nomdb = "comunic"; //Nom de la base de donn�es
$userdb = "root"; //Nom utilisateur
$passworddb = "root"; //Mot de passe pour acc�der acc�s � la base de donn�es
	
//Configuration du site
$urlsite = "http://devweb.local/comunic/current/"; //Url du site
$forceHttps = false; //Forcer ou non la connexion securisee (en HTTPS) du site
$preferenceredirection = "headerphp"; //Pr�f�rence de redirection : HTML ou PHP
$mode_site = "offline"; //Mode du site : en ligne ou offline
$last_update_website = "1476950918"; //Nombre de secondes depuis la derni�re mise � jour du site
$alert_last_update_website = "1"; //P�riode suivant la mise � jour duant laquelle un avertissement sera affich�
$jqueryonline = ""; //Adresse de jQuery en ligne
$affichebeta = 0; //On affiche ou nom le message d'avertissement sur la page principale
$textebeta = array("fr" => ""); //Texte qui est affich� si le message de beta est affich� sur index.php (fran�ais)
$textebeta["en"]=""; //Texte qui est affich� si le message de beta est affich� sur index.php (anglais)
$textecookie["fr"]="Attention : Certaines fonctionnalités comme le niveau de visibilité des posts personnalisé ne sont pas encore entièrement disponibles."; //Texte qui est affich� en page d'acceuil qui avertis pour la cr�ation de cookies (fran�ais)
$textecookie["en"]="Warning : There is some undone functionalities in this website such as the visibility of the posts"; //Texte qui est affich� en page d'acceuil qui avertis pour la cr�ation de cookies (anglais)
$affichemessageclassique = 0; //Afficher ou non le message d'avertissement classique
$bloque_site_for_update = 0; //Bloquer ou non le site pour effectuer une mise � jour
$complementsource = ""; //Compl�ment de source pour le fichier header.php
$activer_publique_chat = "non"; //Activer ou non le chat publique
$activer_cache = "non"; //Activer ou non la mise en cache et la r�duction des ressources
$bloque_clic_droit = "non"; //Intercepter ou non le chat clic droit de la souris
$active_services = "oui"; //Activer ou non les services: 'oui' ou 'non'
$active_gestion_404 = "oui"; //Activer ou non la gestion des erreurs 404 par le fichier index.php: 'oui' ou 'non'
$active_gestion_appareil_mobile = "non"; //Activer ou non la gestion des des appareils mobiles par le fichier index.php: 'oui' ou 'non'
$ID_official_page = "198"; //ID de la page officielle
$ID_actuality_page = "198"; //ID de la page d'actualite
$comment_size_limit = "2555"; //Taille maximum des commentaires, en nombre de caract�res

//Protection de la cr�ation de compte
$activcreationcompte = "nopass"; //Permet d'activer, de d�sactiver ou de prot�ger par mot de passe la cr�ation de compte
$passwordcreataccount = 'daOkAtJI0O5Cg'; //Mot de passe de protection de compte si demand�. Est stock� crypt� pour plus de s�curit�.
$messagecreateaccountdenied = ""; //Message a afficher en cas de refus de cr�ation de compte
$activimagevalidation = "1"; //Affiche ou non l'image de validation de cr�ation de compte. (image de s�curit�)

//Configuration de Piwik
$enable_piwik = "0"; //Activer ou non le suivi Piwik
$id_site_piwik = "1"; //ID du site dans l'interface de Piwik
$adresse_piwik = "//communiquons.org/piwik"; //Adresse du site Piwik (doit �tre compl�te et commencer par // (pas de http: ou https:))

//Facebook API
$active_facebook_api = "non"; //Activer ou non les APIS FACEBOOK pour la cr�ation de compte
$id_app_facebook = ""; //ID de l'appplication Facebook
$facebook_api_key = ""; //Cl�  des API Facebook

//Envoi de mails
$active_envoi_mail = "oui"; //Activation de l'envoi de mails
$methode_envoi_mail = "mail()"; //M�thode d'envoi de mail (PHPMailer ou mail())
$active_login_envoi_mail = "oui"; //Activation de l'authentification pour l'envoi de mail
$mail_envoi = "pierre@mail.local"; //Adresse mail de l'exp�diteur
$admin_mail_envoi = "pierre@mail.local"; //Adresse mail de l'administration
$mail_envoi_erreur_sql = "pierre@mail.local"; //Adresse mail de l'envoi de bug relatif aux requ�tes SQL
$password_mail_envoi = "1478965"; //Mot de passe de l'exp�diteur
$adresse_serveur_mail_envoi = "localhost"; //Adresse du serveur mail d'envoi
$port_serveur_mail_envoi = "25"; //Port du serveur mail d'envoi
$nom_mail_expediteur = "Comunic (communiquons.org)"; //Nom pour les envoi de mail
$info_mail_securite = "Ce mail est un mail relatif &agrave; la s&eacute;cirt&eacute; de votre compte, c'est pourquoi il vous est adress&eacute; m&ecirc;me si vous avez refus&eacute; l'envoi de mails."; //Message � afficher dans tous les mails relatifs � la s�curit� du compte

//Forcer le changement d'adresses mail (redirection)
$force_redirection_mails = "oui"; //Activation ou non de la redirection
$adresse_mail_redirection = "pierre@mail.local"; //Adresse mail de redirection

//Administration
$plus_securite_admin = "oui"; //Renforcer la s�curit� � la connexion de l'administration
$enable_log_admin = "oui"; //Activer la surveillance de l'activit� de l'administration

//Support du SSL
if(isset($_SERVER['HTTPS']))
{
	if($_SERVER['HTTPS'] == "on")
		//Modification de http:// en https:// dans l'URL du site
		$urlsite = str_replace('http://', 'https://', $urlsite);
}
elseif($forceHttps)
	header("location: https://".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]);
?>