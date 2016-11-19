<?php
/**
 * Change user background image
 *
 * @author Pierre HUBERT
 */

if(!isset($_SESSION))
	exit("Invalid call !");

?>
<h3>Image de fond</h3>
<?php
//Vérifions si une image de fond a déjà été posté
if ((isset($_FILES['imgfond'])) AND ($_FILES['imgfond']['error'] == 0))
{
	// Testons si l'extension est autorisée
	$infosfichier = pathinfo($_FILES['imgfond']['name']);
	$extension_upload = $infosfichier['extension'];
	$extensions_autorisees = array('jpg', 'jpeg');
	
	//On supprime l'ancienne image de fond si il y en a une
	if(file_exists(relativeUserDataFolder('imgfond/adresse_imgfond/'.$_SESSION['ID'].'.txt')))
	{
		unlink(relativeUserDataFolder('imgfond/'.file_get_contents(relativeUserDataFolder('imgfond/adresse_imgfond/'.$_SESSION['ID'].'.txt'))));
	}
	
	//Définition de l'adresse de l'image de fond
	$adresse_img_fond = sha1(time()).".jpg";
	
	//On modifie la date d'enregistrement de l'image de fond
	file_put_contents(relativeUserDataFolder("imgfond/adresse_imgfond/".$_SESSION['ID'].".txt"), $adresse_img_fond);
	
	// On peut valider l'image de fond et copier vers son répertoire de destination
	move_uploaded_file($_FILES['imgfond']['tmp_name'], relativeUserDataFolder('imgfond/'.$adresse_img_fond));

	echo "<p>".code_inc_img(path_img_asset('succes.png'), "Succ&egrave;")." L'envoi a bien &eacute;t&eacute; effectu&eacute; !</p>";
	
}

//Vérifions si il faut redimmensionner l'image de fond
if(isset($_GET['editimage']))
{
	echo "<p>Veuillez maintenant choisir quelle partie de l'image sera votre image de fond :</p>";
	
	echo imgfond($_SESSION['ID'], "./", "", "", "photo"); ?>
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
				maxHeight: 200,
			});
		});
	</script>
		
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?c=<?php echo $_GET['c']; ?>&editimgfond" method="post">
		<input type="hidden" name="x1" value="0" />
		<input type="hidden" name="y1" value="0" />
		<input type="hidden" name="x2" value="128" />
		<input type="hidden" name="y2" value="128" />
		<input type="submit" name="submit" value="Enregistrer" />
	</form><?php
	
	//On évite les problèmes
	die();

}

//Vérifions si il faut modifier l'image de fond
if(isset($_GET['editimgfond']) && isset($_POST['x1']) && isset($_POST['y1']) && isset($_POST['x2']) && isset($_POST['y2']))
{
	//Définition de l'adresse de l'image de fond
	$adresse_img_fond = sha1(time()).".jpg";
	
	//On redimensionne l'image
	redimensionnne_enregistre_image(
		$_POST['x1'], 
		$_POST['x2'], 
		$_POST['y1'], 
		$_POST['y2'], 
		relativeUserDataFolder('imgfond/'.file_get_contents(relativeUserDataFolder("imgfond/adresse_imgfond/".$_SESSION['ID'].".txt"))), 
		relativeUserDataFolder("imgfond/".$adresse_img_fond), 
		$_POST['x2']-$_POST['x1'],
		200, 
		"no"
	);
	
	//On modifie la date d'enregistrement de l'avatar
	file_put_contents(relativeUserDataFolder("imgfond/adresse_imgfond/".$_SESSION['ID'].".txt"), $adresse_img_fond);
	
	//Message de succès
	?><p><strong>F&eacute;licitations! Votre nouvelle image de fond a bien &eacute;t&eacute; configur&eacute;e.</strong></p><?php
}

//Requete de l'existence d'une image de fond
if(file_exists(relativeUserDataFolder("imgfond/adresse_imgfond/".$_SESSION['ID'].".txt")))
{
	echo imgfond($_SESSION['ID'], "./", "", 32, "img_fond");
}
else
{
	echo "Vous n'avez pas encore d&eacute;fini d'image de fond. Vous pouvez en envoyer une sur cette page, ce qui permettra de la personnaliser en y ajoutant une touche de votre personnalit&eacute;.";
}
?>

<!-- New background image form -->
<h5>Formulaire d'envoi d'une image de fond :</h5>
<form action='<?php $_SERVER['PHP_SELF']; ?>?c=imgfond' name="Envoi d'une nouvelle image de fond" method='post' enctype="multipart/form-data">
	<table>
		<tr>
			<td>
				S&eacute;lectionnez la nouvelle image de fond (grande et rectangulaire si possible) :
			</td>
			<td>
				<input type='file' name='imgfond' />
			</td>
		</tr>
		<tr>
			<td>
				Confirmer l'envoi
			</td>
			<td>
				<input type='submit' value="Modifier l'imgage de fond" />
			</td>
		</tr>
	</table>
</form>

<!-- Resize image link -->
<div class="bouton_edit_image_parametres">
	<a href="<?php echo $_SERVER['PHP_SELF']; ?>?c=<?php echo $_GET['c']; ?>&editimage">
		<?php echo code_inc_img(path_img_asset('image_edit.png')); ?> Redimensionner votre image de fond
	</a>
</div>

<!-- Delete background image link -->
<div class="bouton_edit_image_parametres">
	<a href="#" onClick="confirmaction('action.php?actionid=27&type=imgfond', 'Voulez-vous vraiment supprimer votre image de fond ?');">
		<?php echo code_inc_img(path_img_asset('image_delete.png')); ?> Supprimer votre image de fond
	</a>
</div>
<?php