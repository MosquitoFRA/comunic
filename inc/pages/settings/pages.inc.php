<?php
/**
 * Change pages settings
 *
 * @author Pierre HUBERT
 */

if(!isset($_SESSION))
	exit("Invalid call !");

//Titre
?><h3>Gestion de vos pages</h3><?php

//Sous-titre
?><h4>Liste de vos pages</h4><?php

//Récupération de la liste des pages
$liste_pages = get_liste_pages($_SESSION['ID'], $bdd);

//Affichage de la liste des pages (temporaire)
echo "<pre>";
print_r($liste_pages);
echo "</pre>";

//Sous-titre
?><h4>Ajout d'une page</h4><?php

?>Fonctions en d&eacute;veloppement<?php