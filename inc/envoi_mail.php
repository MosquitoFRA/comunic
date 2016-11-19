<?php
	//Sécurité
	if(!isset($send_mail))
		die();

	//Chemin relatif
	if(!isset($add_path))
	{
		$add_path = "";
	}

	//Inclusion de la configuration
	include_once($add_path.'inc/config/config.php');
	
	//Préparation de l'envoi de mail
	//->préliminaire
	if(!isset($config_path_defined))
	{
		define('CONFIG_PATH', $add_path.'config.php');
		$config_path_defined = true;
	}
	
	//Vérification de l'existence de l'autorisation d'envoi de mail
	if($active_envoi_mail == "oui")
	{
		//Paramètres d'envoi de mails
		//$nom_destinataire : Nom et prénom du destinataire 
		//$texte_message : Code source HTML du message
		//$adresse_mail_destinataire : adresse mail du destinataire
		//$sujet : Sujet du message
		
		//Préparation de l'envoi
		$message_html = "<!DOCTYPE html>
		<html>
			<head>
				<title>Comunic</title>
				<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
				<style type='text/css'>
					body {margin:0px;padding:0px;background:#ffffff;font-family:arial, helvetica, sans-serif;} 
					#top {margin:0px;padding:0px;background:#0066CC; color: #FFFFFF; width:100%; } 
					#top .container { padding:0px 0px 0px 0px; height: 23px; }
					#top .container a {color: white; text-decoration: none; }
					.titre { text-align: center; }
				</style>
			</head>
			<body>
				<div id='top'> 
					<div class='container'>
						<strong><a href='".$urlsite."'>Comunic</a></strong>
					</div>
				</div>
				
				".$texte_message."
				
				<h6 style='text-align: center'>Merci de ne pas r&eacute;pondre, ce message a &eacute;t&eacute; envoy&eacute; automatiquement. 
					Pour changer les param&egrave;tres relatifs &agrave; l'envoi de mail,
					<a href='".$urlsite."connecter.php?redirect=parametres.php'>
						cliquez ici
					</a>.
				</h6>
			</body>
		</html>";
		
		//Vérification de l'existence d'un forcage de redirection
		if($force_redirection_mails == "oui")
		{
			$adresse_mail_destinataire = $adresse_mail_redirection;
		}
		
		//On vérifie quelle est la méthode d'envoi de mail
		if($methode_envoi_mail == "mail()")
		{
			//Utilisation de la fonction mail()
			//Définition des en-têtes
			$headers   = array();
			$headers[] = "MIME-Version: 1.0";
			$headers[] = "Content-type: text/html; charset=iso-8859-1";
			$headers[] = "From: ".$nom_mail_expediteur." <".$mail_envoi.">";
			$headers[] = "X-Mailer: PHP/".phpversion();
			
			//Envoi du mail
			mail($adresse_mail_destinataire, $sujet, wordwrap($message_html, 70), implode("\r\n", $headers));
		}
		else
		{
			//Utilisation de PHPMailer
			//SMTP needs accurate times, and the PHP time zone MUST be set
			//This should be done in your php.ini, but this is how to do it if you don't have access to that
			date_default_timezone_set('Etc/UTC');
			
			require_once relativePath_3rdparty('phpmailer/PHPMailerAutoload.php');

			//Create a new PHPMailer instance
			$mail = new PHPMailer();
			//Tell PHPMailer to use SMTP
			$mail->isSMTP();
			//Enable SMTP debugging
			// 0 = off (for production use)
			// 1 = client messages
			// 2 = client and server messages
			$mail->SMTPDebug = 0;
			//Ask for HTML-friendly debug output
			$mail->Debugoutput = 'html';
			//Set the hostname of the mail server
			$mail->Host = $adresse_serveur_mail_envoi;
			//Set the SMTP port number - likely to be 25, 465 or 587
			$mail->Port = $port_serveur_mail_envoi;
			
			//Vérification de la nécessité de connexion
			if($active_login_envoi_mail == "oui")
			{
				//Whether to use SMTP authentication
				$mail->SMTPAuth = true;
				//Username to use for SMTP authentication
				$mail->Username = $mail_envoi;
				//Password to use for SMTP authentication
				$mail->Password = $password_mail_envoi;
			}
			else
			{
				//Whether to don't use SMTP authentication
				$mail->SMTPAuth = false;
			}
			
			//Set who the message is to be sent from
			$mail->setFrom($mail_envoi, $nom_mail_expediteur);
			//Set who the message is to be sent to
			$mail->addAddress($adresse_mail_destinataire, $nom_destinataire);
			//Set the subject line
			$mail->Subject = $sujet;
			//Read an HTML message body from an external file, convert referenced images to embedded,
			//convert HTML into a basic plain-text alternative body
			$mail->msgHTML($message_html, dirname(__FILE__));
			//Replace the plain text body with one created manually
			$mail->AltBody = (isset($description_rapide) ? $description_rapide :'Ce message vous est envoyé de la part de '.$nom_mail_expediteur);
			//Attach an image file
			//$mail->addAttachment('images/phpmailer_mini.png');

			//send the message, check for errors
			if (!$mail->send()) {
				$statut = "Mailer Error: " . $mail->ErrorInfo;
			} else {
				$statut = "Message sent!";
			}
		}
	}
	else
	{
		$statut = "Erreur: l'envoi de mail est interdit.";
	}

	//echo (isset($statut) ? $statut : "Statut inconnu.");
?>