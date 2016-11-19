<?php
/**
 * Comunic, an open-source Social network
 *
 * To learn more about this project licence, please read the MIT license
 *
 * @author Pierre HUBERT
 */

//Initializate page
include('inc/initPage.php');

//Check if website is blocked for update or not
include(websiteCoreFolder("system/blockedForUpdate.inc.php"));

//Check if we are on an allowed URL
include(websiteCoreFolder("system/checkAllowedHost.inc.php"));


$liste_allowed_complements = array(
	"index.php",
	"?id="
);

//Pr�paration de la v�rification de la demande d'une redirection
if(isset($_SERVER['REDIRECT_REDIRECT_SCRIPT_URL']))
	$_SERVER['REDIRECT_URL'] = $_SERVER['REDIRECT_REDIRECT_SCRIPT_URL'];

//V�rification de la demande d'une redirection
if(isset($_SERVER['REDIRECT_URL']) && $active_gestion_404 == "oui")
{

	//Ce n'est pas l'URL initiale (ex: http://comunic.cu.cc/index.php)
	$infosURL = getInfosTypedURL();
	
	//We check if it is a webpage
	if(isset($pagesList[$infosURL['premier_dossier']])) {
		//We include the file and we quit
		include(pagesRelativePath($pagesList[$infosURL['premier_dossier']]['file']));
		exit();
	}

	//We try to verify if it is a personnal page
	$user = folder_is_an_user($infosURL['premier_dossier'], $bdd);
	
	//On v�rifie si il s'agit de la page d'un utilisateur
	if(!$user)
	{
		//Ce n'est pas le cas
		//On affiche le message d'erreur
		include('inc/404.html');
		
		header("HTTP/1.0 404 Not Found"); //Fichier non trouv�
		
		//Fermeture de la page
		die();
	}
	else
	{
		//On v�rifie qu'il n'y a pas de sous-dossier ouvert
		if($infosURL['premier_dossier'] != $infosURL['url_saisie'])
		{
			//Redirection pour �viter les probl�mes d'affichage
			header('Location: '.$infosURL['uri_base'].$infosURL['premier_dossier']);
			die();
		}
		
		//Redirection vers la page web
		header('Location: '.$urlsite.'index.php?'.$infosURL['premier_dossier']);
		die();
	}
}

//On v�rifie si un sous-dossier de index.php a �t� appel�
$url_saisie = str_replace('?', '', strstr($_SERVER['REQUEST_URI'], "?"));
if($url_saisie != "")
{
	//On r�cup�re le nom du premier sous-dossier (si il y a un slash
	if(str_replace("/", "", $url_saisie) != $url_saisie)
		$premier_dossier = strstr($url_saisie, "/", true);
	else
		$premier_dossier = $url_saisie;
	
	//On tente de d�finir une page d'utilisateur
	$user = folder_is_an_user($premier_dossier, $bdd);
	
	//On v�rifie si il s'agit de la page d'un utilisateur
	if($user)
	{
		//On v�rifie qu'il n'y a pas de sous-dossier ouvert
		if($premier_dossier != $url_saisie)
		{
			//Redirection pour �viter les probl�mes d'affichage
			header('Location: '.$urlsite.'index.php/'.$premier_dossier);
			die();
		}
			
		//D�finition de l'ID trouv�e comme l'ID de la page
		$_GET['id'] = $user['ID'];	
	}
}



//On v�rifie si il faut mettre � jour la position du panneau
if(isset($_SESSION['ID']))
{
	if(isset($_GET['miseajourpanneau']))
	{
		//On d�termine quelle sera la mise � jour
		if($_GET['miseajourpanneau'] == 1)
		{
			$situationpanneau = 1;
		}
		else
		{
			$situationpanneau = 0;
		}
		
		//On enregistre le panneau dans la base de donn�es
		$sql = "UPDATE utilisateurs SET volet_amis_ouvert = ".$situationpanneau." WHERE ID = ".$_SESSION['ID']." ";
		
		//Ex�cution de la requ�te
		$bdd->query($sql);
		
		//On quitte le script courant
		exit();
	}
}


//Avant tout, on v�rifie si la personne ne veut pas visualiser une page publique
$autorisationspeciale = 0;
if(!isset($_SESSION['ID']))
{
	if (isset($_GET['id']))
	{
		//On v�rifie si la page est publique
		if(detectepagepublique($_GET['id'], $bdd))
		{
			$autorisationspeciale = 1;
		}
		
	}
}

?>
<!DOCTYPE html>
<html>
	<head>
		<title><?php echo((!isset($_SESSION['ID'])) && ($autorisationspeciale == 0) ? "Bienvenue &agrave; Comunic. D&eacute;couvrez un nouveau moyen de communication, inscrivez-vous gratuitement ou connectez-vous." : "Comunic"); ?></title>
		<?php 
			if(isset($_SESSION['ID']) || $autorisationspeciale == 1)
				$not_home_login_page = true;
			include(pagesRelativePath('common/head.php'));
		?>
		
			
	</head>
	<body <?php if((!isset($_SESSION['ID'])) && ($autorisationspeciale == 0)) echo "class='page_acceuil_login'"; ?>>
	<?php include(pagesRelativePath('common/pageTop.php')); ?>
	<?php
	
	if((!isset($_SESSION['ID'])) && ($autorisationspeciale == 0))
	{
		//Including file (home screen)
		include('inc/pages/homeLogout/homeLogout.inc.php');
	}
	else
	{
		//Including file (user's home)
		include('inc/pages/homeUser/homeUser.inc.php');
	}

	?></body>
</html>