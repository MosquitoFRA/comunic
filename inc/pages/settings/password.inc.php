<?php
/**
 * Change password
 *
 * @author Pierre HUBERT
 */

if(!isset($_SESSION))
	exit("Invalid call !");

?>
<h3>Modification du mot de passe</h3>
<?php
//Vérification de l'existence de l'existence demande de modification de mot de passe
if((isset($_POST['oldpassword'])) && (isset($_POST['newpassword'])) && (isset($_POST['confirmnewpassword'])))
{
	//Vérification de la corresponde du nouveau mot de passe et de sa confirmation
	if($_POST['newpassword'] != $_POST['confirmnewpassword'])
	{
		echo "<p><font color='#FF0000'><p>Erreur : Le nouveau mot de passe et sa confirmation ne correspondent pas. </font></p> \n";
	}
	else
	{
		//Requete de vérification de l'ancien mot de passe
		$oldpass = $bdd->query("SELECT * FROM utilisateurs WHERE ID = ".$_SESSION['ID']);
		$test = $oldpass->fetch();
		$oldpass->closeCursor();
		
		//Hachage du mot de passe saisi dans le formulaire
		$oldpassword = sha1($_POST['oldpassword']);
		$oldpassword = crypt($oldpassword, $oldpassword);
		
		//Vérification
		if($oldpassword != $test['password'])
		{
			echo "<p><font color='#FF0000'><p>Erreur : l'ancien mot de passe saisi est invalide. </font></p> \n";
		}
		else
		{
			//Maintenant que tous les tests ont étés passés avec succès, on peut hacher et modifier le mot de passe dans al base de donnéesss
			$motdepasse = sha1($_POST['newpassword']);
			$motdepasse = crypt($motdepasse, $motdepasse);
			
			
			//Enregistrement de la modification dans la base de donnée
			$sql = "UPDATE utilisateurs SET password = ? WHERE ID = ?";
			$modif = $bdd->prepare($sql);
			$modif->execute(array($motdepasse, $_SESSION['ID']));
			
			//Affichage d'un message de succès
			echo "<p>".code_inc_img(path_img_asset('succes.png'), "Succ&egrave;")."Le mot de passe a &eacute;t&eacute; modifi&eacute; avec succ&egrave;s.</p>";
			
			//Vérification de l'autorisation d'envoi de mails
			if($active_envoi_mail == "oui")
			{
				//Envoi d'un message au demandé
				$send_mail = true;
				$sujet = "Modification de votre mot de passe";
				$description_rapide = "Vous avez changé votre mot de passe.";
				$nom_destinataire = $afficher['prenom']." ".$afficher['nom'];
				$adresse_mail_destinataire = $afficher['mail'];
				
				//Rechargement des informations
				$afficher = cherchenomprenom($_SESSION['ID'], $bdd); 
				
				$texte_message = "
				<h3 class='titre'>Modification de votre mot de passe</h3>
				<p>Ce message vous a &eacute;t&eacute; adress&eacute; automatiquement afin de vous informer que vous (ou quelqu'un se passant pour vous) avez modifi&eacute; votre mot de passe avec succ&egrave;s depuis la page de param&egrave;tres.
				Nous vous recommandons de modifier votre mot de passe r&eacute;guli&egrave;rement afin d'&eacute;viter que votre mot de passe soit vol&eacute; et si c'est le cas, de le modifier imm&eacute;diatement.</p>
				".$info_mail_securite."
				<p><strong>Important : Si vous n'avez pas chang&eacute; votre mot de passe, changez-le et contactez-nous. Il se peut que quelqu'un ait pirat&eacute; votre compte et r&eacute;cup&eacute;r&eacute; votre mot de passe.</strong></p>
				<p><a href='".$urlsite."'>Connectez-vous</a> pour acc&eacute;der &agrave; toute les param&egrave;tres de Comunic.</a></p>
				";
				
				//Envoi du message
				include('inc/envoi_mail.php');
			}
		}
	}
}
?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>?c=password" method='post'>
	<table>
		<tr><td>Ancien mot de passe</td><td><input type='password' name='oldpassword' /></td></tr>
		<tr><td>Nouveau mot de passe</td><td><input type='password' name='newpassword' /></td></tr>
		<tr><td>Confirmer le nouveau mot de passe</td><td><input type='password' name='confirmnewpassword' /></td></tr>
		<tr><td>Confirmer la modification</td><td><input type='submit' value='Modifier le mot de passe' /></td></tr>
	</table>
</form>
<?php