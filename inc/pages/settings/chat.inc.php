<?php
/**
 * Change chat settings
 *
 * @author Pierre HUBERT
 */

if(!isset($_SESSION))
	exit("Invalid call !");

//On vérifie si il faut vider le chat
if(isset($_GET['vide']))
{
	//Requete de suppression
	$sql = 'DELETE FROM chat WHERE ID_personne = '.$_SESSION['ID'].' ';
	
	//Execution de la requete
	$videchat = $bdd->query($sql);
	
	//On affiche un message de succès
	echo "<p>".code_inc_img(path_img_asset('succes.png'), "Succ&egrave;")." Le chat a bien &eacute;t&eacute; vid&eacute; !</p>";
}

?>
<!-- Parametres du chat -->
<h3>Parametres du chat</h3>
<p>Affichage de tous vos posts</p>
<table>
	<?php
		//Requete de recherche de post
		$sql='SELECT * FROM chat WHERE ID_personne = '.$_SESSION['ID'];
		
		//Execution de la requete
		$requete = $bdd->query($sql);
		
		//Affichage des résultats
		while ($afficherchat = $requete->fetch())
		{
			?>
			<tr>
				<td><?php echo $afficherchat['date_envoi']; ?></td>
				<td><?php echo corrige_accent_javascript(bloquebalise(corrige_caracteres_speciaux($afficherchat['message']))); ?></td>
			</tr>
			<?php
		}
	?>
</table>
<p><a href='<?php echo $_SERVER['PHP_SELF']; ?>?c=chat&vide=yes'>Vider l'historique du chat</a></p>
<!-- Fin de: parametres du chat -->
<?php