<?php
/**
 * Show images comments
 *
 * @author Pierre HUBERT
 */

	// Exemple d'en-tête serveur permettant une bonne mise en cache 
	
	// X-Powered-By:	PHP/5.5.3-1ubuntu2.6
	// Vary:	Accept-Encoding
	// Server:	Apache/2.4.6 (Ubuntu)
	// Last-Modified:	Wed, 22 Apr 2015 13:16:19 GMT
	// Keep-Alive:	timeout=5, max=100
	// Expires:	Thu, 23 Apr 2015 13:06:49 GMT
	// Etag:	"pub1429708579;gz"
	// Date:	Thu, 23 Apr 2015 12:36:49 GMT
	// Content-Type:	application/x-javascript; charset=utf-8
	// Content-Length:	5549
	// Content-Encoding:	gzip
	// Connection:	Keep-Alive
	// Cache-Control:	max-age=1800

//Démarrage de la session
session_start();

//Init page
include('inc/initPage.php');

//Détermination du sous-chemin
$path = str_replace('.php', '', strstr($_SERVER['REQUEST_URI'], ".php"));

//Calcul du nombre de chaque type de caractères
$array_chars = count_chars($path);

//Contrôle du nombre de slash
if($array_chars[47] != "2")
	die('Invalid arguments !');

//Récupération des paramètres
$get = explode('/', $path);

//Récupération des informations
$idcomment = $get[1];
$comment = $get[2];

//Récupération de l'ID du commentaire
$id_commentaire = base64_decode($idcomment)*1;

//Contrôle de l'ID du commentaire
if($id_commentaire < 0)
	die('Invalid arguments!');

//Récupération du commentaire
$sql = "SELECT * FROM commentaires WHERE ID = ?";
$requete = $bdd->prepare($sql);
$requete->execute(array($id_commentaire));

//Contrôle de la requête
if(!($infos_commentaires = $requete->fetch()))
	die('Specified comment does not exists !');

//Fermeture de la requête
$requete->closeCursor();

//On vérifie que le commentaire contienne bien une image
if($infos_commentaires['image_commentaire'] == "")
	die('Le commentaire sp&eacute;cifi&eacute; ne contient pas d\'image.');

//Contrôle du numéro de commentaire demandé
if(sha1($infos_commentaires['commentaire']) != $comment)
	die('Script closed for security reasons.');

//Contrôle du texte inclus (pour le niveau de visibilite)
$sql = "SELECT * FROM texte WHERE ID = ?";
$requete = $bdd->prepare($sql);
$requete->execute(array($infos_commentaires['ID_texte']));

//Contrôle du résultat
if(!($infos_texte = $requete->fetch()))
	die('An error occured, please try again later');
	
//Fermeture de la requête
$requete->closeCursor();

//Contrôle du résultat
if($infos_texte['niveau_visibilite'] != "1" AND !isset($_SESSION['ID']))
	die('Login required to view this image !');

//On vérifie si le commentaire est issu d'un texte de niveau de visibilité "moi seleument"
if($infos_texte['niveau_visibilite'] == "3")
{
	if($_SESSION['ID'] != $infos_texte['ID_personne'] AND $_SESSION['ID'] != $infos_texte['ID_amis'])
	{
		die("You aren't allowed to view this image !");
	}
}

//Maintenant on peut afficher l'image
//Envoi des en-têtes
header('Pragma: ');
header('Connection: Keep-Alive');
header('Cache-Control: max-age=1800');
header('Content-Type: image/png');
header('Keep-Alive: timeout=5, max=100');
header('Last-Modified: '.gmdate('D, d M Y H:i:s \G\M\T', time()));
header('Date: '.gmdate('D, d M Y H:i:s \G\M\T', time()));
header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', strtotime('+ 1 week')));
header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', strtotime('+ 1 week')));

//On vérifie si il s'agit d'un fichier
if(preg_match("<file:>", $infos_commentaires['image_commentaire']))
{
	//$fichier = "imgcommentaire/".str_replace('file:', '', $infos_commentaires['image_commentaire']);
	$fichier = str_replace('file:', '', $infos_commentaires['image_commentaire']);

	//On inclue le fichier
	echo file_get_contents(relativeUserDataFolder($fichier));
	
	//On quitte le fichier
	exit();
}

//Affichage de l'image
echo base64_decode($infos_commentaires['image_commentaire']);
