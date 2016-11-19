<?php
	
	//Initialisation de la langue
	$lang = detecteinstallelangue();
	
	//Récupération du nom de fichier
	$nom_fichier = substr(strrchr($_SERVER['PHP_SELF'], "/"), 1);
	//$nom_fichier_cache = "cache/".sha1("header.php of ".$nom_fichier);

	//Inclusion de la liste des descriptions
	include(websiteRelativePath().'inc/liste_descriptions.php');
?>
<!-- Informations d'en-tête -->
<!--<meta http-equiv='Content-Type' content='text/html; charset=us-ascii'>-->
<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
<meta name="description" content="<?php
	if(isset($liste_descriptions[$nom_fichier]))
		echo $liste_descriptions[$nom_fichier];
	else
		echo $liste_descriptions["default"];
?>">
<meta name="abstract" content="Comunic est un r&eacute;seau social qui permet de communiquer gratuitement entre amis tout en respectant la vie priv&eacute;e.">
<meta name="keywords" content="r&eacute;seau social, chat, pages personnelles, amis, comuniquer, vie priv&eacute;e, accessibilit&eacute;, vid&eacute;os personnelles, compte, gratuit, moderne">
<meta name="author" content="Pierre Hubert">
<meta name="revisit-after" content="15">
<meta name="language" content="FR">
<meta name="copyright" content="2013 - 2016 Comunic">
<meta name="robots" content="All">
<!-- Fin de: Informations d'en-tête -->

<?php
	//Autoriser ou non les flux réseaux
	include(websiteRelativePath().'inc/manage_flux.html');
 ?>

<!-- Proposition de Comunic en tant que moteur de recherche -->
<link rel="search" type="application/opensearchdescription+xml" title="Comunic" href="<?php echo $urlsite; ?>action.php?actionid=33" />
<!-- Fin de: Proposition de Comunic en tant que moteur de recherche -->

<!-- Appel nécessaire au Responsive -->
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<?php

/**
 * Calling CSS files
 */

//Main CSS file
echo code_inc_css(path_css_asset('global.php/'.$last_update_website));

//Opened page or login required 
if(isset($_SESSION['ID']) || isset($not_home_login_page))
{
	// Video-js
	echo code_inc_css(url_3rdparty('video_js/video-js.css'));
	
	// Login required
	if(isset($_SESSION['ID']))
	{ 
		// Notifications
		echo code_inc_css(path_css_asset('global_notifications.css'));
	}
}

/**
 * JS config
 */
?><script type="text/javascript">
	var config = [];
	config['pathAssets'] = "<?php echo path_assets(); ?>";
</script><?php

/**
 * Calling JS files
 */

// Main JS file
echo code_inc_js(path_js_asset('global.php/'.$last_update_website));

// Smiley
echo code_inc_js(path_js_asset('liste_smile.js'));
	
	
// Opened page or login required
if(isset($_SESSION['ID']) || isset($not_home_login_page))
{
	// Jquery MouseWheel
	echo code_inc_js(path_js_asset('jquery/jquery.mousewheel.js'));
	
	//Jquery Fancybox
	echo code_inc_js(url_3rdparty('fancyapps/source/jquery.fancybox.js'));
	echo code_inc_js(path_js_asset('initialise_fancybox.js'));
	
	// VideoJS
	echo code_inc_js(url_3rdparty('video_js/video.js'));
	echo "<script type='text/javascript'>videojs.options.flash.swf = \"".$urlsite."video_js/video-js.swf\";</script>";
	
	// Login required
	if(isset($_SESSION['ID']))
	{
		//Liste des groupes
		echo code_inc_js($urlsite.'r.php/js/liste_groupes.js');
		
		// Gestion du choix de groupes
		echo code_inc_js(path_js_asset('groupes.js'));
		
		// Tiny MCE
		echo code_inc_js(path_js_asset('tiny_mce/tiny_mce.js'));
		
	}
}

/**
 * Piwik, if allowed
 */
if($enable_piwik == 1 AND (!isset($_SESSION['block_piwik'])))
	include('inc/gestion_piwik.php');

/**
 * Personalisez source
 */
echo $complementsource;

?>
<!-- Icone du site -->
<link rel="icon" type="image/vnd.microsoft.icon" href="<?php echo path_img_asset('favicon.ico'); ?>" />
<link rel="shortcut icon" type="image/x-icon" href="<?php echo path_img_asset('favicon.ico'); ?>" />
<!-- Fin de: Icone du site -->

<!-- Feuilles de style CSS -->
<?php
	//Compatibilité de Internet Explorer
	if(isset($_SERVER['HTTP_USER_AGENT']))
	{
		if(preg_match("/MSIE/", $_SERVER['HTTP_USER_AGENT']) || isset($_SESSION['compatible']))
		{
			?><!-- Compatibilté du navigateur -->
			<link rel="stylesheet" href="<?php echo $urlsite; ?>css/ie.css">
			<!-- Script javascript associé -->
			<script type="text/javascript">
			function MM_jumpMenu(targ,selObj,restore){ //v3.0
			  eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
			  if (restore) selObj.selectedIndex=0;
			}
			</script>
			<!-- Fin de: Compatibilité du navigateur --><?php
		}
	}
?>
<!-- Fin de: Feuilles de style CSS -->


<?php
	//On enregistre l'activité de l'utilisateur uniquement si ce dernier est connecté
	if(isset($_SESSION['ID']))
	{
		//On enregistre l'activité
		update_last_activity($_SESSION['ID'], $bdd);
		
		//On définit une routine pour un trackage régulier
		?><script type="text/javascript">
			//Inutile, le fichier amis.php suffit
			//var last_activity=setInterval("ajax_rapide('about.php?withbreak')", 30000); // répète toutes les 30s
		</script><?php
		
		//On quitte le script courrant si c'était une demande de trackage d'activité
		if(isset($_GET['withbreak']))
		{
			//Fermeture du script
			die();
		}
	}
	
	//On charge les notifications de l'utilisateur uniquement si ce dernier est connecté
	if(isset($_SESSION['ID']))
	{
		$info = cherchenomprenom($_SESSION['ID'], $bdd);
		
		if($info['bloquenotification'] == 0)
		{
			//On affiche les scripts JAVASCRIPT
			?><span id="son_notification_area"></span><script type="text/javascript">//Timer pour actualiser toute les 2 secondes les notifications
						<?php echo ($info['bloque_son_notification'] == "0" ? 'prepare_joue_son("'.$urlsite.'audio/notification.ogg", "'.$urlsite.'audio/notification.mp3", "son_notification");' : ''); ?>//Prépare le son
						var last_notifications_timer=setInterval("getpopupnotification('notification.php?rapide')", 3000); // répète toutes les 3s
					</script><?php
		}
		
		//On affiche le menu clique droit uniquement si l'utilisateur est connecté et que la capture du clic droit est activée
		if($bloque_clic_droit == "oui")
		{
			//Inclusion du menu clique droit
			include('inc/menu_contextuel.php');
		}
	}
?>