<?php
/**
 * This file is useful to check we are 
 * on an allowed host for Comunic
 *
 * @author Pierre HUBERT
 */

isset($_SESSION) OR exit("Invalid call - ".$_SERVER['PHP_SELF']);

/**
 * Getting list of allowed host
 */
include(websiteRelativePath("inc/config/listAllowedHosts.inc.php"));

/**
 * Performing security check
 */
if(!in_array($_SERVER['HTTP_HOST'], $liste_allowed_hosts))
{
	//On redirige vers l'URL du site
	header('location: '.$urlsite);
}

