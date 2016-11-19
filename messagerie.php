<?php
//Inclusion du script de sécurité
include('securite.php');

//Init page
include('inc/initPage.php');

//Préparation de l'affichage de la page
$confirmation_suppression = "Voulez-vous vraiment supprimer ce message ?";

//Fonction d'affichage de confirmation de suppression de message
function confirmation_supp_messsage($message, $destination)
{
	return '<img onClick="confirmaction(\''.$destination.'\', \''.$message.'\')" src=\'img/supp.png\' title=\'Supprimer le message\' />';
}

//On vérifie si il faut supprimer un message
if(isset($_GET['suppmessageid']))
{
	//On supprime le message
	suppmessagefromid($_SESSION['ID'], $_GET['suppmessageid'], $bdd);
}
?><!DOCTYPE html>
<html>
	<head>
		<title>Votre messagerie interne</title>
		<?php include(pagesRelativePath('common/head.php')); ?>
	</head>
	<body>
		<?php include(pagesRelativePath('common/pageTop.php')); ?>
		<h1 class='titre'>Messagerie interne</h1>
		<?php if(!isset($_GET['write'])) { ?><p style="text-align: center;"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?write"><?php echo $lang[64]; ?></a></p><?php } else { ?><p style="text-align: center;"><a href="<?php echo $_SERVER['PHP_SELF']; ?>">Retour &agrave; l'acceuil de la messagerie</a></p><?php } ?>
		<?php
			if(!isset($_GET['voire']) && !isset($_GET['write']))
			{
				//On récupère les messages
				$messages = recherchermessageutilisateur($_SESSION['ID'], $bdd);
				
				//On prépare l'affichage des messages
				$entetemessages = '<table align="center"><tr style="background-color: rgb(116, 167, 226); color: #FFFFFF;"><td>Expediteur</td><td>Objet</td><td>Date de l\'envoi</td><td>Action</td></tr>';
				
				if(count($messages['lu'])!= 0 || count($messages['nonlu'])!= 0)
				{
					echo $entetemessages;
				}
				else
				{
					echo "<p>Il n'y a pour le moment aucun message &agrave; afficher</p>";
				}
				
				//On vérifie si il y a des messages non lus
				if(count($messages['nonlu']) != 0)
				{
					//On affiche les messages non lus
					//echo '<p style="text-align: center;">Messages non lus</p>';
					
					foreach($messages['nonlu'] as $afficher)
					{
						$infoexpediteur = cherchenomprenom($afficher['ID_expediteur'], $bdd);
						
						echo'<tr><td>'.$infoexpediteur['prenom'].' '.$infoexpediteur['nom'].'</td><td><b>'.affiche_smile(bloquebalise($afficher['objet'])).'</b></td><td>'.$afficher['date_envoi'].'</td><td><a href="'.$_SERVER['PHP_SELF'].'?voire='.$afficher['ID'].'"><img src="img/voir.png" title="Voir le message" /></a> '.confirmation_supp_messsage($confirmation_suppression, $_SERVER['PHP_SELF'].'?suppmessageid='.$afficher['ID']).'</td></tr>';
					}
					
				}
				
				//On vérifie si il y a des messages lus
				if(count($messages['lu'])!= 0)
				{
					foreach($messages['lu'] as $afficher)
					{
						$infoexpediteur = cherchenomprenom($afficher['ID_expediteur'], $bdd);
						
						echo'<tr><td>'.$infoexpediteur['prenom'].' '.$infoexpediteur['nom'].'</td><td>'.affiche_smile(bloquebalise($afficher['objet'])).'</td><td>'.$afficher['date_envoi'].'</td><td><a href="'.$_SERVER['PHP_SELF'].'?voire='.$afficher['ID'].'"><img src="img/voir.png" title="Voir le message" /></a> '.confirmation_supp_messsage($confirmation_suppression, $_SERVER['PHP_SELF'].'?suppmessageid='.$afficher['ID']).'</td></tr>';
					}
				}
				
				if(count($messages['lu'])!= 0 || count($messages['nonlu'])!= 0)
				{
					echo "</table>";
				}
			}
			elseif(!isset($_GET['write']))
			{
				?><h2 class='titre'>Affichage d'un message</h2>
				<p style='text-align: center'><a href='<?php echo $_SERVER['PHP_SELF']; ?>'> Retour &agrave; l'interface de messagerie principale</a></p><?php
				
				//On récupère le message
				$messages = recherchermessageutilisateur($_SESSION['ID'], $bdd, $_GET['voire']);
				
				if (count($messages['lu']) != 0 || count($messages['nonlu']) != 0)
				{
					//On enregistre le message dans $message
					if(count($messages['lu']) != 0)
					{
						$message = $messages['lu'][0];
					}
					else
					{
						$message = $messages['nonlu'][0];
					}
					
					//On met le message en vu
					metslemessageenvu($_GET['voire'], $bdd);
					
					$infoexpediteur = cherchenomprenom($message['ID_expediteur'], $bdd);
					
					//Affichage du message
					?><table align = 'center'>
						<tr>
							<td>
								<table border="1" align="center" cellspacing="0" cellpadding="5" width='100%'>
									<tr>
										<td>Exp&eacute;diteur : <?php echo $infoexpediteur['prenom'].' '.$infoexpediteur['nom']; ?></td>
										<td>Objet : <?php echo affiche_smile(bloquebalise($message['objet'])); ?></td>
										<td>Date d'envoi: <?php echo $message['date_envoi']; ?> </td>
										<td> Actions : <img  onClick="confirmaction('<?php echo $_SERVER['PHP_SELF']; ?>?suppmessageid=<?php echo $message['ID']; ?>', '<?php echo $confirmation_suppression; ?>')"  src="img/supp.png" title="Supprimer le message" /></td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td>
								<?php echo affiche_smile(bloquebalise($message['message'])); ?>
							</td>
						</tr>
					</table><?php
				}
				else
				{
					?><table align='center'>
						<tr>
							<td><img src='img/erreur.png' /></td>
							<td>Impossible d'afficher le message demand&eacute;. Il est possible que vous n'ayez les autorisations n&eacute;cessaires ou que ce message n'existe plus. N'hésitez pas à nous <a href='contact.php'>contacter</a> pour de plus amples informations.</td>
						</tr>
					</table><?php
				}
			}
			else
			{
				if(isset($_POST['message']) && (isset($_POST['sujet'])) && (isset($_POST['destinataire'])))
				{
					//We send the message
					if($_POST['message'] != "" && $_POST['sujet'] != "")
					{
						//We send the message
						if(sendmessage($_SESSION['ID'], $_POST['destinataire'], $_POST['sujet'], $_POST['message'], $bdd))
						{
							//Envoi d'une notification à l'autre utilisateur
							sendnotification_one_user($_SESSION['ID'], $_POST['destinataire'], "vous a envoy&eacute; un message.", $bdd, "messagerie.php");
							
							?><p style="text-align: center;"><img src="img/succes.png" title="Succ&egrave;" alt="V" />Le message a bien &eacute;t&eacute; envoy&eacute;.</p><?php
							
							//Pour plus de sécurité on supprime les variables d'information
							unset($_POST['sujet']);
							unset($_POST['message']);
							unset($_POST['destinataire']);
						}
						else
						{
							?><table>
								<tr>
									<td><img src="img/exclamation.png" title="Erreur" alt="X" /></td>
									<td>Une erreur est survenue lors de l'envoi du message. L'exp&eacute;diteur est peut-&ecirc;tre incorrecte ou notre base de donn&eacute;s est temporairement inaccessible. Veuillez r&eacute;essayer ult&eacute;rieurement.</td>
								</tr>
							</table><?php
						}
					}
					else
					{
						?><script>alert("Le sujet ou le message n'a pas été spécifié !");</script><?php
					}
				}
					?><form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?write" name="Send a message">
						<table style="margin: auto;">
							<tr>
								<td><?php echo $lang[72]; ?> :</td>
								<td><?php echo $afficher['prenom']." ".$afficher['nom']; ?></td>
							</tr>
							<tr>
								<td>Sujet</td>
								<td><input type="text" name="sujet" value="<?php if(isset($_POST['sujet'])) echo $_POST['sujet']; ?>" /></td>
							</tr>
							<tr>
								<td><?php echo $lang[73]; ?> :</td>
								<td><select name="destinataire"><?php
								//Requete de recherche dans la table personnes
								$sql = "SELECT * FROM utilisateurs ORDER BY prenom";
								$resultat = $bdd->query($sql);
								
								//Affichage  des résultats
								while($afficher = $resultat->fetch())
								{
									echo "<option value='".$afficher['ID']."' ".(isset($_POST['destinataire']) ? ( $afficher['ID'] == $_POST['destinataire'] ? "selected" : "" ) : "" ).">".bloquebalise($afficher['prenom']).' '.bloquebalise($afficher['nom'])." ";
								}
								
								//Fermeture de la requete
								$resultat->closeCursor();
								?></select>
								</td>
							</tr>
							<tr>
								<td>
									Message
								</td>
								<td>
									<textarea name="message" rows="3" cols="30"><?php if(isset($_POST['message'])) echo $_POST['message']; ?></textarea>
								</td>
							</tr>
							<tr>
								<td><input type="submit" value="<?php echo $lang[31]; ?>" /></td>
								<td><em><?php echo $lang[74]; ?></em></td>
							</tr>
						</table>
					</form><?php
			}
		?>
		<hr><?php include(pagesRelativePath('common/pageBottom.php')); ?>
	</body>
</html>