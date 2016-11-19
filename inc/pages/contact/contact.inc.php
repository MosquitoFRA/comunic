<?php
/**
 * Contact administration page
 *
 * @author Pierre HUBERT
 */

isset($_SESSION) OR exit("Invalid call - ".$_SERVER['PHP_SELF']);

//Crypt inclusion
$cryptinstall=path_3rdparty("crypt/cryptographp.fct.php");
include $cryptinstall; 
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Contact - Comunic</title>
		<?php include(pagesRelativePath('common/head.php')); ?>
	</head>
	<body class="metro">
		<?php include(pagesRelativePath('common/pageTop.php')); ?>
		<h1 class='titre'>Contact</h1>
		<div class="nouveau_corps_page"><?php
			//Récupération de la liste des types de contact
			$liste_type_contact = get_list_type_contact($bdd);
			
			//Si il s'agit d'une personne connectée, on liste des contacts disponibles
			if(isset($_SESSION['ID']))
			{
				//On vérifie si il faut supprimer un contact
				if(isset($_GET['delete_contact']))
				{
					//Enregistrement du numéro de contact
					$id_contact = $_GET['delete_contact']*1;
					
					//Vérification du numéro de contact
					if($id_contact > 0)
					{
						//On supprime le contact
						$sql = "DELETE FROM contact WHERE ID = ? AND ID_personne = ?";
						
						//Exécution de la requête
						$requete = $bdd->prepare($sql);
						$requete->execute(array($id_contact, $_SESSION['ID']));
					}
				}
				
				?><h3>Anciennes prises de contact</h3>
					<?php
						//Requête de recherche
						$sql = "SELECT * FROM contact WHERE ID_personne = ? ORDER BY ID";
						$requete = $bdd->prepare($sql);
						$requete->execute(array($_SESSION['ID']));
						
						//Affichage des résultats
						while($afficher_contact = $requete->fetch())
						{
							?><div class="notice marker-on-right bg-cobalt fg-white one_contact">
								<?php echo $liste_type_contact[$afficher_contact['ID_type']-1]["nom_".$lang['nomlangue_raccourcis']]; ?><br />
								<small><?php echo adapte_date($afficher_contact['date_envoi']); ?> (<a href="#" onClick="confirmaction('<?php echo siteURL('contact.php')."?delete_contact=".$afficher_contact['ID']; ?>', 'Voulez-vous vraiment supprimer cette prise de contact ? Ce choix est definitif !');">supprimer</a>)</small><br /><br />
								
								<?php echo $afficher_contact['texte']; ?>
							</div><br /><?php
							
							//On indique qu'un contact a été afficher
							$one_contact_showed = true;
						}
						
						//Fermeture de la requête
						$requete->closeCursor();
						
						//On vérifie si aucun contact n'a été affiché
						if(!isset($one_contact_showed))
						{
							?><div class="notice marker-on-right bg-cobalt fg-white one_contact">
								Aucune prise de contact pour l'instant
							</div><?php
						}
						?>
				<?php
			}
		
			if(isset($_POST['texte']) AND isset($_POST['id_type']))
			{
				if($_POST['texte'] == "")
				{
					//Message d'erreur
					echo('Vous n\'avez pas sp&eacute;cifi&eacute; de contact !');
				}
				else
				{
					if(!isset($_SESSION['ID']))
					{
						//On vérifie notre image :)
						if(!isset($_POST['codeimage']) && !isset($_POST['mail']))
						{
							echo "Ce formulaire de contact ne doit pas provenir d'une autre ressource que Comunic.";
							die();
						}
						elseif(!chk_crypt($_POST['codeimage']))
						{
							echo "<p>Le contact n'a pas pu &ecirc;tre enregistr&eacute; pour la raison suivante : Code de l'image de validation incorrect.</p>";
							die();
						}
						elseif($_POST['mail'] == "")
						{
							echo "<p>Le contact n'a pas pu &ecirc;tre enregistr&eacute; pour la raison suivante : Erreur de l'adresse mail.</p>";
							die();
						}
					}
					
					//On vérifie le type de contact
					if(!isset($liste_type_contact[$_POST['id_type']-1]))
					{
						die("<p>Raison de prise de contact ind&eacute;terminable.</p>");
					}
					
					//On définit le compte de celui qui poste la demande
					$user = 0;
					if(isset($_SESSION['ID']))
					{
						$user = $_SESSION['ID'];
					}
					
					$texte = $_POST['texte'];
					
					if(isset($_POST['mail']))
					{
						if($_POST['mail'] != "")
						{
							$texte = $_POST['texte']." <br />  Adresse mail de l'utilisateur non connect&eacute; : ".$_POST['mail'];
						}
					}
					
					//Enregistrement de l'adresse IP
					if(isset($_SERVER['REMOTE_ADDR']))
					{
						$texte = $texte." <br />  Adresse IP de l'ordinateur ayant envoy&eacute; le contact : ".$_SERVER['REMOTE_ADDR'];
					}
					
					//On poste la demande
					$sql = "INSERT INTO contact (ID_personne, date_envoi, texte, ID_type, mail_personne, IP_personne) VALUES (".$user.", NOW(), ?, ?, ?, ?)";
					
					//Exécution de la requete
					$insertion = $bdd->prepare($sql);
					$insertion->execute(array($texte, $_POST['id_type'], (!isset($_SESSION['ID']) ? $_POST['mail'] : ""), $_SERVER['REMOTE_ADDR']));
					
					//On affiche un message de succès
					echo "<p>".code_inc_img(path_img_asset('succes.png'), "Bravo !")." La demande a bien &eacute;t&eacute; prise en compte. ".($active_envoi_mail == "oui" ? "Un message de confirmation vous sera envoy&eacute;</p>" : '')."</p>";
					echo "<p>Voici le texte de la demande : <pre>".$texte."</pre></p>";
					echo "<p>Voici la raison de la prise de contact : <pre>".$liste_type_contact[$_POST['id_type']-1]["nom_".$lang['nomlangue_raccourcis']]."</pre></p>";
					
					//Envoi du message à l'utilisateur
					//Vérification de l'autorisation de l'envoi d'un mail
					if($active_envoi_mail == "oui")
					{
						//Envoi d'un message de confirmation
						$send_mail = true;
						$sujet = "Confirmation de demande de contact";
						$description_rapide = "Votre demande de prise de contact Comunic a été enregistrée.";
						$nom_destinataire = "Vous";
						$adresse_mail_destinataire = (isset($_SESSION['ID']) ? $afficher['mail'] : $_POST['mail']);
						$texte_message = "
						<h3 class='titre'>Contact</h3>
						<p>Votre demande de contact a &eacute;t&eacute; prise en compte. Voici quelle est votre demande :</p>
						<pre>".$texte."</pre>
						<p>Type de demande :</p>
						<pre>".$liste_type_contact[$_POST['id_type']-1]["nom_fr"]."</pre>
						<p>Nous vous r&eacute;pondrons dans les plus bref d&eacute;lais.</p>
						<p><a href=".$urlsite.">Connectez-vous à Comunic</a> pour renvoyer un contact si n&eacute;cessaire.
						</p>";
						
						//Envoi du message
						include('inc/envoi_mail.php');
					}
					
					//Envoi du message à l'administration
					//Vérification de l'autorisation de l'envoi d'un mail
					if($active_envoi_mail == "oui")
					{
						//Envoi d'un message de confirmation
						$send_mail = true;
						$sujet = "Demande de contact";
						$description_rapide = "Un utilisateur de Comunic a demandé à entrer en contact avec vous.";
						$nom_destinataire = "Administration de Comunic";
						$adresse_mail_destinataire = $admin_mail_envoi;
						$texte_message = "
						<h3 class='titre'>Contact</h3>
						<p>Un utilisateur de Comunic a souhait&eacute; entrer en contact avec l'administration. Voici le texte de la demande :</p>
						<pre>".$texte."</pre>
						<p>Veuillez r&eacute;pondre &agrave; ce contact dans les plus bref d&eacute;lais.</p>
						<p>Connectez-vous &agrave; l'administration pour obtenir plus de d&eacute;tails.
						</p><h4 class='titre'>
						D&eacute;tails techniques:
						</h4><table>
							<tr><td>La personne est connect&eacute;e :</td>
							<td>".(isset($_SESSION['ID']) ? "Oui" : "Non")."</td></tr><tr><td>
							Adresse mail :</td>
							<td>".(isset($_SESSION['ID']) ? $afficher['mail'] : $_POST['mail'])."</td></tr>
							<tr><td>ID compte</td>
							<td>".$user."</td></tr>
							<tr><td>Type de contact</td>
							<td>".$liste_type_contact[$_POST['id_type']-1]["nom_fr"]."</td></tr>
						</table>";
						
						//Envoi du message
						include('inc/envoi_mail.php');
					}
				}
			}
			else
			{
				//On affiche le formulaire de contact
				?><form action='<?php echo siteURL('contact.php'); ?>' method='post' name='contact' class="form_contact">
					<h3> Contactez-nous </h3>
					<p>Vous pouvez utiliser le formulaire de contact pour nous proposer une am&eacute;lioration ou nous faire une demande.</p>
						<table>
							</tr>
								<td>La raison de cette prise de contact </td>
								<td><select name="id_type"><?php
									//On détermine quel type de contact sera sélectionné par défaut
									$type_contact = (isset($_POST['id_type']) ? $_POST['id_type'] : 1 );
								
									//Affichage de la liste des propositions
									foreach($liste_type_contact as $afficher_type_contact)
									{
										echo "<option value='".$afficher_type_contact['ID']."' ".($type_contact == $afficher_type_contact['ID'] ? "selected" : "")."> ".$afficher_type_contact['nom_'.$lang['nomlangue_raccourcis']];
									}
								?></select></td>
							</tr><tr>
								<td>
									Votre message
								</td>
								<td>
									<textarea name='texte'><?php if(isset($_POST['contact'])) echo $_POST['contact']; ?></textarea>
							</tr><?php
							if(!isset($_SESSION['ID']))
							{
								//Petite vérification de sécurité, au cas ou... :)
								?><tr>
									<td>Image de validation :</td>
									<td style="text-align: center"><?php dsp_crypt(0,1); ?></td>
								</tr><tr>
									<td>Code de l'image</td>
									<td><input type="text" name="codeimage"></td>
								</tr>
								<tr>
									<td>Votre adresse mail</td>
									<td><input type="mail" name="mail" <?php if(isset($_POST['mail'])) echo "value='".$_POST['mail']."'"; ?> required/></td>
								</tr><?php
							}
							?>
							<tr>
								<td colspan="2"><h6>Afin de pouvoir d&eacute;terminer avec pr&eacute;cision qui a r&eacute;ellement envoy&eacute;<br />
								ce texte en cas de probl&egrave;me grave, votre adresse IP est enregistr&eacute;e.</h6></td>
							</tr>
							<tr>
								<td>
									Confirmer l'envoi
								</td>
								<td>
									<input type='submit' value='Envoyer' />
								</td>
							</tr>
							<tr>
								<td colspan="2"><h6>En cliquant sur "Envoyer" vous reconaissez &ecirc;tre<br />
								le l&eacute;gitime possesseur de l'adresse mail <i><?php if(isset($_SESSION['ID'])) echo $afficher['mail']; else echo "sp&eacute;cifi&eacute;e ci-dessus"; ?></i> .</h6></td>
							</tr>
						</table>
					</form>
				<?php
			}
			?>
			</div><hr />
			<?php include(pagesRelativePath('common/pageBottom.php')); ?>
	</body>
</html>