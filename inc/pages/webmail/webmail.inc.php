<?php
/**
 * Webmail client
 * Roundcube client has been removed because of the complexity of the service
 *
 * @author Pierre
 */

	//Sécurité
	include('securite.php');

	//On indique que RoundCube a été utilisé
	$_SESSION['roundcube_used'] = true;
?><!DOCTYPE html>
<html>
	<head>
		<title>Comunic - Webmail</title>
		<?php include(pagesRelativePath('common/head.php')); ?>
		
		<style type="text/css">
			#iframe_webmail {
				width: 100%;
				border: none;
				margin-bottom: -4px;
			}
		</style>
	</head>
	<body>
		<?php include(pagesRelativePath('common/pageTop.php')); ?>
		<iframe id="iframe_webmail" src="<?php echo $urlsite; ?>/roundcube/?mail=<?php echo $afficher['mail']; ?>"></iframe>
		
		<script type="text/javascript">
		var viewportheight;
		var viewportwidth;
		  
		// the more standards compliant browsers (mozilla/netscape/opera/IE7) use window.innerWidth and window.innerHeight
		  
		if (typeof window.innerWidth != 'undefined')
		{
			viewportheight = window.innerHeight;
			viewportwidth = window.innerWidth;
		}
			  
		// IE6 in standards compliant mode (i.e. with a valid doctype as the first line in the document)	 
		else if (typeof document.documentElement != 'undefined' && typeof document.documentElement.clientWidth != 'undefined' && document.documentElement.clientWidth != 0)
		{
			viewportheight = document.documentElement.clientHeight;
			viewportwidth = document.documentElement.clientWidth;
		}
		  
		// older versions of IE 
		else
		{
			viewportheight = document.getElementsByTagName('body')[0].clientHeight;
			viewportwidth = document.getElementsByTagName('body')[0].viewportWidth;
		}
		
		//Définition de la place disponible
		var hauteur_disponible = viewportheight-45;
		var largeur_disponible = viewportwidth-36;
		
		//Application de la hauteur à l'iframe
		document.getElementById('iframe_webmail').style.height = hauteur_disponible + "px";
		document.getElementById('iframe_webmail').style.width = largeur_disponible + "px";
		</script>
		
		<div style="position: absolute; top: -10000px;">
		<?php include(pagesRelativePath('common/pageBottom.php')); ?>
		</div>
	</body>
</html>