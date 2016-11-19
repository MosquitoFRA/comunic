<?php
//Démarrage de la session
session_start();

//Init page
include('inc/initPage.php');

//Initialisation de la langue
$lang = detecteinstallelangue();

if(isset($_POST['mail']))
{
	if($_POST['mail'] != "")
	{
		//On définit un cookie contenant l'adresse mail du client pour une connexion plus rapide
		setcookie('usermail', $_POST['mail'], time() + 365*24*3600, null, null, false, true);
	}
}

//On vérifie si l'utilisateur est connecté
if(isset($_SESSION['ID']))
{
	//On vérifie si il y a une redirection ou non
	if(isset($_GET['redirect']))
	{
		header('Location: '.$_GET['redirect']);
		exit();
	}
		
	
	header('Location: index.php');
	die();
}

//Connexion d'une erreur d'appel de ce script
if(isset($_POST['redirect']))
	$_GET['redirect'] = $_POST['redirect'];

//Vérification de l'existence d'une demande de connexion
if((isset($_POST['mail'])) && (isset($_POST['motdepasse'])))
{
	if (($_POST['mail'] =='') || ($_POST['motdepasse'] == ''))
	{
		$erreur = $lang[6];
	}
	elseif(!connnecteruser($_POST['mail'], $_POST['motdepasse'], $bdd))
	{
		$erreur = $lang[7];
	}
	else
	{
		//L'utilisateur est connecté.
		//On vérifie si il a défini une question de sécurité
		//On commence par rechercher les informations de la personne
		$afficher = cherchenomprenom($_SESSION['ID'], $bdd);
		
		//On vérifie si il faut bloquer Piwik ou non
		if($afficher['allow_piwik'] == 0)
			$_SESSION['block_piwik'] = true;
		
		//On vérifie si l'on doit supprimer les anciennes notifications
		if($afficher['nettoyage_automatique_notifications'] == 1);
			nettoie_anciennes_notifications($_SESSION['ID'], $bdd); //Exécution du nettoyage
		
		if(($afficher['question1'] == '' || $afficher['reponse1'] == '' || $afficher['question2'] == '' || $afficher['reponse2'] == '') AND (!isset($_GET['redirect'])))
		{
			?><!DOCTYPE html><?php
			?><html>
				<head>
					<title>Comunic - D&eacute;finition des questions de s&eacute;curit&eacute;</title>
					<?php include(pagesRelativePath('common/head.php')); ?>
					</head>
				<body class="metro">
					<?php include(pagesRelativePath('common/pageTop.php')); ?>
					<div class="nouveau_corps_page">
						<h2 class="titre">Pr&eacute;parez-vous &agrave; l'oubli de mot de passe</h2>
						<p>Vous &ecirc;tes bien connect&eacute; &agrave; Comunic mais vous n'avez pas encore d&eacute;fini les questions et les r&eacute;ponse de s&eacute;curit&eacute; qui vous permettront de r&eacute;cup&eacute;rer votre compte lorsque vous aurez perdu votre mot de passe. Nous vous conseillons de d&eacute;finir au plus vite vos questions de s&eacute;curit&eacute;.</p>
						<center>
							<a class="command-button success" href="parametres.php">
								<i class="icon-arrow-right-3 on-left"></i>
								Pr&eacute;parez-vous
								<small>D&eacute;finissez les questions de s&eacute;curit&eacute;.</small>
							</a>
							<a href="index.php">Plus tard</a>
						</center>
					</div>

					<?php
						//On inclut le pied de page uniquement si c'est nécessaire
						if(!isset($_GET['light']))
							include(pagesRelativePath('common/pageBottom.php'));
					?>
				</body>
			</html><?php
			die();
		}
		
		//On vérifie si l'on doit définir une adresse de déconnexion
		if(isset($_POST['logout_address']))
		{
			//On l'enregistre
			$_SESSION['logout_adress'] = $_POST['logout_address'];
		}
		
		if(!isset($_GET['redirect']))
		{
				//Redirection HTML
				header('Location: index.php');
				echo '<p>Connexion effectu&eacute;e avec succ&egrave;s. Redirection en cours....</p><a href="index.php">Rediriger</a>';
				echo '<meta http-equiv="refresh" content="0; url=index.php" />';
				die();
		}
		else
		{
			header('Location: '.$_GET['redirect']);
			echo "Connexion r&eacute;ussie, redirection en cours... <a href='".$_GET['redirect']."'>Rediriger</a>";
			echo '<meta http-equiv="refresh" content="0; url='.$_GET['redirect'].'" />'; 
		}
	}
}

//Prépartion au cas de l'existence du service
if(isset($_GET['idservice']))
{
	//Fonction de service
	//Inclusion de la configuration
	include('inc/services.conf.php');
	
	//Vérification de l'existence de ce service
	if(isset($liste_services[$_GET['idservice']]))
	{
		//On récupère les informations de ce service
		$info_service = $liste_services[$_GET['idservice']];
		
		//On supprime la variable $_GET['message'] si elle existe
		if(isset($_GET['message']))
		{
			unlink($_GET['message']);
		}
		
		//Définition de la variable de reconnexion
		$_GET['redirect'] = $info_service['adresse_service'];
	}
}

?><!DOCTYPE html>
<html>
	<head>
		<?php include(pagesRelativePath('common/head.php')); ?>
		<title><?php echo $lang[0]; ?></title>
	</head>
	<body class="metro">
		<?php include(pagesRelativePath('common/pageTop.php')); ?>
		
			<!-- Formulaire de connexion -->
			<h1 class='titre'><?php echo $lang[0]; ?></h1>
			<form action='<?php echo $_SERVER['PHP_SELF']; ?>?<?php 
																//Fonction en cas d'erreur de connexion pour perpétuer l'affichage
																if(isset($_GET['message'])) 
																	echo "message=".$_GET['message'];  //Simple message contenu dans l'URL
																elseif(isset($_GET['service'])) 
																	echo "idservice=".$_GET['idservice']; //Appel d'un service
			?>' name='<?php echo $lang[1]; ?>' method='post'>
			<?php if(isset($erreur)) { ?><center><font color='#FF0000'><?php echo $erreur; ?></font></center><?php } ?>
			<?php /* En cas de redirection nécessaire */ if(isset($_POST['logout_address'])) { ?><input type="hidden" name="logout_address" value="<?php echo $_POST['logout_address']; ?>" /><?php } ?>
			<div class="nouveau_corps_page">
				<label>Mail</label>
				<div class="input-control text">
					<input type='mail' required name='mail' value='<?php 
																									//Valeur du champ de mail
																									if(isset($_POST['mail'])) 
																										echo $_POST['mail']; //Une valeur a déjà été saisie
																									elseif(isset($_COOKIE['usermail'])) 
																									echo $_COOKIE['usermail']; //Une adresse mail est déjà enregistrée dans un cookie
								?>' placeholder="Adresse mail" />
				</div>
				
				<label><?php echo $lang[1]; ?> </label>
				<div class="input-control password">
					<input type='password' name='motdepasse' required placeholder="Mot de passe">
				</div>
					
				<input type='submit' value='<?php echo $lang[0]; ?>' /><br /><br /><br />
					
					<!-- Création de compte -->
					<a href='creercompte.php'><?php echo $lang[3]; ?></a><br />
					
					<!-- Mot de passe oublié -->
					<a href='solvepassword.php' title='R&eacute;cup&eacute;rer votre mot de passe'>Mot de passe perdu</a>
						
			</div>
			<?php if(isset($_GET['redirect'])) { ?><input type='hidden' name='redirect' value="<?php echo $_GET['redirect']; ?>" /><?php } ?>
			</form>
			
			<style type="text/css">
			/* On cache le formulaire de connexion */
			#loginuser { display: none; }
			.nouveau_corps_page {
				text-align: center;
				max-width: 400px !important;
			}
			</style>
			<!-- Fin de: Formulaire de connexion -->
			
			<hr />
			<?php
				//On inclut le pied de page uniquement si c'est nécessaire
				if(!isset($_GET['light']))
					include(pagesRelativePath('common/pageBottom.php'));
			?>
	</body>
</html>