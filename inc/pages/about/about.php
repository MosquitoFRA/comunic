<?php
/**
 * About the project page
 *
 * @author Pierre HUBERT
 */

isset($_SESSION) OR exit("Invalid call - ".$_SERVER['PHP_SELF']);

?>
<!DOCTYPE html>
<html id="<?php echo (isset($_GET['cgu']) ? "" :"about"); ?>">
	<head>
		<title>A propos</title>
		<?php include(pagesRelativePath('common/head.php')); ?>
	</head>
	<body class="metro">
		<?php include(pagesRelativePath('common/pageTop.php')); 

			if(!isset($_GET['cgu']))
			{
				?><h1 class='titre'>A propos</h1>
					<div class="contener_main">
						<div class="logo_contener">
							<?php echo code_inc_img(path_img_asset('logo_comunic.png'), "Logo de Comunic"); ?>
						</div>
						<div class="text_content">Comunic a &eacute;t&eacute; con&ccedil;u par Pierre Hubert.</div>
						<div class="text_content">Remerciement pour leurs pr&eacute;cieux conseil &agrave; Tristan Th&eacute;venin, Florent Wang et Samir Bertache.</div>
						<div class="text_content">Tous droits r&eacute;serv&eacute;s. 2013-2016</div>
					</div>
							
					<div class="contener_infos">
						<h2>Informez-vous</h2>
						<!-- CGU Link -->
						<div class="big_link">
							<a href='<?php echo siteURL('about.php'); ?>?cgu'>
								<?php echo code_inc_img(path_img_asset('text_list_numbers.png')); ?>
								Lisez les conditions d'utilisation
							</a>
						</div>

						<!-- Privacy Link -->
						<div class="big_link">
							<a href="<?php echo siteURL('about.php'); ?>?cgu&privacy">
								<?php echo code_inc_img(path_img_asset('lock.png')); ?>
								Politique de confidentialit&eacute; et d'utilisation des donn&eacute;es
							</a>
						</div>
					</div>

					<p style="text-align: center;">
						Informations relatives &agrave; certaines extensions :
					</p>
					
					<!-- Let's Encrypt -->
					<p style="text-align: center;">
						Un grand merci &agrave; Let's Encrypt gr&acirc;ce &agrave; qui notre site peut fonctionner en HTTPS...
					</p>
					
					<!-- Metro UI CSS -->
					<p style="text-align: center;">
						Sans <a href="http://metroui.org.ua/" target='_blank'>Metro UI CSS</a>, notre site n'aurait pas une telle interface...
					</p>

					<!-- Webcam - WebRTC -->
					<p style="text-align: center;">
						<i>Certains composants de gestion de la webcam sont issues de <a href='http://www.webrtc.org/' target="_blank">WebRTC</a> dont la licence est disponible <a href="<?php echo path_assets('legal/LICENSE_webrtc.txt'); ?>" target="_blank"> ici </a>.</i>
					</p>
					
					<!-- Fatcow -->
					<p style="text-align: center;">Certaines images de ce site sont issues de Fatcow: <br />
												&copy; Copyright 2009-2014 FatCow Web Hosting. All rights reserved.<br />
												<a href='http://www.fatcow.com' target='_blank'>http://www.fatcow.com</a><?php
		}
		else
		{
			?><div class="container_cgu">
				<?php
					//Preparing licence
					if(!isset($_GET['privacy']))
					{
						//The subject are the CGU
						$name = "Conditions d'utilisation de Comunic";
						$path = path_assets('legal/cgu.html');
						$licence = file_get_contents(relativePath_assets('legal/cgu.html'));
					}
					else
					{
						//The subject is the privacy policy
						$name = "Politique de gestion et d'utilisation des donn&eacute;es";
						$path = path_assets('legal/privacy.html');
						$licence = file_get_contents(relativePath_assets('legal/privacy.html'));
					}

					//Showing licence
					?><h2 class='titre'><?php echo $name; ?></h2>
					<div class="cgu"><?php echo $licence; ?></div>
					<a href="<?php echo $path; ?>" target="_blank">Fichier HTML</a><?php

			?></div><?php
		}
		
		//Bottom inclusion
		?><hr><?php 
		include(pagesRelativePath('common/pageBottom.php'));
	?></body>
</html>