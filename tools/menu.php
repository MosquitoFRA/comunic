<?php
	//Inclusion de la configuration du site
	include((isset($sub_folder) ? "../" : "").'../inc/config/config.php');
?>

<!-- Icone du site -->
<link rel="icon" type="image/vnd.microsoft.icon" href="<?php echo $urlsite;?>tool/assets/img/favicon.ico" />
<link rel="shortcut icon" type="image/x-icon" href="<?php echo $urlsite;?>tools/assets/img/favicon.ico" />
<!-- Fin de: Icone du site -->

<!-- Metro UI CSS 3.0 -->
<link type="text/css" rel="stylesheet" href="<?php echo $urlsite;?>tools/3rdparty/metrouicss/css/metro.min.css" />
<link type="text/css" rel="stylesheet" href="<?php echo $urlsite;?>tools/3rdparty/metrouicss/css/metro-icons.min.css" />

<!-- Barre de menu -->
<div class="app-bar" data-role="appbar">
	<a class="app-bar-element" href="<?php echo $urlsite; ?>tools/"><span class="mif-tools"></span> Outils</a>
	<span class="app-bar-divider"></span>
	
	<a class="app-bar-element" href="<?php echo $urlsite; ?>tools/nobin"><span class="mif-lock"></span> ZeroBin</a>
	<a class="app-bar-element" href="<?php echo $urlsite; ?>tools/speaker"><span class="mif-volume-high"></span> Speaker.js</a>
	<a class="app-bar-element" href="<?php echo $urlsite; ?>tools/svgedit"><span class="mif-paint"></span> SVGEdit</a>
	<a class="app-bar-element place-right" href="<?php echo $urlsite; ?>" target="_blank">Comunic</a>
</div>