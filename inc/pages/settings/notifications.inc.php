<?php
/**
 * Change notifications settings
 *
 * @author Pierre HUBERT
 */

if(!isset($_SESSION))
	exit("Invalid call !");

//Configuration des notifications
if(isset($_POST['edit_notifications']))
{
	//On vérifie si il faut activer l'ancienne version de notifications
	$bloquenotification = (isset($_POST['bloquenotification']) ? 0 : 1);
	
	//On vérifie si les sons des notifications sont autorisés
	$bloque_son_notification = (isset($_POST['bloque_son_notification']) ? 1 : 0);
	
	//Modification de la base de données
	$sql = "UPDATE utilisateurs SET bloquenotification = ?, bloque_son_notification = ? WHERE ID = ?";
	$modif = $bdd->prepare($sql);
	$modif->execute(array($bloquenotification, $bloque_son_notification, $_SESSION['ID']));
	
	//Actualisation de la page
	echo "Enregistrement des modification termin&eacute;, actualisation de la page....";
	echo '<meta http-equiv="refresh" content="0;URL=parametres.php?c=notifications">';
	die();
}

//Si demandé, vider le cache des notifications
if(isset($_GET['vide_cache_notification']))
{
	//On supprime les entrées de la base de données
	$sql = "DELETE FROM notification WHERE ID_personne = ?";
	$suppression = $bdd->prepare($sql);
	$suppression->execute(array($_SESSION['ID']));
	
	?><font style="color: green"><?php echo code_inc_img(path_img_asset('succes.png')); ?> Le cache des notifications a &eacute;t&eacute; vid&eacute;.</font><?php
}

?><form action="<?php echo $_SERVER['PHP_SELF']; ?>?c=<?php echo $_GET['c']; ?>" method="post">
	<input type="hidden" name="edit_notifications" value="1"><!-- Correctif de formulaire : Il n'y a que des cases à cocher -->
	<p>Cette page vous permet de configurer le comportement de notifications.</p>
	<table>
		<tr><td>Activer l'ancienne version des notifications</td><td><input type='checkbox' name='bloquenotification' <?php if ($afficher['bloquenotification'] != 1) echo 'checked'; ?> /></td></tr>
		<tr><td>Bloquer le son de l'ancienne version de notifications</td><td><input type='checkbox' name='bloque_son_notification' <?php if ($afficher['bloque_son_notification'] == 1) echo 'checked'; ?> /></td></tr>
		<tr><td colspan="2"><input type="submit" value="Mettre &agrave; jour les param&egrave;tres de notification de Comunic" /></td></tr>
		<tr><td colspan="2"><p>Voici &agrave; quoi ressemble l'ancienne version des notifications :</p></td></tr>
		<tr><td colspan="2" style="text-align: center;"><?php echo code_inc_img(path_img_asset('notification_old.png'), "Ancienne version du syst&egrave;me de notification."); ?></td></tr>
		<tr><td colspan="2"><p>Vous pouvez &eacute;galement &eacute;couter le son produit par l'ancien syst&egrave;me de notifications:</p></td></tr>
		<tr><td colspan="2" style="text-align: center;">
			<audio controls>
				<source src='<?php echo path_audio_asset('notification.ogg'); ?>'></source>
				<source src='<?php echo path_audio_asset('notification.mp3'); ?>'></source>
				Votre navigateur est trop ancien pour &eacute;couter le son de l'ancien syst&egrave;me de notifications.
			</audio>
		</td></tr>
		<tr><td colspan="2">Il est possible de vider le cache du syst&egrave;me de notification :</td></tr>
	</table>
</form>
<!-- Pour vider le cache des notifications --><button onClick="document.location.href='<?php echo $_SERVER['PHP_SELF']; ?>?c=<?php echo $_GET['c']; ?>&vide_cache_notification'"> Vider le cache des notifications</button>
<!-- Nettoyage automatique des notifications --><?php include('inc/nettoyage_automatique_notifications.php'); ?>
	<?php		
