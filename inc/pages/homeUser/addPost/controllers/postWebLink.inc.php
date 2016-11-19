<?php
/**
 * Post a web link
 *
 *	@author Pierre HUBERT
 */

isset($_SESSION) OR exit('Invalid call - '.$_SERVER['PHP_SELF']);

//Enregistrement de l'URL
$url = $_POST['adresse_page'];
$description = ($_POST['texte_lien_page'] != "" ? "<p>".$_POST['texte_lien_page']."</p>" : "");

//Inclusion de la fonction d'analyse
require_once(relativePath_3rdparty('analysing_page/analyser_fr.php'));

//Contrôle de l'URL
if(!preg_match('<http://>', $url) AND !preg_match('<https://>', $url))
{
	echo affiche_message_erreur("L'URL saisie est invalide !"); //L'URL donnée est invalide
}
else
{
	//On commence par récupérer le code source de l'URL
	ob_start();
	$source = file_get_contents($url);
	ob_end_clean();
	
	//Contrôle de la source
	if($source == "")
	{
		echo affiche_message_erreur("La page demand&eacute;e n'a pas &eacute;t&eacute; trouv&eacute;e !"); //Page non trouvée (404)
	}
	else
	{
		//On peut tenter d'extraire les informations
		$infos_page = analyse_source_page_extrait_description($source);
		
		//On prépare l'enregistrement de la page
		$infos_page['titre'] = ($infos_page['titre'] == null ? "default" : $infos_page['titre']);
		$infos_page['description'] = ($infos_page['description'] == null ? "default" : $infos_page['description']);
		$infos_page['image'] = ($infos_page['image'] == null ? "default" : $infos_page['image']);
		
		//On enregistre la page
		//Ajout du texte
		if($_SESSION['ID'] == $idPersonn)
			ajouttexte($_SESSION['ID'], $description, $bdd, $niveau_visibilite, "webpage_link", "", 0, 0, 0, $url, $infos_page['titre'], $infos_page['description'], $infos_page['image']);
		else //Si c'est un amis
			ajouttexte_amis($_SESSION['ID'], $idPersonn, $description, $bdd, $niveau_visibilite, "webpage_link", "", 0, 0, 0, $url, $infos_page['titre'], $infos_page['description'], $infos_page['image']);
		
		//Message de succès
		echo "<p><img src='".path_img_asset('succes.png')."' title='Succ&egrave;s' alt='V' /> Le lien vers la page a bien &eacute;t&eacute; ajout&eacute;.</p>";
	}
}