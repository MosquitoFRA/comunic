<?php
/**
 * Manage multiauth 
 *
 * @author Pierre HUBERT
 */

if(!isset($_SESSION))
	exit("Invalid call !");
	
	//On a besoin de l'ID du compte parent pour permettre l'exécution de cette page !
	if(!isset($_SESSION['ID_parent']))
	{
		//Rapport d'erreur à l'administration
		report_error('if(!isset($_SESSION[\'ID_parent\']))', 'La variable $_SESSION[\'ID_parent\'] n\'existe pas, elle est n&eacute;cessaire &agrave; multi_login_settings.php (inc).');
		
		//Affichage d'un message d'erreur
		affiche_message_erreur("Votre navigateur n'a pas bien &eacute;t&eacute; connect&eacute; &agrave; Comunic. Veuillez essayer de vous d&eacute;connecter et vous reconnecter et r&eacute;essayer.");
		
		//Fermeture
		exit();
	}
		
	if(!isset($bdd))
		die('An error occured while accessing the BDD !');
	
	//On vérifie si il faut retourner au compte principal
	if(isset($_GET['back_main_account']))
	{
		//Attribution de l'ID_parent à l'ID
		$_SESSION['ID'] = $_SESSION['ID_parent'];
		
		//Retour à la page d'acceuil du site
		echo "<script>location.href='".$urlsite."';</script>";
	}
	
	//On vérifie si il faut supprimer un compte
	if(isset($_GET['delete_entree']) AND isset($_GET['id']))
	{
		//Définition des variables
		$id_entree_to_delete = $_GET['delete_entree']*1;
		$id_personne_concernee_1 = $_GET['id']*1;
		$id_personne_concernee_2 = $_SESSION['ID_parent']*1;
		
		if($id_entree_to_delete > 0 AND $id_personne_concernee_1 > 0 AND $id_personne_concernee_2 >0)
		{
			//Suppression de la base de données
			delete_sql("multi_login", "ID = ? AND ((ID_personne = ? AND ID_target = ?) OR (ID_target = ? AND ID_personne = ?))", $bdd, array(
				$id_entree_to_delete, 
				$id_personne_concernee_1, 
				$id_personne_concernee_2,
				$id_personne_concernee_1,
				$id_personne_concernee_2
			));
			
			//Affichage d'un message de succès
			?><script>affiche_notification_succes("Le compte a bien &eacute;t&eacute; supprim&eacute; de la console de multi-authentification.");</script><?php
		}
		else
			//Afficher un message d'erreur
			affiche_message_erreur("Une erreur d'entr&eacute; a emp&ecirc;ch&eacute; la suppression d'avoir lieu !", true);
	}
	
	//On vérifie si il faut ajouter un compte
	if(isset($_POST['mail_new_account']) AND isset($_POST['pass_new_account']))
	{
		//On vérifie si les informations sont correctes
		$infos_user_add = connnecteruser($_POST['mail_new_account'], $_POST['pass_new_account'], $bdd, true, false, true);
		
		if(!$infos_user_add)
		{
			//Afficher un message d'erreur
			affiche_message_erreur("L'adresse mail ou le mot de passe du compte saisi est/sont incorrects !", true);
		}
		elseif($infos_user_add['allow_multilogin'] != "1")
		{
			//Afficher un message d'erreur
			affiche_message_erreur("Ce compte refuse la multi-authentification !", true);
		}
		elseif($infos_user_add['ID'] == $_SESSION['ID_parent'])
		{
			//Afficher un message d'erreur
			affiche_message_erreur("Vous ne pouvez pas ajouter votre propre compte dans le gestionnaire de multi-authentification !", true);
		}
		else
		{

			//On peut rajouter le comtpe à la liste des comptes connus
			insert_sql("multi_login", "ID_personne, ID_target, date_ajout, IP_ajout", "?, ?, NOW(), ?", $bdd, array(
				$_SESSION['ID_parent'],
				$infos_user_add['ID'],
				$_SERVER['REMOTE_ADDR']
			));
			
			//Vérification de l'autorisation d'envoi de mails
			if($active_envoi_mail == "oui")
			{
				//Envoi d'un message au demandé
				$send_mail = true;
				$sujet = "Modification de vos paramètres de multi-authentification";
				$description_rapide = "Les paramètres de multi-authentification de votre compte ont été modifiés.";
				$nom_destinataire = $infos_user_add['prenom']." ".$infos_user_add['nom'];
				$adresse_mail_destinataire = $infos_user_add['mail'];
				
				//Rechargement des informations
				$afficher = cherchenomprenom($_SESSION['ID_parent'], $bdd); 
				
				$texte_message = "
				<h3 class='titre'>Ajout de votre compte &agrave; la console de multi-authentification de ".$afficher['prenom']." ".$afficher['nom']."</h3>
				<p>Nous vous informons que votre compte a &eacute;t&eacute; ajout&eacute; &agrave; la console de multi-authentification de ".$afficher['prenom']." ".$afficher['nom'].". Si vous n'avez pas fait cette demande, prenez imm&eacute;diatement contact avec l'administration de Comunic pour rapporter ce probl&egrave;. ".$afficher['prenom']." ".$afficher['nom']." pourra d&eacute;sormais depuis son compte acc&eacute;der au v&ocirc;tre sans saisir votre mot de passe &agrave; chaque fois.</p>
				";
				
				//Envoi du message
				include(websiteRelativePath('inc/envoi_mail.php'));
			}
			
			//Affichage d'un message de succès
			?><script>affiche_notification_succes("Le compte a bien &eacute;t&eacute; ajout&eacute; &agrave; la console de multi-authentification!");</script><?php
		}
	}
	
	//Récupération de la liste des comptes qui gèrent le compte de l'utilisateur courant, connecté
	$liste_managed = select_sql("multi_login", "ID_target = ?", $bdd, array($_SESSION['ID_parent']));
	
	//Récupération de la liste des comptes qui peuvent être gérés par le compte de l'utilisateur courant, connecté
	$liste_accounts_user = select_sql("multi_login", "ID_personne = ?", $bdd, array($_SESSION['ID_parent']));
	
?><style type="text/css">
	.multi_account_listing img { vertical-align: middle; }
	
	.volet_droit_parametres {
		max-width: 90% !important;
		text-align: justify;
	}
</style>

<h3>Gestion de la multi-authentification</h3>
<p>La multi-authentification vous permet de g&eacute;rer plusieurs comptes rapidement en ne vous connectant qu'&agrave; un seul, le compte principal. Il vous est vivement conseill&eacute; de choisir un mot de passe s&ucirc;r pour le compte principal.</p>


<h4> Votre compte </h4>
<p> Multi-authentification autoris&eacute;e : <b><?php echo ($afficher['allow_multilogin'] == 1 ? "Autoris&eacute;e" : "Interdite"); ?></b> (Voir la section <i>G&eacute;n&eacute;ral</i>)</p>
<p> Ce compte peut &ecirc;tre g&eacute;r&eacute; par : </p>
	<?php
		if(count($liste_managed) == 0)
			echo "<p><i>Votre compte n'est actuellement rattach&eacute; &agrave; aucun compte pour le moment.</i></p>";
		else
		{
			?><div class="grid multi_account_listing"><?php
				foreach($liste_managed as $afficher_entree)
				{
					?><div class="row"><?php
						//Recherche des informations de la personne
						$infos_personne_manager = cherchenomprenom($afficher_entree['ID_personne'], $bdd);
						
						//Affichage de l'avatar
						echo avatar($infos_personne_manager['ID']);
						echo " ";
						
						//Affichage du prénom et du nom
						echo "<a href='index.php?id=".$infos_personne_manager['ID']."'>".$infos_personne_manager['nom_complet']."</a>";
						echo " ";
						
						echo "<img src='".path_img_asset('supp.png')."' class='a' title='Supprimer de la console' onClick='confirmaction(\"".$_SERVER['PHP_SELF']."?c=".$_GET['c']."&delete_entree=".$afficher_entree['ID']."&id=".$infos_personne_manager['ID']."\", \"Souhaitez-vous vraiment supprimer ".$infos_personne_manager['nom_complet']." de la liste des personnes pouvant acc&eacute;der &agrave; votre compte ?\");' />";
						
					?></div><?php
				}
			?></div><?php
		}
	?>
<h4>G&eacute;rer un compte</h4>
<p> Compte d&eacute;j&agrave; g&eacute;r&eacute;s :</p>

<p> Ce compte peut acc&eacute;der sans saisie de mots de passes aux comptes suivants : </p>
	<?php
		if(count($liste_accounts_user) == 0)
			echo "<p><i>Votre compte ne peut acc&eacute;der actuellement &agrave; aucun compte.</i></p>";
		else
		{
			?><div class="grid multi_account_listing"><?php
				foreach($liste_accounts_user as $afficher_entree)
				{
					?><div class="row"><?php
						//Recherche des informations de la personne
						$infos_personne_managed = cherchenomprenom($afficher_entree['ID_target'], $bdd);
						
						//On vérifie si il faut utiliser ce compte
						if(isset($_GET['use_account']))
						{
							if($_GET['use_account'] == $infos_personne_managed['ID'])
							{
								//Attribution de l'ID
								$_SESSION['ID'] = $infos_personne_managed['ID'];
								
								//Redirection vers la page d'acceuil
								//header('Location: '.$urlsite);
								echo '<meta http-equiv="refresh" content="0;URL=index.php">';
							}
						}
						
						//Affichage de l'avatar
						echo avatar($infos_personne_managed['ID']);
						echo " ";
						
						//Affichage du prénom et du nom
						echo "<a href='index.php?id=".$infos_personne_managed['ID']."'>".$infos_personne_managed['nom_complet']."</a>";
						echo " ";
						
						echo "<img src='".path_img_asset('supp.png')."' class='a' title='Supprimer de la console' onClick='confirmaction(\"".$_SERVER['PHP_SELF']."?c=".$_GET['c']."&delete_entree=".$afficher_entree['ID']."&id=".$infos_personne_managed['ID']."\", \"Souhaitez-vous vraiment supprimer ".$infos_personne_managed['nom_complet']." de la liste des personnes auxquelles vous pouvez acc&eacute;der depuis votre compte ?\");' />";
						
						echo "<a class='button' href='".$_SERVER['PHP_SELF']."?c=".$_GET['c']."&use_account=".$infos_personne_managed['ID']."'><i class='icon-enter'></i> Utiliser ce compte</a>";
						
					?></div><?php
				}
			?></div><?php
		}
	?>
	
	<!-- Ajout d'un compte pour la multi-authentification -->
	<p> Ajouter un compte : </p>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?c=<?php echo $_GET['c']; ?>" method="post">
		<label>Adresse mail, mot de passe du compte</label>
		<!-- Adresse mail -->
		<div class="input-control text size3">
			<input type="mail" required name="mail_new_account" placeholder="Adresse mail du compte..." />
		</div>
		
		<!-- Mot de passe -->
		<div class="input-control password size3">
			<input type="password" required name="pass_new_account" placeholder="Mot de passe du compte..." />
		</div>
		
		<!-- Bouton de validation -->
		<input type="submit" value="Ajouter le compte" />
	</form>
	<!-- Fin du formulaire d'ajout de compte -->