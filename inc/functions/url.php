<?php
/**
 * URL functions
 *
 * @author Pierre HUBERT
 */

/**
 * Returns informations about typed URL
 *
 * @return 	Array 	Infos about URL
 */
function getInfosTypedURL(){
	$data = array();

	//On récupère l'URI de base
	$data['uri_base'] = str_replace("index.php", "", $_SERVER['SCRIPT_NAME']);
	
	//On enlève l'URI de base à l'URL saisie
	$data['url_saisie'] = str_replace($data['uri_base'], "", $_SERVER['REDIRECT_URL']);
	
	//On récupère le nom du premier sous-dossier (si il y a un slash)
	if(str_replace("/", "", $data['url_saisie']) != $data['url_saisie'])
		$data['premier_dossier'] = strstr($data['url_saisie'], "/", true);
	else
		$data['premier_dossier'] = $data['url_saisie'];

	//Returning data
	return $data;
}