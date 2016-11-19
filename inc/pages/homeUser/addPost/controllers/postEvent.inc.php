<?php
/**
 * Post an event
 *
 *	@author Pierre HUBERT
 */

isset($_SESSION) OR exit('Invalid call - '.$_SERVER['PHP_SELF']);

//Exemple d'envoi :
// [date] => 16.04.2015
// [heure] => 4
// [minute] => 4
// [nom_evenement] => test
// [niveau_visibilite] => 2

$date = $_POST['date'];
//$heure = $_POST['heure']*1;
//$minute = $_POST['minute']*1;
$nom_evenement = $_POST['nom_evenement'];

if($date == "")
{
	echo affiche_message_erreur("L'&eacute;v&eacute;nement n'a pas de date !"); //L'événement doit avoir une date
}
else
{
	//Décomposition de la date
	$array_date = explode('|', str_replace('.', '|', $date));
	
	if(count($array_date) != 3)
		echo affiche_message_erreur("La date sp&eacute;cifi&eacute;e est incorrecte."); //La date donnée est invalide
	elseif($array_date[1] > 31 OR $array_date[1] < 1)
		echo affiche_message_erreur("Le jour sp&eacute;cifi&eacute; est incorrect."); //Le jour donné est invalide
	elseif($array_date[2] < date("Y", time()))
		echo affiche_message_erreur("L'ann&eacute;e donn&eacute;e est pass&eacute;e."); //L'année donnée est invalide
	else
	{
		//Ajout du texte
		if($_SESSION['ID'] == $idPersonn)
			ajouttexte($_SESSION['ID'], $nom_evenement, $bdd, $niveau_visibilite, "count_down", "", $array_date[2], $array_date[1], $array_date[0]);
		else //Si c'est un amis
			ajouttexte_amis($_SESSION['ID'], $idPersonn, $nom_evenement, $bdd, $niveau_visibilite, "count_down", "", $array_date[2], $array_date[1], $array_date[0]);
		
		//Message de succès
		echo "<p><img src='".path_img_asset('succes.png')."' title='Succ&egrave;s' alt='V' /> L'&eacute;v&eacute;nement a bien &eacute;t&eacute; ajout&eacute;.</p>";
	}
}