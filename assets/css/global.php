<?php
	//Envoi des en-têtes
	header('Pragma: ');
	header('Connection: Keep-Alive');
	header('Cache-Control: max-age=1800');
	header('Content-Type: text/css');
	header('Keep-Alive: timeout=5, max=100');
	header('Last-Modified: '.gmdate('D, d M Y H:i:s \G\M\T', time()));
	header('Date: '.gmdate('D, d M Y H:i:s \G\M\T', time()));
	header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', strtotime('+ 1 year')));
	header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', strtotime('+ 1 year')));
	
	//Récupération du contenu du fichier
	$source = file_get_contents('header_global.css');
	$source = $source.file_get_contents('global.css');
	
	//Modification de la source
	
	$source = str_replace('../fonts/', '../../fonts/', $source);
	$source = str_replace('../img/', '../../img/', $source);
	$source = str_replace('fancybox_sprite.png', '../../img/fancybox/fancybox_sprite.png', $source);
	$source = str_replace('blank.gif', '../../img/fancybox/blank.gif', $source);
	$source = str_replace('fancybox_loading@2x.gif', '../../img/fancybox/fancybox_loading@2x.gif', $source);
	$source = str_replace('fancybox_sprite@2x.png', '../../img/fancybox/fancybox_sprite@2x.png', $source);
	$source = str_replace('fancybox_overlay.png', '../../img/fancybox/fancybox_overlay.png', $source);
	$source = str_replace('fancybox_loading.gif', '../../img/fancybox/fancybox_loading.gif', $source);

	//Polices locales
	$source = str_replace("https://themes.googleusercontent.com/", "../../fonts/themes.googleusercontent.com/", $source);
	
	//Affichage de la source
	echo $source;
?>