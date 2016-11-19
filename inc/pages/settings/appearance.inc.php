<?php
/**
 * Change appearance settings
 *
 * @author Pierre HUBERT
 */

if(!isset($_SESSION))
	exit("Invalid call !");

if(isset($_POST['color_menu']))
{
	//On vérifie si l'utilisateur veut l'ancien menu ou un menu moderne
	if(($_POST['color_menu'] == "blue") || ($_POST['color_menu'] == "dark") || ($_POST['color_menu'] == "light") || ($_POST['color_menu'] == "white"))
	{
		//Modification de la base de donnée pour un menu moderne
		$sql = "UPDATE utilisateurs SET old_menu = ?, color_menu = ? WHERE ID = ?";
		$modif = $bdd->prepare($sql);
		$modif->execute(array(0, $_POST['color_menu'], $_SESSION['ID']));
	}
	else
	{
		//Modification de la base de donnée pour l'ancien menu
		$sql = "UPDATE utilisateurs SET old_menu = 1, color_menu = 'none' WHERE ID = ".$_SESSION['ID'];
		$modif = $bdd->query($sql);
		//$modif->execute(array('none', $_SESSION['ID']));
	}
	
	//Actualisation de la page
	echo "Enregistrement des modification termin&eacute;, actualisation de la page....";
	echo '<meta http-equiv="refresh" content="0;URL=parametres.php?c=apparence">';
	die();
}
?><h3>Apparence du site</h3>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>?c=apparence" method="post">
	<table>
		<tr><td>Menu bleu</td><td><input type="radio" name="color_menu" value="blue" <?php echo ($afficher['color_menu'] == "blue" ? "checked" : ""); ?> /></td></tr>
		<tr><td>Menu noir</td><td><input type="radio" name="color_menu" value="dark" <?php echo ($afficher['color_menu'] == "dark" ? "checked" : ""); ?> /></td></tr>
		<tr><td>Menu l&eacute;ger</td><td><input type="radio" name="color_menu" value="light" <?php echo ($afficher['color_menu'] == "light" ? "checked" : ""); ?> /></td></tr>
		<tr><td>Menu blanc</td><td><input type="radio" name="color_menu" value="white" <?php echo ($afficher['color_menu'] == "white" ? "checked" : ""); ?> /></td></tr>
		<!--<tr><td>Ancienne version du menu</td><td><input type="radio" name="color_menu" value="old_menu" <?php echo ($afficher['old_menu'] == "1" ? "checked" : ""); ?> /></td></tr>-->
		<tr><td colspan="2"><input type="submit" value="Mettre &agrave; jour l'apparence de Comunic" /></td></tr>
	</table>
</form><?php