<?php
/**
 * Change general settings
 *
 * @author Pierre HUBERT
 */

if(!isset($_SESSION))
	exit("Invalid call !");

?><div class="metro general_settings">
	<h3><?php echo $lang[56]; ?></h3>
	<?php
		//Vérification de l'existence d'une demande de vérification
		if((isset($_POST['nom'])) && (isset($_POST['prenom'])) && (isset($_POST['public'])))
		{
			if(!isset($_POST['affiche_chat']))
			{
				$_POST['affiche_chat'] = $afficher['affiche_chat'];
			}
		
			//On vérifie si la page est ouverte
			if (isset($_POST['pageouverte']) && $_POST['public'] == '1')
			{
				//On rend la page ouverte
				$pageouverte = 1;
			}
			else
			{
				//Sinon elle est fermée
				$pageouverte = 0;
			}
			
			//On vérifie si les commentaires doivent être bloqués
			if (isset($_POST['bloquecommentaire']))
			{
				//On bloque les commentaires
				$bloquecommentaire = 1;
			}
			else
			{
				//Sinon ils sont accessibles
				$bloquecommentaire = 0;
			}
			
			//On vérifie si les posts en provenance des amis sont autorisés
			$autoriser_post_amis = (isset($_POST['autoriser_post_amis']) ? 1 : 0);
			
			//On vérifie si l'envoi de mail est autorisé
			$autorise_mail = (isset($_POST['autorise_mail']) ? 1 : 0);
			
			//On vérifie si la liste d'amis est publique
			$liste_amis_publique = (isset($_POST['liste_amis_publique']) ? 1 : 0);
			
			//On vérifie si l'on doit structurer les posts en plusieurs  pages
			$mode_pages = (isset($_POST['mode_pages']) ? 1 : 0);
			
			//On vérifie si l'on doit autoriser le système de multiauthentification pour ce compte ou non
			$allow_multilogin = (isset($_POST['allow_multilogin']) ? 1 : 0);
			
			//Sécurité pour l'adresse URL
			$site_web = str_replace('javascript:', '', $_POST['site_web']);
			
			//Modification de la base de données
			$sql = "UPDATE utilisateurs SET nom = ?, prenom = ?, affiche_chat = ?, public = ?, pageouverte = ".$pageouverte.", site_web = ?, question1 = ?, reponse1 = ?, question2 = ?, reponse2 = ?, bloquecommentaire = ?, autoriser_post_amis = ?, autorise_mail = ?, liste_amis_publique = ?, mode_pages = ?, allow_multilogin = ? WHERE ID = ?";
			$modif = $bdd->prepare($sql);
			$modif->execute(array($_POST['nom'], $_POST['prenom'], $_POST['affiche_chat'], $_POST['public'], $site_web, $_POST['question1'], $_POST['reponse1'], $_POST['question2'], $_POST['reponse2'], $bloquecommentaire, $autoriser_post_amis, $autorise_mail, $liste_amis_publique, $mode_pages, $allow_multilogin, $_SESSION['ID']));
			
			//Vérification de l'autorisation d'envoi de mails
			if($active_envoi_mail == "oui")
			{
				//Envoi d'un message au demandé
				$send_mail = true;
				$sujet = "Modification de vos paramètres";
				$description_rapide = "Les paramètres de votre compte ont été modifiés.";
				$nom_destinataire = $afficher['prenom']." ".$afficher['nom'];
				$adresse_mail_destinataire = $afficher['mail'];
				
				//Rechargement des informations
				$afficher = cherchenomprenom($_SESSION['ID'], $bdd); 
				
				$texte_message = "
				<h3 class='titre'>Modification de vos param&egrave;tres</h3>
				<p>Voici les nouveau param&egrave;tres de votre compte:</p>
				<table align='center'>
					<tr><td>ID </td><td>".$afficher['ID']."</td></tr>
					<tr><td>Pr&eacute;nom :</td><td>".$afficher['prenom']."</td></tr>
					<tr><td>Nom :</td><td>".$afficher['nom']."</td></tr>
					<tr><td>Adresse mail :</td><td>".$afficher['mail']."</td></tr>
					<tr><td>Page publique :</td><td>".($afficher['public'] == 1 ? "Oui" : "Non")."</td></tr>
					<tr><td>Page ouverte :</td><td>".($afficher['pageouverte'] == 1 ? "Oui" : "Non")."</td></tr>
					<tr><td>Structurer les posts en plusieurs pages :</td><td>".($afficher['mode_pages'] == 1 ? "Oui" : "Non")."</td></tr>
					<tr><td>Autoriser Comunic &agrave; m'envoyer des mails</td><td>".($afficher['autorise_mail'] == 1 ? "Oui" : "Non")."</td></tr>
					<tr><td>Adresse de votre site web (optionnel)</td><td>".$afficher['site_web']."</td></tr>
					<tr><td>Autoriser la gestion de ce compte depuis un autre compte ? </td><td>".($afficher['allow_multilogin'] == 1 ? "Oui" : "Non")."</td></tr>
					<tr><td>Question de s&eacute;curit&eacute; 1:</td><td>".$afficher['question1']."</td></tr>
					<tr><td>R&eacute;ponse de s&eacute;curit&eacute; 1:</td><td>".$afficher['reponse1']."</td></tr>
					<tr><td>Question de s&eacute;curit&eacute; 2:</td><td>".$afficher['question2']."</td></tr>
					<tr><td>R&eacute;ponse de s&eacute;curit&eacute; 2:</td><td>".$afficher['reponse2']."</td></tr>
				</table>
				".$info_mail_securite."
				<p><strong>Important : Si vous n'avez pas chang&eacute; vos param&egrave;tres, modifiez votre mot de passe et contactez-nous. Il se peut que quelqu'un ait pirat&eacute; votre compte et r&eacute;cup&eacute;r&eacute; votre mot de passe.</strong></p>
				<p><a href='".$urlsite."'>Connectez-vous</a> pour acc&eacute;der &agrave; toute les param&egrave;tres de Comunic.</a></p>
				";
				
				//Envoi du message
				include(websiteRelativePath().'inc/envoi_mail.php');
			}
			
			//Actualisation de la page
			echo "Enregistrement des modification termin&eacute;, actualisation de la page....";
			echo '<meta http-equiv="refresh" content="0;URL=parametres.php">';
			die();
		}
	?>
	<form action='<?php echo $_SERVER['PHP_SELF']; ?>' method='post' id="general">
	<?php
		/*//Recherche des informations personelles
		$sql = "SELECT * utilisateurs WHERE ID='".$_SESSION['ID']."'";
		//Exécution de la requete
		$requete = $bdd->query($sql);
		
		//Recherche d'éventuelles erreurs
		if(!$afficher = $requete->fetch())
		{
			echo "<p>Un problème technique est survenu. Merci de réessayer plus tard.</p>";
			die();
		}
		
		//Fermeture de la requete
		$general->closeCursor();*/
	?><legend>Informations de connexion</legend>
	<label>Num&eacute;ro de compte et <?php echo $lang[14]; ?></label>
	<div class="input-control text size2">
		<input type="text" disabled value="<?php echo $afficher['ID']; ?>" />
	</div>
	<div class="input-control text size4">
		<input type="text" disabled value="<?php echo $afficher['mail']; ?>" />
	</div>
	
	<label><?php echo $lang[12]; ?> et <?php echo $lang[13]; ?></label>
	<div class="input-control text size3">
		<input type='text' name='prenom' value='<?php echo $afficher['prenom']; ?>' />
	</div>
	<div class="input-control text size3">
		<input type='text' name='nom' value='<?php echo $afficher['nom']; ?>' />
	</div>
	
	<label><?php echo $lang[1]; ?></label>
	<div class="input-control">
		<a href='<?php echo $_SERVER['PHP_SELF']; ?>?c=password' title='Cliquez ici pour modifier le mot de passe'><?php echo $lang[58]; ?></a>
	</div>
	
	<p>&nbsp;</p>
	
	<legend>Personnalisation g&eacute;n&eacute;rale du compte</legend>
	<?php if($activer_publique_chat == "oui") { ?><label>Affichage du chat automatique</label><div class="input-control select"><select name='affiche_chat'><option <?php if ($afficher['affiche_chat'] == 1) echo "selected"; ?> value='1' />Oui <option <?php if ($afficher['affiche_chat'] == 0) echo "selected"; ?> value='0' />Non</select></div><?php } ?>
		
	<label>Votre page est... <a href='aide.php?id=6' target='_blank'><?php echo code_inc_img(path_img_asset('help.png')); ?></a></label>
	<div class="input-control select size6">
		<select name='public'>
			<option <?php if ($afficher['public'] == 1) echo "selected"; ?> value='1' />Publique 
			<option <?php if ($afficher['public'] == 0) echo "selected"; ?> value='0' />Privee
		</select>
	</div>
	
	<?php // On affiche cette ligne uniqument si la page est publique
		if($afficher['public'] == 1) 
		{ 
			?><div class="input-control switch">
				<label>
					<input type='checkbox' <?php
						if ($afficher['pageouverte'] == 1) 
							echo 'checked'; 
				?>  name='pageouverte' /><span class="check"></span> Page ouverte <strong>Attention: Cette option rend votre page accessible aux personnes non connect&eacute;es!</strong>
				</label>
			</div><?php
		} 
	?>
	
	<?php /* On affiche cette ligne uniqument si la page est ouverte */ if ($afficher['pageouverte'] == 1) echo '<p>Votre page est accessible &agrave; cette adresse : '.$urlsite.'?id='.$afficher['ID'].'</p>';?>
		
	<div class="input-control switch">
		<label>
			<input type='checkbox' name='bloquecommentaire' <?php if ($afficher['bloquecommentaire'] == 1) echo 'checked'; ?> />
			<span class="check"></span>
			Interdire le post de commentaires sur ma page
		</label>
	</div><br />
	
	<div class="input-control switch">
		<label>
			<input type='checkbox' name='autoriser_post_amis' <?php if ($afficher['autoriser_post_amis'] == 1) echo 'checked'; ?> />
			<span class="check"></span>
			Autoriser les posts provenant de mes amis
		</label>
	</div><br />
	
	<div class="input-control switch">
		<label>
			<input type='checkbox' name='mode_pages' <?php if ($afficher['mode_pages'] == 1) echo 'checked'; ?> />
			<span class="check"></span>
			Structurer mes posts en plusieurs pages (En d&eacute;veloppement)
		</label>
	</div><br />
	
	<div class="input-control switch">
		<label>
			<input type='checkbox' name='autorise_mail' <?php if ($afficher['autorise_mail'] == 1) echo 'checked'; ?> />
			<span class="check"></span>
			Autoriser Comunic &agrave; m'envoyer des mails (ne concerne pas les mails de s&eacute;curit&eacute;, qui sont syst&eacute;matiquement envoy&eacute;s.)
		</label>
	</div><br />
	
	<!-- Liste d'amis publique -->
	<div class="input-control switch">
		<label>
			<input type='checkbox' name='liste_amis_publique' <?php if ($afficher['liste_amis_publique'] == 1) echo 'checked'; ?> />
			<span class="check"></span>
			Rendre ma liste d'amis publique
		</label>
	</div><br />
	<!-- Fin de: Liste d'amis publique -->
	
	<!-- Autorisation de gérer ce compte depuis un autre compte -->
	<div class="input-control switch">
		<label>
			<input type='checkbox' name='allow_multilogin' <?php if ($afficher['allow_multilogin'] == 1) echo 'checked'; ?> />
			<span class="check"></span>
			Autoriser ce compte a &ecirc;tre g&eacute;r&eacute; depuis un autre compte (multi-authentification)
		</label>
	</div><br />
	<!-- Fin de: Autorisation de gérer ce compte depuis un autre compte -->
	
	<p>&nbsp;</p>
	
	<legend>Site web (optionnel)</legend>
	<label>Adresse URL</label>
	<div class="input-control text size4">
		<input type='text' name='site_web' placeholder="http://" value='<?php echo $afficher['site_web']; ?>' />
	</div>
	
	<p>&nbsp;</p>
	
	<legend>Pr&eacute;paration &agrave; l'oubli de mot de passe</legend>
	<label>
		Question de s&eacute;curit&eacute; 1 

		<!-- Help -->
		<a href='aide.php?id=7' title="En savoir plus..." target='_blank'> <?php echo code_inc_img(path_img_asset('help.png')); ?></a> 

		<small>Ne faites pas attention aux majuscules/minuscules.</small>
	</label>
	<div class="input-control text size3">
		<input type='text' name='question1' placeholder="Question 1" value='<?php echo echap_guillemet_html($afficher['question1']); ?>' />
	</div>
	<div class="input-control text size3">
		<input type='text' name='reponse1' placeholder="R&eacute;ponse 1" value='<?php echo echap_guillemet_html($afficher['reponse1']); ?>' />
	</div>
	
	<label>Question de s&eacute;curit&eacute; 2 <small>Ne faites pas attention aux majuscules/minuscules.</small></label>
	<div class="input-control text size3">
		<input type='text' name='question2' placeholder="Question 2" value='<?php echo echap_guillemet_html($afficher['question2']); ?>' />
	</div>
	<div class="input-control text size3">
		<input type='text' name='reponse2' placeholder="R&eacute;ponse 2" value='<?php echo echap_guillemet_html($afficher['reponse2']); ?>' />
	</div>
	
	<label><input type='submit' value='<?php echo $lang[57]; ?>' /></label>
	
	<label><span id="ciblesuppressioncompte"><h6><a onClick="confirmdeleteaccount()" title="Supprimer votre compte">Supprimer votre compte</a></h6></span></label>
	</table>
	</form>
</div><?php