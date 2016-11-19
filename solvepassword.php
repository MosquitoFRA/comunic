<?php
//	Récupération de mot de passe	//
//		Service Pierre 2014			//
//		Fichier confidentiel		//
// 	Ne pas révéler son contenu	 	//

//Démarrage de la session
session_start();

//Init page
include('inc/initPage.php');

//Formulaire de contact
function forumlairecontact($mail, $nom, $prenom){
	$output = "<p>Essayez de nous contacter : (la premi&egrave;re phrase ne doit pas &ecirc;tre enlev&eacute;e ou modifi&eacute;e. Sinon votre demande ne pourrait aboutir.)</p>";
	$output .= '<form action="contact.php" method="post">';
	$output .= '<textarea name="contact" cols="50" rows="10" noresize>Bonjour, j\'ai perdu mon mot de passe mais je n\'ai pas défini ou oublié mes questions de sécurité.'."\n";
	$output .= 'Mon pr&eacute;nom : '.$prenom."\n"; 
	$output .= 'Mon nom : '.$nom."\n";
	$output .= "Merci de m'envoyer dans ma boite mail un mot de passe de confirmation qui me permettra de prouver que je suis le l&eacute;gal possesseur de ce compte.";
	$output .= '</textarea><br /><input type="hidden" name="mail" value="'.$mail.'" />';
	$output .= "<input type='hidden' name='id_type' value='5' />";
	$output .= "<input type='submit' value='Envoyer' />";
	$output .= '</form>';
	
	//On renvoi le formulaire
	return $output;
}

//On vérifie si il faut changer d'adresse mail
if(isset($_GET['change_email']))
{
	unset($_SESSION['solvepassword_email']);
}

?><!DOCTYPE html>
<html>
	<head>
		<title>R&eacute;cup&eacute;ration de votre mot de passe</title>
		<?php include(pagesRelativePath('common/head.php')); ?>
	</head>
	<body>
		<?php include(pagesRelativePath('common/pageTop.php')); ?>
		<?php 
		
		//On vérifie si la personne est connectée ou non
		if(isset($_SESSION['ID']))
			affiche_message_erreur("Vous &ecirc;tes connect&eacute;. Cette page ne pr&eacute;sente aucun int&eacute;r&ecirc;t pour les personnes connect&eacute;es. Vous pouvez <a href='index.php'>retourner &agrave; la page d'acceuil</a>.");
		
		//On vérifie si il existe un nouveau mot de passe en attente
		/* Structure : $_SESSION['reset_password'] = array(
							'ID' => $infopersonne['ID'],
							'mail' => $infopersonne['mail'],
							'new_password' => $new_password
						); */
		if(isset($_SESSION['reset_password']) OR (isset($_GET['id']) AND isset($_GET['new_password'])))
		{
			//On vérifie si il y a une demande d'annulation
			if(isset($_GET['cancel']))
			{
				unset($_SESSION['reset_password']);
				die("<p class='acceuil_solve_password'><a href='".$_SERVER['PHP_SELF']."?change_email'>Continuer vers la page d'acceuil de r&eacute;initialisation de mot de passe</a></p>");
			}
			
			//On vérifie si on a reçu le mot de passe
			if(isset($_POST['new_password']) OR isset($_GET['new_password']))
			{
				//Si le mot de passe provient du formulaire
				if(isset($_POST['new_password']))
					$_GET['new_password'] = crypt(sha1($_POST['new_password']), sha1($_POST['new_password']));
			
				//Récupération des informations de l'utilisateur
				$infos_user = cherchenomprenom((isset($_SESSION['reset_password']['ID']) ? $_SESSION['reset_password']['ID'] : $_GET['id']*1), $bdd);
				
				if($_GET['new_password'] == $infos_user['new_password'])
				{
					//Mise à jour du mot de passe
					//Enregistrement de la modification dans la base de donnée
					$sql = "UPDATE utilisateurs SET new_password = ?, password = ? WHERE ID = ?";
					$modif = $bdd->prepare($sql);
					
					if($modif->execute(array("", $_GET['new_password'], $infos_user['ID'])))
					{
					
						//Message de succès
						echo "<p class='acceuil_solve_password'> Le mot de passe a &eacute;t&eacute; r&eacute;initialis&eacute;. <a href='connecter.php'>Connexion</a></p>";
						unset($_SESSION['reset_password']);
						die();
					
					}
					else
					{
						$erreur = "Une erreur a survenue lors de la mise &agrave; jour de la base de donn&eacute;es, veuillez rafra&icirc;chir la page.";
					}
				}
				else
				{
					$erreur = "Mot de passe incorrect.";
				}
			}
			
			?><div class='acceuil_solve_password'>
				
				<?php
					//Recherche de la présence d'erreur
					if(isset($erreur))
						echo "Erreur: ".$erreur." <br />";
				?>
				
				Veuillez maintenant saisir le nouveau mot de passe envoy&eacute; dans votre bo&icirc;te mail :
				
				<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
					<input type="password" name="new_password" placeholder="Mot de passe" /> <input type="submit" value="Valider" />
				</form>
				
				<a href="<?php echo $_SERVER['PHP_SELF']; ?>?cancel">R&eacute;initaliser le mot de passe d'un autre compte</a>
			</div><?php
			exit();
			
		}
		
		//On vérifie si une demande de mail a été envoyée
		if(isset($_GET['mail']) AND isset($_GET['prenom']) AND isset($_GET['nom']) AND isset($_GET['id']) AND isset($_GET['date_create']) AND ($active_envoi_mail == "oui"))
		{
			$last_change_password_mail = (isset($_SESSION['changed_password']) ? $_SESSION['changed_password'] : 0);
			if($last_change_password_mail > strtotime("-2 hours"))
			{
				echo "<p>Erreur : Vous ne pouvez pas r&eacute;initialiser plusieurs fois un mot de passe en moins de 48H.</p>";
			}
			elseif($_GET['mail'] != "")
			{
				//Définition des variables
				$mail = $_GET['mail'];
				$prenom = $_GET['prenom'];
				$nom = $_GET['nom'];
				$id = base64_decode($_GET['id'])*1;
				$date_creation_compte = base64_decode($_GET['date_create']);
				
				if($id > 0)
				{
					//Recherche des informations sur le compte
					$infopersonne = cherchenomprenom($id, $bdd, 'ID', "<p class='acceuil_solve_password'>Compte sp&eacute;cifi&eacute; inexistant. <a href=".$_SERVER['PHP_SELF']." title='Resaisir l\'adresse mail'>R&eacute;essayer</a></p>");
					
					//Contrôle des informations
					if($infopersonne['mail'] == $mail AND $infopersonne['prenom'] == $prenom AND $infopersonne['nom'] == $nom AND $infopersonne['date_creation'] == $date_creation_compte AND $infopersonne['ID'] == $id)
					{
						//Génération du nouveau mot de passe
						$new_password = crypt($infopersonne['mail'].$infopersonne['prenom'].$infopersonne['nom'].$infopersonne['ID'].$infopersonne['date_creation'].$infopersonne['password'].time().(isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : "no_remote_addr").(isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : "no_user_agent"));
						$new_password = crypt($new_password, $new_password);
						$new_password = substr(str_replace(array('$', '/', "\\", "."), "", $new_password), 0, 8);

						//Enregistrement du nouveau mot de passe dans la session
						$_SESSION['reset_password'] = array(
							'ID' => $infopersonne['ID'],
							'mail' => $infopersonne['mail'],
							'new_password' => $new_password
						);
						
						
						//Le nouveau mot de passe étant généré, on l'enregistre
						$motdepasse = sha1($new_password);
						$motdepasse = crypt($motdepasse, $motdepasse);
						
						//Enregistrement de la modification dans la base de donnée
						$sql = "UPDATE utilisateurs SET new_password = ? WHERE ID = ?";
						$modif = $bdd->prepare($sql);
						$modif->execute(array($motdepasse, $infopersonne['ID']));
						
						//On envoi un mail
						//Vérification de l'autorisation d'envoi de mails
						if($active_envoi_mail == "oui")
						{
							//Envoi d'un message au demandé
							$send_mail = true;
							$sujet = "Modification de votre mot de passe";
							$description_rapide = "Vous avez réinitialisé votre mot de passe.";
							$nom_destinataire = $infopersonne['prenom']." ".$infopersonne['nom'];
							$adresse_mail_destinataire = $infopersonne['mail'];
							
							$texte_message = "
							<h3 class='titre'>R&eacute;initialisation de votre mot de passe</h3>
							<p>Vous avez r&eacute;cemment demand&eacute; une r&eacute;initialisation de mot de passe. Voici quel est votre nouveau mot de passe : <b>".$new_password."</b>.
							Nous vous recommandons de <a href='".$urlsite."connecter.php?redirect=".urlencode('parametres.php?c=password')."'> changer votre mot de passe </a> afin d'en d&eacute;finir un plus facile &agrave; retenir par vous.</p>
							
							<p><b>Important :</b> Afin de valider ce nouveau mot de passe, <a href='".$urlsite."solvepassword.php?new_password=".urlencode($motdepasse)."&id=".$infopersonne['ID']."'> cliquez ici </a>.</p>
							
							<p><strong>Important : Si vous n'avez pas r&eacute;initialis&eacute; votre mot de passe, ignorez ce message.</p>
							<p><a href='".$urlsite."'>Connectez-vous</a> pour acc&eacute;der &agrave; toute les param&egrave;tres de Comunic.</a></p>
							";
							
							//Envoi du message
							include('inc/envoi_mail.php');
							
							///On indique qu'un mot de passe a été changé
							$_SESSION['changed_password'] = time();
							
							//Message de succès
							echo "<p class='acceuil_solve_password'>Un nouveau mot de passe a &eacute;t&eacute; envoy&eacute; dans votre bo&icirc;te mail. <a href='solvepassword.php'>Valider le mot de passe</a></p>";
							exit();
						}
					}
					else
					{
						echo "<p class='acceuil_solve_password'>Informations s&eacute;cifi&eacute;es incorrectes. <a href=".$_SERVER['PHP_SELF']." title='Resaisir l\'adresse mail'>R&eacute;essayer</a></p>";
						die();
					}
				}
			}
		}
		
		
		//Recherche de l'adresse mail
		if(!isset($_SESSION['solvepassword_email']))
		{
			if(isset($_POST['solvepassword_email']))
			{
				$infopersonne = cherchenomprenom($_POST['solvepassword_email'], $bdd, 'mail', "<p class='acceuil_solve_password'>L'adresse mail sp&eacute;cifi&eacute;e est incorrecte. <a href=".$_SERVER['PHP_SELF']." title='Resaisir l\'adresse mail'>R&eacute;essayer</a>");
				
				?><table class="info_user_solve_password">
					<tr>
						<td>
							<?php echo avatar($infopersonne['ID'], './', 128, 128); ?>
						</td>
						<td>
							<h3><?php echo $infopersonne['prenom'].' '.$infopersonne['nom']; ?></h3>
						</td>
					</tr>
					<tr>
						<td>R&eacute;cup&eacute;ration <br /> de mot de passe</td>
						<td>
							<?php
								if($infopersonne['question1'] != '' && $infopersonne['reponse1'] != '' && $infopersonne['question2'] != '' && $infopersonne['reponse2'] != '')
								{
									//Enregistrement de l'adresse mail
									$_SESSION['solvepassword_email'] = $_POST['solvepassword_email'];
									
									//Message de succès
									echo "<p>Nous allons maintenant pouvoir vous r&eacute;tablir un mot de passe fonctionnel. <a href='".$_SERVER['PHP_SELF']."'>Continuer avec les questions de s&eacute;curit&eacute;</a></p>";
								}
								else
								{
									//Message d'erreur
									echo "<p>Impossible de d&eacute;finir un nouveau mot de passe en utilisant les questions de s&eacute;curit&eacute; pour ce compte car elles n'ont pas &eacute;t&eacute; d&eacute;finies. ";
									if($active_envoi_mail == "non")
									{
										echo "<a href='".$_SERVER['PHP_SELF']."'>R&eacute;essayer</a></p>";
										echo forumlairecontact($infopersonne['mail'], $infopersonne['nom'], $infopersonne['prenom']);
									}
								}
								
								if($active_envoi_mail == "oui") {
									echo "<p><a href='".$_SERVER['PHP_SELF']."?mail=".urlencode($infopersonne['mail'])."&prenom=".urlencode($infopersonne['prenom'])."&nom=".urlencode($infopersonne['nom'])."&id=".urlencode(base64_encode($infopersonne['ID']))."&date_create=".urlencode(base64_encode($infopersonne['date_creation']))."' title='Envoyer un mail' >";
									echo code_inc_img(path_img_asset('mail_green.png'), "", "", "", "vertical-align: middle;");
									echo " Envoyer un mail de r&eacute;initialisation de mot de passe";
									echo "</a></p>";
								}
							?>
						</td>
					</tr>
				</table><?php
				die();
			}
			
			?><div class="acceuil_solve_password">
				<h2><?php echo code_inc_img(path_img_asset('set_password.png')); ?> R&eacute;cup&eacute;ration du mot de passe</h2>
				<p>Bienvenue dans l'assistant de r&eacute;cup&eacute;ration de mot de passe. Veuillez v&eacute;rifier que vous disposez de vos questions de s&eacute;curit&eacute;. Nous allons commencer par vous demander votre adresse mail afin de vous identifier.</p>
				<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method='post'>
					Adresse mail : <input type='mail' name='solvepassword_email' /> <input type='submit' value='Envoyer' />
				</form>
			</div>
			<?php
			die();
		}
		
		//Préparation de l'affichage des questions
		?><div class="questions_solve_password"><?php
		
		//On recherche les informations de la personne
		$infopersonne = cherchenomprenom($_SESSION['solvepassword_email'], $bdd, "mail", "<p>Une erreur est survenue. Veuillez r&eacute;essayer ult&eacute;rieurement ou actualiser la page.</p>");
		
		if(isset($_POST['reponse1']) && isset($_POST['reponse2']))
		{
			//On vérifie les réponses
			if((strtoupper($_POST['reponse1']) == strtoupper($infopersonne['reponse1'])) && (strtoupper($_POST['reponse2']) == strtoupper($infopersonne['reponse2'])))
			{
				//On modifie le mot de passe
				$motdepasse = sha1($infopersonne['mail']);
				$motdepasse = crypt($motdepasse, $motdepasse);
				
				//Enregistrement de la modification dans la base de donnée
				$sql = "UPDATE utilisateurs SET password = ? WHERE ID = ?";
				$modif = $bdd->prepare($sql);
				$modif->execute(array($motdepasse, $infopersonne['ID']));
				
				//On connecte la personne
				$_SESSION['ID'] = $infopersonne['ID'];
				
				echo "<p>F&eacute;licitations! les r&eacute;ponses donn&eacuteses sont correctes! Votre compte est accessible. Le mot de passe est d&eacute;sormais votre adresse mail. <a href='parametres.php?c=password'>Cliquez ici pour d&eacute;finir un nouveau mot de passe.</a> ou <a href='index.php'>Cliquez ici pour acc&eacute;der &agrave; votre page d'acceuil.</a></p>";
				die();
			}
			else
			{
				echo "<p>La r&eacute;ponse 1 et/ou la r&eacute;ponse 2 est/sont incorrectes. Vous pouvez : soit compl&eacute;ter &agrave; nouveau les questions de s&eacute;curit&eacute;, soit nous contacter afin que nous r&eacute;initilasions votre mot de passe.</p>";
				echo forumlairecontact($infopersonne['mail'], $infopersonne['prenom'], $infopersonne['nom']);
			}
		}
		else
		{
			//Message de bienvenue dans les questions
			?><p><b><?php echo avatar($infopersonne['ID'], './'); echo $infopersonne['prenom'].' '.$infopersonne['nom']; ?></b>, veuillez maintenant r&eacute;pondre aux questions de s&eacute;curit&eacute; suivantes : (Le syst&egrave;me ne prend pas en compte la casse : les majuscules et les minuscules peuvent &ecirc;tre librement utilis&eacute;es)</p><?php
		}
		//On donne les questions de sécurité
		?>
			<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
				<table>
					<tr>
						<td><?php echo $infopersonne['question1']; ?></td>
						<td><input type='text' name='reponse1' value="<?php if(isset($_POST['reponse1'])) echo $_POST['reponse1']; ?>" /></td>
					</tr>
					<tr>
						<td><?php echo $infopersonne['question2']; ?></td>
						<td><input type='text' name='reponse2' value="<?php if(isset($_POST['reponse2'])) echo $_POST['reponse2']; ?>" /></td>
					</tr>
					<tr>
						<td></td>
						<td><input type='submit' value='Valider' />
					</tr>
				</table>
			</form>
			
			<!-- Message pour changer l'adresse mail -->
			<p>Ce n'est pas votre adresse mail ? <a href="<?php echo $_SERVER['PHP_SELF']; ?>?change_email=1">Cliquez ici</a>.</p>
		</div>
		<hr />
		<?php include(pagesRelativePath('common/pageBottom.php')); ?>
	</body>
</html>
