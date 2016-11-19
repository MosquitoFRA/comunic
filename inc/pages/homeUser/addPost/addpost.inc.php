<?php
/**
 * Add a post
 */

isset($_SESSION['ID']) OR exit('addPost requires login !');

//Check requirements
if(!isset($_GET['id']) AND !isset($id))
	die("Invalid call!");

if(!isset($id))
	//Définition de l'ID (avec sécurité)
	$idPersonn = $_GET['id']*1;
else
	$idPersonn = $id;
	
//If it is the actuality, we have to change the ID to the main ID
if($idPersonn == 0)
	$idPersonn = $_SESSION['ID'];

//Récupération des informations de la personne
$afficher = cherchenomprenom($idPersonn, $bdd);
	
//Adaptation des variables serveur
$_SERVER['PHP_SELF'] = str_replace('action.php', 'index.php', $_SERVER['PHP_SELF']);
	

//Si ce n'est pas la page de la personne on vérifie si elle est amie et autorisée à ajouter un post
if($_SESSION['ID'] != $idPersonn)
{
	//Including file
	include('controllers/checkFriendAllowance.inc.php');
}

if($_SESSION['ID'] == $idPersonn || isset($allowcreatepost))
{
	//Initialisation du niveau de visibilité
	$niveau_visibilite = (isset($_POST['niveau_visibilite']) ? $_POST['niveau_visibilite']*1 : 2);
	
	//On complète, si nécessaire, le niveau de visibilité
	if($niveau_visibilite == 3 AND isset($_POST['liste_groupes']))
	{
		//Intégration des groupes dans le niveau de visibilité
		foreach($_POST['liste_groupes'] as $lister=>$value)
		{
			$niveau_visibilite .= "|".$lister;
		}
	}

	//Vérification de l'envoi d'un texte ou d'une image
	if(isset($_POST['texte']))
	{
		//Including file
		include('controllers/postTextImage.inc.php');
	}
	
	//If a YouTube video has been posted...
	if(isset($_POST['youtube']) && isset($_POST['commentyoutube']))
	{
		//Including file
		include('controllers/postYouTube.inc.php');
	}
	
	//Vérification de l'existence de l'envoi d'une vidéo
	if(isset($_POST['idvideo']) && isset($_POST['commentaire_video']) && isset($_POST['niveau_visibilite']))
	{
		//Including file
		include('controllers/postVideo.inc.php');
	}
	
	//Vérification de l'envoi d'un événement
	if(isset($_POST['date']) AND isset($_POST['nom_evenement']))
	{
		//Including file
		include('controllers/postEvent.inc.php');
	}
	
	//Vérification de l'envoi d'un lien vers une page web
	if(isset($_POST['adresse_page']) AND isset($_POST['texte_lien_page']))
	{
		//Including file
		include('controllers/postWebLink.inc.php');
	}
	
	//Vérification de l'envoi d'un fichier PDF
	if(isset($_POST['texte_post_with_pdf']))
	{
		//Including file
		include('controllers/postPDF.inc.php');
	}
	
	//On vérifie si il faut ajouter un sondage
	if(isset($_POST['reponses_sondage']) AND isset($_POST['question_sondage']) AND isset($_POST['commentaire_sondage']))
	{
		//Including file
		include('controllers/postSurvey.inc.php');
	}
	
	//Add text form
	//Including file
	include('views/v_addPost.inc.php');
}