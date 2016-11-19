<?php
	//Inclusion du script de sécurité
	include('securite.php');
	
	//Initializate page
	include('inc/initPage.php');

	//Get settings file list
	$settingsFolder = "inc/pages/settings/";
	include($settingsFolder.'settingsList.php');

?><!DOCTYPE html>
<html>
	<head>
		<?php include(pagesRelativePath('common/head.php')); ?>
		<title><?php echo $lang[55]; ?></title>
		<?php
			/**
			 * Javascript for the settings page
			 */

			//Suppression de compte
		 	echo code_inc_js(path_js_asset('confirmdeleteaccount.js'));
		
			//Découpage d'une image
			echo code_inc_css(path_css_asset('imgareaselect/imgareaselect-default.css'));
			echo code_inc_js(path_js_asset('imgareaselect/jquery.imgareaselect.pack.js'));

		?>
		
	</head>
	<body class="metro">
		<!-- En-tête -->
		<?php include(pagesRelativePath('common/pageTop.php')); ?>
		<div class="container container-settings">
			<div class="grid">
				<div class="row">
					<div class="span4">
						<?php
							//Including menu
							include($settingsFolder.$settingsList['settingsMenu']['file']);
						?>
					</div>
					<div class="span8 volet_droit_parametres">
						<?php 

						/**
						 * Settings page calling
						 */

						//Page determination
						//If not any specific page has been called
						if(!isset($_GET['c']))
							$settingsPage = "general";
						//Else we get the called page
						else
							$settingsPage = $_GET['c'];

						//If the required page doesn't exists, we select the error page
						if(!isset($settingsList[$settingsPage]))
							$settingsPage = "error";

						//Calling file
						include($settingsFolder.$settingsList[$settingsPage]['file'])
						?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- Pied de page -->
		<hr>
		<?php include(pagesRelativePath('common/pageBottom.php')); ?>
	</body>
</html>