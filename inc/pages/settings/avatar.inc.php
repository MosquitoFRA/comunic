<?php
/**
 * Change avatar settings
 *
 * @author Pierre HUBERT
 */

if(!isset($_SESSION))
	exit("Invalid call !");

?>
<h1>Avatar</h1>
<?php
//Vérifions si un avatar a déjà été posté
if ((isset($_FILES['avatar'])) AND ($_FILES['avatar']['error'] == 0))
{
	// Testons si l'extension est autorisée
	$infosfichier = pathinfo($_FILES['avatar']['name']);
	$extension_upload = $infosfichier['extension'];
	$extensions_autorisees = array('jpg', 'jpeg');
	
	//Génération du nom de l'avatar
	$extension = ".jpg";
	$nom_avatar = str_replace('@', '', sha1(time()).$extension);
	
	//On supprime l'ancien avatar si il y en a un
	if(file_exists('avatars/adresse_avatars/'.$_SESSION['ID'].'.txt'))
	{
		unlink(relativeUserDataFolder('avatars/'.file_get_contents(relativeUserDataFolder('avatars/adresse_avatars/'.$_SESSION['ID'].'.txt'))));
	}
	
	//On modifie l'adresse d'enregistrement de l'avatar
	file_put_contents(relativeUserDataFolder("avatars/adresse_avatars/".$_SESSION['ID'].".txt"), $nom_avatar);
	
	// On peut copier l'avatar vers le répertoire de destinations
	move_uploaded_file($_FILES['avatar']['tmp_name'], relativeUserDataFolder('avatars/'.$nom_avatar));
	
	echo "<h2>Terminer l'envoi</h2>";
	echo "<p>".code_inc_img(path_img_asset('succes.png'), "Succ&egrave;")." L'envoi a bien &eacute;t&eacute; effectu&eacute; !</p>";
}

//Check if we have to edit image
if(isset($_GET['editimage']))
{
	echo "<p>Veuillez choisir quelle partie de l'image sera votre avatar :</p>";
	
	echo avatar($_SESSION['ID'], "./", "", "", "photo"); ?>
	<style type="text/css">#photo{max-width: none !important;}</style>

	<script type="text/javascript">
		$(document).ready(function () {
			$('#photo').imgAreaSelect({
				handles: true,
				x1: 0, y1: 0, x2: 128, y2: 128,
				onSelectEnd: function (img, selection) {
					$('input[name="x1"]').val(selection.x1);
					$('input[name="y1"]').val(selection.y1);
					$('input[name="x2"]').val(selection.x2);
					$('input[name="y2"]').val(selection.y2);            
				},
				aspectRatio: '2:2',
			});
		});
	</script>
		
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?c=<?php echo $_GET['c']; ?>&editavatar" method="post">
		<input type="hidden" name="x1" value="0" />
		<input type="hidden" name="y1" value="0" />
		<input type="hidden" name="x2" value="128" />
		<input type="hidden" name="y2" value="128" />
		<input type="submit" name="submit" value="Enregistrer" />
	</form><?php

}

//Vérifions si il faut modifier l'avatar
if(isset($_GET['editavatar']) && isset($_POST['x1']) && isset($_POST['y1']) && isset($_POST['x2']) && isset($_POST['y2']))
{
	//On définit l'adresse de l'avatar
	$nom_avatar = str_replace('@', '', sha1(time()).".jpg");
	
	//On redimensionne l'image
	redimensionnne_enregistre_image(
		$_POST['x1'], 
		$_POST['x2'], 
		$_POST['y1'], 
		$_POST['y2'], 
		relativeUserDataFolder("avatars/".file_get_contents(relativeUserDataFolder("avatars/adresse_avatars/".$_SESSION['ID'].".txt"))), 
		relativeUserDataFolder("avatars/".$nom_avatar), 
		128, 
		128);
	
	//On modifie la date d'enregistrement de l'avatar
	file_put_contents(relativeUserDataFolder("avatars/adresse_avatars/".$_SESSION['ID'].".txt"), $nom_avatar);
	
	?><p><strong>F&eacute;licitations! Votre avatar a bien &eacute;t&eacute; configur&eacute;.</strong></p><?php
}

//Requete de l'existence d'un avatar
if(file_exists(relativeUserDataFolder('avatars/adresse_avatars/'.$_SESSION['ID'].".txt")))
{
	echo avatar($_SESSION['ID'], "./", 64, 64, "avatar");
}
else
{
	echo "Vous n'avez pas encore d&eacute;fini d'avatar. Voici l'avatar par d&eacute;faut :<img src='".webUserDataFolder('avatars/0.jpg')."' whidth='32' height='32' title='Vous pouvez définir votre avatar dans les parametres.' />";

	//We notice that there isn't any avatar yet
	$noAvatar = true;
}
?>

<!-- New avatar form -->
<h5>Formulaire d'envoi d'un avatar :</h5>
<form action='<?php $_SERVER['PHP_SELF']; ?>?c=avatar' name="Envoi d'un nouvel avatar" method='post' enctype="multipart/form-data">
	<table>
		<tr>
			<td>
				S&eacute;lectionnez le nouvel avatar
			</td>
			<td>
				<input type='file' name='avatar' />
			</td>
		</tr>
			<td>
				
			</td>
			<td>
				<input type='submit' value="Envoyer le nouvel avatar" />
			</td>
		</tr>
	</table>
</form>

<!-- Resize avatar -->
<?php
if(!isset($noAvatar)){
	?><div class="bouton_edit_image_parametres">
		<a href="<?php echo $_SERVER['PHP_SELF']; ?>?c=<?php echo $_GET['c']; ?>&editimage">
			<?php echo code_inc_img(path_img_asset('image_edit.png')); ?> Redimensionner votre avatar
		</a>
	</div><?<?php 
} ?>

<!-- Use Webcam -->
<div class="bouton_edit_image_parametres">
	<a href="action.php?actionid=26">
		<?php echo code_inc_img(path_img_asset('webcam.png')); ?> Utilisez votre WebCam pour changer d'avatar
	</a>
</div>

<!-- Delete avatar -->
<div class="bouton_edit_image_parametres">
	<a href="#" onClick="confirmaction('action.php?actionid=27&type=avatar', 'Voulez-vous vraiment supprimer votre avatar ?');">
		<?php echo code_inc_img(path_img_asset('image_delete.png')); ?> Supprimer votre avatar
	</a>
</div>
<?php