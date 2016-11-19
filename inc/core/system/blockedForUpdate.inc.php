<?php
/**
 * This file is used to check whether the website
 * was shutdown for upgrade or not
 * 
 * @author Pierre HUBERT
 */

isset($_SESSION) OR exit("Invalid call - ".$_SERVER['PHP_SELF']);

//On vérifie si le site est bloqué pour une mise à jour ou non
if($bloque_site_for_update == 1)
{
	echo  add_url_site(file_get_contents(websiteRelativePath().'inc/message_update.html'), $urlsite);
	die();
}