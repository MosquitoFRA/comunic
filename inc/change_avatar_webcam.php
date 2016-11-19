<?php
	//Sécurité
	if(!isset($change_avatar_webcam))
		die("Invalid Request!");
	if(!isset($_SESSION['ID']))
		die("Login required !");

	//On vérifie si un avatar a été envoyé
	if(isset($_POST['data']))
	{
		if($_POST['data'] != "")
		{
			//On supprime l'ancien avatar si il y en a un
			if(file_exists(relativeUserDataFolder('avatars/adresse_avatars/'.$_SESSION['ID'].'.txt')))
			{
				unlink(relativeUserDataFolder('avatars/'.file_get_contents(relativeUserDataFolder('avatars/adresse_avatars/'.$_SESSION['ID'].'.txt'))));
			}
			
			//Nom du nouvel avatar
			$adresse = sha1(time()).".png";
			
			//On modifie la date d'enregistrement de l'avatar
			file_put_contents(relativeUserDataFolder('avatars/adresse_avatars/'.$_SESSION['ID'].'.txt'), $adresse);
			
			//Enregistrement de l'avatar
			base64_to_jpeg($_POST['data'], relativeUserDataFolder("avatars/".$adresse));
			
			//Redirection vers la page de modification de l'avatar
			//header('Location: parametres.php?c=avatar');
		}
	}
	
?><!DOCTYPE html>
<html>
	<head>
		<?php include(pagesRelativePath('common/head.php')); ?>
		<title>Utilisation de la WebCam pour changer d'avatar</title>
	</head>
	<body>
		<?php include(pagesRelativePath('common/pageTop.php')); ?>
		<div class="corps_page_nouvel_avatar">
			<h2 class="titre">Utilisation de la WebCam pour envoyer un nouvel avatar</h2>
			<a href="parametres.php?c=avatar"> Retour aux param&egrave;tres </a>
			<div id="container">
				
				<!-- Bouton d'activation de la webcam -->
				<div class="bouton_photo_webcam"><button onClick="navigator.getUserMedia(constraints, successCallback, errorCallback);">Activer la WebCam</button></div>
				
				<!-- Zone d'apparition de la webcam -->
				<video autoplay poster="<?php echo path_img_asset(''); ?>activer_webcam.png"></video>
				
				<!-- Bouton de prise de photo -->
				<div class="bouton_photo_webcam"><button id="bouton_prendre_photo">Prendre une photo</button></div>
				
				<!-- Zone d'apparition de la photo -->
				<canvas class="target_image_webcam_nouvel_avatar" id="target_image_webcam_nouvel_avatar"></canvas>
				
				<!-- Bouton d'enregistrement de la photo -->
				<div class="bouton_envoi_nouvel_avatar_from_webcam"><button onClick="send_snapshot_webcam_for_avatar();">Envoyer la photo</button></div>
				
				<!-- Formulaire d'envoi des données -->
				<form action="action.php?actionid=<?php echo $action; ?>" method="post" id="post_new_image_from_webcam"><input type="hidden" name="data" id="data" /></form>

			</div>
			
			<!-- Conseils pour les utilisateurs -->
			<b> Note : Il se peut que si vous utilisez une ancienne version de votre navigateur ou que vous utilisez Internet Explorer cette page ne fonctionne pas.</b>
			<p> Information : Il se peut que votre navigateur vous demande l'autorisation d'utiliser la webcam. Dans ce cas, acceptez-la. :</p>
			<?php echo code_inc_img(path_img_asset('accepte_webcam.jpg'), "Dans Firefox, cliquez sur Partager le p&eacute;riph&eacute;rique selectionn&eacute; pour accepter l'affichage de la webcam.", "", "", "margin:auto;"); ?>
			<!-- Fin de: Conseils pour les utilisateurs -->
		</div>
		<!-- Pied de page -->
		<hr /><?php include(pagesRelativePath('common/pageBottom.php')); ?>
		
		<!-- Inclusion des scripts Javascript spécifiques -->
		<?php echo code_inc_js(path_js_asset('webrtc_main.js')); ?>
		<!-- Fin de: Inclusion des scripts Javascript spécifiques -->
	</body>
</html>