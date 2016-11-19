<?php
/**
 * Private chat of the project
 *
 * @author Pierre HUBERT
 */

//Safety
include('securite.php');

//Intialisising file
include('inc/initPage.php');
	
//International
$lang = detecteinstallelangue();
	
//Info:
	// Table :
		// Name: chatprive
		// Content :
			// ID : Unique ID of the post
			// date_envoi : Date of the post
			// ID_personne : Who has sent the post
			// ID_destination : To who is sent the post
			// contenu : content of the post
		//Is accessible by privatechat.php
		//See Pierre Hubert for more informations.s

//Get ready
$headerprivatechat = code_inc_js(path_js_asset('private_chat.js')).
					 code_inc_js(path_js_asset('grand_dialogue_smiley.js')).
					 code_inc_js(path_js_asset('liste_smile.js')).
					 code_inc_css(path_css_asset('privatechat.css')).
					 code_inc_css(path_css_asset('grand_dialogue_smiley.css')).
					 code_inc_css(path_css_asset('metro-bootstrap_2.css'));

//Informations de l'utilisateur connecté
$afficher = cherchenomprenom($_SESSION['ID'], $bdd);

if(!isset($_GET['id']))
{
	echo "<b>Error : OK for chat but with who ?</b>";
	die();
}
else
{
	//Définition de l'ID de l'autre personne
	$id_other = $_GET['id']*1;
}

if($_SESSION['ID'] == $id_other)
{
	echo "<b>Error: You can't chat with you !</b>";
	die();
}
elseif($id_other == 0)
{
	//Redirection vers la page de choix d'un session private chat
	header('Location: '.$urlsite.'action.php?actionid=36');
	
	//echo "<b>Error : File stopped for security reasons.</b>";
	die();
}
	
if(!isset($_GET['ajax']))
{
	//Définition du temps
	$time = time();
	
?><!DOCTYPE html>
<html>
	<head>
		<title><?php echo $lang[65]; ?></title>
		<?php echo $headerprivatechat; ?>
		
		<script type="text/javascript">
			//Script interne de la page web
			//Redimensionnement automatique du champ de texte (c) Comunic - Tous réusage nécessite une autorisation écrite de Pierre HUBERT
			//Initialisation
			/*function autoresize_textarea_init(id_area, max_size)
			{
				if(!id_area)
					console.log("Erreur: veuillez indiquer l'ID de la zone SVP");
				else
				{
					//Vérification de la présence de la taille maximale
					if(!max_size)
					{
						console.log("Avertissement: taille du champ de textarea non definie. Par defaut: 255");
						max_size = 255;
					}
					
					
					console.log("Initialisation de l'auto-resize...")
					
					document.getElementById(id_area).onKeyUp = function() {
						alert('ok !');
					}
				}
			}*/
			
			function autoresize_textarea(textarea, max_size)
			{

				//Vérification de la présence de la taille maximale
				if(!max_size)
				{
					console.log("Avertissement: taille du champ de textarea non definie. Par defaut: 255");
					max_size = 255;
				}
				
				//Détermination de la longueur du texte actuelle
				var longueur = textarea.value.length;
				var width_textarea = textarea.width;
				
				//Determination du nombre de ligne
				var nb_lignes = longueur/max_size;
				
				if(nb_lignes <= 1)
					nb_lignes = 1;
				else if(nb_lignes <= 2)
					nb_lignes = 2;
				else if(nb_lignes <= 3)
					nb_lignes = 3;
				else if(nb_lignes <= 4)
					nb_lignes = 4;
				else
					nb_lignes = 5;
				
				//Hauteur du textarea
				var height_textarea = 8 + (nb_lignes*16) + "px";
				
				//Application de la nouvelle hauteur du textarea
				if(textarea.style.height != height_textarea)
					textarea.style.height = height_textarea;
				
				//console.log("Longueur du texte : " + longueur + " Longueur du textarea : " + width_textarea + " Nombre de lignes : " + nb_lignes + " => Hauteur du textarea : " + height_textarea);
			}
		</script>
		
		<style type="text/css">
			.autoresize {
				overflow: hidden;
				word-wrap: break-word;
				resize: none;
			}
		</style>
	</head>
	<body class="metro">
		<?php
		//Form to add a content
		?><!-- Private chat Form -->
		<div class="form_send_message">
			<textarea name="message" class="autoresize" id="message" onkeyup="autoresize_textarea(this, 22);"></textarea>
			<!--<script>autoresize_textarea_init('message', 255);</script>-->
			<?php echo code_inc_img(path_img_asset('smiley/smile_gris.gif'), "Smileys", "", "", "", "ouvre_grande_liste_smiley('message');", $class = "open_grande_boite_dialogue_smiley"); ?>
			<input type="button" value="<?php echo $lang[31]; ?>" onclick="submitPrivateChat('<?php echo $_SERVER['PHP_SELF']; ?>?refresh=last&id_window=<?php echo $time; ?>&ajax=1&id=<?php echo $id_other; ?>', 'contentpost');" />
		</div><br />
		<!--End of: Private chat Form -->
		
		<!-- Song -->
		<audio id="new_mp">
			<source src="<?php echo path_audio_asset('new_mp.mp3'); ?>" />
			<source src="<<?php echo path_audio_asset('new_mp.ogg'); ?>" />
		</audio>
		<!-- End of: Song --><?php
						
		//Preparing the chat
			?><table id="contentpost" width="90%"></table><?php //A table for receive all the posts of the user.
			?><script type="text/javascript">
				//First refresh of the private chat
				getinIDajax('<?php echo $_SERVER['PHP_SELF']; ?>?ajax=1&id=<?php echo $id_other; ?>&id_window=<?php echo $time; ?>', 'contentpost'); 
				
				//Javascript for launch the automated refresh of the chat (all 4 seconds)
				var timerprivatechat=setInterval("getinIDajax('<?php echo $_SERVER['PHP_SELF']; ?>?ajax=1&id=<?php echo $id_other; ?>&refresh=last&id_window=<?php echo $time; ?>', 'contentpost')", 4000);
			</script><?php
		
		//Ending page
		?></body>
		</html><?php
}
else
{
	//If requested, we save a message 
	if(isset($_POST['message']))
	{
				if($_POST['message'] != "")
				{
					//We save message
					save_private_chat_message($_SESSION['ID'], $id_other, $bdd, $_POST['message']);
					
					//On vérifie si le système d'envoi de mail est activé
					if($active_envoi_mail == "oui")
					{
						//On affiche une notification à l'utilisateur
						if(sendnotification_one_user($_SESSION['ID'], $id_other, "vous a envoy&eacute; un message dans le chat priv&eacute;.", $bdd, "private_chat:".$_SESSION['ID']))
						{
							//On vérifie si la personne est connectée ou non
							if(determine_si_personne_connecte($id_other, $bdd) == false)
							{
						
								//Récupération des informations de la personne
								$informations = cherchenomprenom($id_other, $bdd);
								
								//On vérifie si la personne autorise le post de mail
								if($informations['autorise_mail'] == 1)
								{
									//Envoi d'un mail pour prévenir la personne
									//Envoi d'un message de confirmation
									$send_mail = "";
									$nom_destinataire = $informations['prenom']." ".$informations['nom'];
									$sujet = "Nouveau message dans le chat privé de ".$afficher['prenom']." ".$afficher['nom'];
									$description_rapide = "Un message vous a été envoyé de la part de ".$afficher['prenom']." ".$afficher['nom'];
									$adresse_mail_destinataire = $informations['mail'];
									$texte_message = "
									<h3 class='titre'>Message dans le chat priv&eacute;</h3>
									<p>Un message vient de vous &ecirc;tre adress&eacute; par ".$afficher['prenom']." ".$afficher['nom']." dans le chat priv&eacute; de Comunic.</p>
									<p>Voici l'historique de votre discussion dans Comunic :</p>
									<table>";
									
									//Get the content of the chat
									$content_chat = get_content_private_chat($_SESSION['ID'], $id_other, $bdd, 15, "*", $urlsite);
									
									//Show content of the private chat
									foreach($content_chat as $show)
									{
										//We show the content of the chat
										$texte_message .= "<tr><td>";
										
										if($show['ID_personne'] == $_SESSION['ID'])
										{
											$texte_message .= $afficher['prenom']." ".$afficher['nom']." : ".corrige_caracteres_speciaux(corrige_accent_javascript($show['contenu'])); 
										}
										else
										{
											$texte_message .= "Vous : ". corrige_caracteres_speciaux(corrige_accent_javascript($show['contenu'])); 
										}
										
										$texte_message .= "</td></tr>";
									}
									
									$texte_message .="</table>";
									
									
									$texte_message .= "<p><a href='".$urlsite."'>Connectez-vous &agrave; Comunic</a> afin d'acc&eacute;der &agrave; toute les notifications</a>
									</p>";
									
									//Envoi du message
									include('inc/envoi_mail.php');
								}
							}
						}
					}
				}
	}
	
	//Définition du numéro de fenêtre
	$id_window = (isset($_GET['id_window']) ? $_GET['id_window'] : "default");
	
	//On vérifie quel temps il faut accorder au chat privé
	if(isset($_SESSION['window_private_chat'][$_SESSION['ID']][$id_other." - ".$id_window][0]) AND isset($_GET['refresh']))
	{
		if($_SESSION['window_private_chat'][$_SESSION['ID']][$id_other." - ".$id_window][0] > 0)
		{
			//On définit le temps
			$last_id_post = $_SESSION['window_private_chat'][$_SESSION['ID']][$id_other." - ".$id_window][0][0];
		}
	}
	
	if(!isset($_SESSION['window_private_chat'][$_SESSION['ID']][$id_other." - ".$id_window][0]))
	{
		$_SESSION['window_private_chat'][$_SESSION['ID']][$id_other." - ".$id_window][0] = array(1);
	}
	
	//Get the content of the chat
	$content_chat = get_content_private_chat($_SESSION['ID'], $id_other, $bdd, 15, (isset($last_id_post) ? $last_id_post : "*"));
	
	//On vérifie si c'est nécessaire de continuer
	if(count($content_chat) == 0)
		exit('0');
	
	//On met tous les messages en vu
	mettre_en_vu_private_chat($_SESSION['ID'], $id_other, $bdd);
	
	//Show content of the private chat
	foreach($content_chat as $show)
	{
		//On met à jour le dernier post vu si possible
		if((isset($_SESSION['window_private_chat'][$_SESSION['ID']][$id_other." - ".$id_window][0][0]) ? $_SESSION['window_private_chat'][$_SESSION['ID']][$id_other." - ".$id_window][0][0] : 0) < $show['ID'])
		{
			//On met à jour la variable
			$id_post = $show;
			$_SESSION['window_private_chat'][$_SESSION['ID']][$id_other." - ".$id_window][0] = $id_post;
		}
		
		//We show the content of the chat
		echo "<tr><td>";
				
		if($show['ID_personne'] == $_SESSION['ID'])
		{
			?><div class="notice marker-on-right bg-lightBlue fg-white">
				<?php echo afficher_lien($show['contenu']); ?>
			</div>
			<?php

		}
		else
		{
			?><div class="notice marker-on-left bg-lightOlive fg-white">
				<?php echo afficher_lien($show['contenu']); ?>
			</div>
			<?php
		}
				
				echo "</tr>";
	}
}

