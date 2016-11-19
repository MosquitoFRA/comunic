<?php
/**
 * This page let the user create its account
 *
 * @author Pierre HUBERT
 */

//Initializate page
include('inc/initPage.php');

//Inclusion du crypt
$cryptinstall=path_3rdparty("crypt/cryptographp.fct.php");
include $cryptinstall; 

//On vérifie si l'utilisateur est connecté
if(isset($_SESSION['ID']))
{
	header('Location: index.php');
	die();
}

//On vérifie si il y a une demande de redirection
if(isset($_GET['redirect']) OR isset($_POST['redirect']))
	$redirect = (isset($_GET['redirect']) ? $_GET['redirect'] : $_POST['redirect']);

?>
<!DOCTYPE html>
<html id="new_account">
	<head>
		<title>Creation de compte</title>
		<?php include(pagesRelativePath('common/head.php')); ?>
	</head>
	<body class="metro">
	<?php include(pagesRelativePath('common/pageTop.php')); ?>
<?php
	//Correctif de sécurité
	if(!isset($_POST['validation']))
		$_POST['validation'] = false;

	//Si tous les champs ne sont pas disponibles, on fait comme si il n'y avait rien
	if((!isset($_POST['nom'])) || (!isset($_POST['prenom'])) || (!isset($_POST['mail'])) || (!isset($_POST['password'])) || (!isset($_POST['confirmpassword'])) || (($activimagevalidation == 1) && (!isset($_POST['validation']))))
	{
		//Rien
	}
	//Vérifions que tous les champs ont été remplis ainsi que la correspondance des mots de passe
	elseif(($_POST['nom'] == '') || ($_POST['prenom'] == '') || ($_POST['mail'] == '') || ($_POST['password'] == '') || ($_POST['confirmpassword'] == '') || (($activimagevalidation == 1) && ($_POST['validation'] == "")) || ($_POST['password'] != $_POST['confirmpassword']))
	{
		$erreur = "Tous les champs n'ont pas &eacute;t&eacute; remplis ou les mots de passe saisis ne sont pas les m&ecirc;mes .<br />";
	}
	//Vérifions si le code de l'image de validation est incorrect
	elseif(($activimagevalidation == 1) && (!chk_crypt($_POST['validation'])))
	{
		$erreur = "Le code de l'image de validation est incorrect.<br />";
	}
	else
	{
		//On prépare la vérification de sécurité
		$tableau_a_verifier = array($_POST['nom'], $_POST['prenom'], $_POST['mail']);
		
		//On l'exécute
		if(trouve_caractere_tableau($tableau_a_verifier, '<') || trouve_caractere_tableau($tableau_a_verifier, '>') || trouve_caractere_tableau($tableau_a_verifier, ';') || trouve_caractere_tableau($tableau_a_verifier, ',') || trouve_caractere_tableau($tableau_a_verifier, '{'))
		{	
			//Message d'erreur de sécurité
			$erreur = "Les caractères suivants sont interdits pour le nom, le prénom et l'adresse mail : > et < et ; et , et { ";
		}
		elseif(!filter_var($_POST['mail'], FILTER_VALIDATE_EMAIL))
		{
			//Message d'erreur: email incorrect
			$erreur = "L'adresse mail saisie est incorrecte.";
		}
		else
		{
			//On peut créer le compte 
			
			//Préparation des variables
			$nom = str_replace("'", '', $_POST['nom']);
			$prenom = str_replace("'", '', $_POST['prenom']);
			$mail = str_replace("'", '', $_POST['mail']);
			$password = sha1($_POST['password']);
			$password = crypt($password, $password);
			
			//Preparation de la requete SQL
			$sql = "INSERT INTO utilisateurs (nom, prenom, date_creation, mail, password) VALUES ('".$nom."', '".$prenom."', NOW(), '".$mail."', '".$password."') ";
			
			//Execution de la requete SQL
			$insertion = $bdd->query($sql);
			
			//Vérification de l'autorisation de l'envoi d'un mail
			if($active_envoi_mail == "oui")
			{
				//Envoi d'un message de confirmation
				$send_mail = true;
				$sujet = "Confirmation de cr&eacute;ation de compte";
				$description_rapide = "Confirmation de création de compte Comunic";
				$nom_destinataire = $prenom." ".$nom;
				$adresse_mail_destinataire = $mail_user = $mail;
				$texte_message = "
				<h3 class='titre'>F&eacute;licitations !</h3>
				<p>Votre compte a &eacute;t&eacute; bien cr&eacute;&eacute;. Vous pouvez d&egrave;s  maintenant utiliser Comunic et rechercher vos amis.</p>
				<p>Voici les informations g&eacute;n&eacute;rales de votre nouveau compte Comunic.</p>
				<table>
				<tr><td>Nom</td><td>".$nom."</td></tr>
				<tr><td>Pr&eacute;nom</td><td>".$prenom."</td></tr>
				<tr><td>Adresse mail</td><td>".$mail."</td></tr>
				<tr><td>Mot de passe</td><td>________________________</td></tr>
				</table>
				<p>Nous vous recommandons d'imprimer cette page et de compl&eacute;ter le champs \"Mot de passe\" puis de conserver la page imprim&eacute;e en lieu s&ucirc;r.
				</p>";
				
				//Envoi du message
				include('inc/envoi_mail.php');
			}
			
			//Confirmation de l'inscription
			//Message de succès
			$account_created_check = true;
			include('inc/account_created.php');
			
			//Fermeture de la page
			die('</body></html>');
		}
	}
	?><div class="nouveau_corps_page">
		<h2 class='titre'>Creation de compte</h2>
		<?php
			//Si nécessaire, message d'erreur
			if(isset($erreur)) affiche_message_erreur($erreur, true);
		?>
		<!-- Formulaire d'inscription -->
		<form action='<?php echo $_SERVER['PHP_SELF']; ?>' name='nouveaucompte' method='post' id="newaccount">
		
		<!-- Prénom de l'utilisateur -->
		<label>Pr&eacute;nom</label>
		<div class="input-control text">
			<input type='text' name='prenom' placeholder="Saisissez votre pr&eacute;nom" value='<?php echo (isset($_POST['prenom']) ? $_POST['prenom'] : ""); ?>'/>
		</div>
		<!-- Fin de: Prénom de l'utilisateur -->
		
		<!-- Nom de l'utilisateur -->
		<label>Nom</label>
		<div class="input-control text">
			<input type='text' name='nom' placeholder="Saisissez votre nom" value='<?php echo (isset($_POST['nom']) ? $_POST['nom'] : ""); ?>'/>
		</div>
		<!-- Fin de: Nom de l'utilisateur -->
		
		<!-- Adresse mail de l'utilisateur -->
		<label>Adresse mail <small><i class="icon-warning"></i> Attention : L'adresse mail ne pourra &ecirc;tre modifi&eacute;e ult&eacute;rieurement.</small></label>
		<div class="input-control text">
			<input type='mail' name='mail' id="mailarea" placeholder="Saisissez votre adresse mail" value='<?php echo (isset($_POST['mail']) ? $_POST['mail'] : ""); ?>'/>
		</div>
		<!-- Fin de: Adresse mail de l'utilisateur -->
		
		<!-- Mot de passe de l'utilisateur -->
		<label>Mot de passe</label>
		<div class="input-control password" data-role="input-control">
			<input placeholder="Saisissez votre mot de passe" name="password" type="password">
		</div>
		<!-- Fin de: Mot de passe de l'utilisateur -->
		
		<!-- Confirmer le mot de passe de l'utilisateur -->
		<label>Mot de passe</label>
		<div class="input-control password" data-role="input-control">
			<input placeholder="Saisissez &agrave; nouveau votre mot de passe pour le confirmer" name="confirmpassword" type="password">
		</div>
		<!-- Fin de: Confirmer le mot de passe de l'utilisateur -->
		
		<?php if ($activimagevalidation == 1) { ?>
		<!-- Image de validation -->
		<div style="text-align: center;">
			<?php dsp_crypt(0,1, false); ?>
		</div>
		<div class="input-control text">
			<input type='text' name='validation' placeholder="Recopiez le code de validation" />
		</div>
		<!-- Fin de: Image de validation -->
		<?php } ?>
		
		<!-- Bouton de confirmation -->
		<div style="text-align: center;"><input type='button' value='Creer le compte' onClick="if (validermail('mailarea')) document.getElementById('newaccount').submit();" /></div>
		<!-- Fin de: Bouton de confirmation -->
		
		<!-- Redirection automatique -->
		<?php
			if(isset($redirect))
				echo "<input type='hidden' name='redirect' value='".$redirect."' />";
		?>
		<!-- /Redirection automatique -->
		
		<a href='about.php?cgu' target='_blank'><?php echo $lang[46]; ?></a>
		</form>
	</div>
	<hr />
	<?php include(pagesRelativePath('common/pageBottom.php')); ?>
	<!-- Fin du formulaire d'insciption -->
	</body>
</html>