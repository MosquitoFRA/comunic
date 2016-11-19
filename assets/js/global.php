<?php
	//Envoi des en-têtes
	header('Pragma: ');
	header('Connection: Keep-Alive');
	header('Cache-Control: max-age=1800');
	header('Content-Type: application/x-javascript');
	header('Keep-Alive: timeout=5, max=100');
	header('Last-Modified: '.gmdate('D, d M Y H:i:s \G\M\T', time()));
	header('Date: '.gmdate('D, d M Y H:i:s \G\M\T', time()));
	header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', strtotime('+ 1 year')));
	header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', strtotime('+ 1 year')));
	
	//Récupération du contenu du fichier
	//if(preg_match('<ie>', $_SERVER['REQUEST_URI']))
	//	$source = file_get_contents('global_ie.js');
	//else
		$source = file_get_contents('header_global.js');
		$source = $source.file_get_contents('global.js');
	
	//Affichage de la source
	echo $source;
?>