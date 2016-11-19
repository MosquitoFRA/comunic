<?php
/**
 * Text editing page
 *
 * @author Pierre HUBERT
 */

//Security
include(websiteRelativePath('securite.php'));

//On vérifie si il y a une demande d'édit de post
if(!isset($_GET['id']))
{
	//Redirection vers la page d'acceuil
	header('location: index.php');
	
	//On quitte la page
	exit();
}

//Getting post ID
$id_texte = $_GET['id']*1;
	
?><!DOCTYPE html>
<html>
	<head>
		<title>Edition du post</title>
		<?php include(pagesRelativePath('common/head.php')); ?>
	</head>
	<body class="metro">
		<?php include(pagesRelativePath('common/pageTop.php')); ?>
		<h1 class="titre">Edition d'un post</h1>
		<?php
			//On recherche le post
			$sql = "SELECT * FROM texte  WHERE ID = ? AND (ID_personne = ? || ID_amis = ?)";
			
			//SQL -> exécution de la requête
			$requete = $bdd->prepare($sql);
			$requete->execute(array($id_texte, $_SESSION['ID'], $_SESSION['ID']));
			
			//On vérifie si la demande est correcte
			ob_start();
			if(!$afficher_texte = $requete->fetch())
			{
				//Fin de l'interruption de l'envoi des données
				ob_end_clean();
				
				//On affiche un message d'erreur
				?><table><tr><td><img src="img/erreur.png" title="Erreur"></td><td>Le message demand&eacute; ne peut pas &ecirc;tre &eacute;dit&eacute;. Causes les plus probables : vous ne disposez pas des autorisations n&eacute;cessaires ou le message n'existe plus. <a href="contact.php">Contactez-nous</a> pour plus d'informations.</td></tr></table><?php
				
				//On arrête le chargement de la page
				exit();
			}
			ob_end_clean();
			
			if(preg_match('/endof/', $afficher_texte['texte']))
			{
				?><script>alert("Les images qui ont été postées ne peuvent pas être modifiées.");</script><?php
				?><input type="button" onClick="javascript:history.back(1)" value="Retour à la page précédente" /><?php
				
				//On quitte le script courant
				exit();
			}
			
			//On vérifie si la demande a déjà été postée
			if(isset($_POST['message']))
			{
				if($_POST['message'] != "")
				{
					if(preg_match('/endof/', $_POST['message']))
					{
						?><script>alert("Votre demande de modification n'a pas pu être reçue car elle présente un important problème de sécurité.");</script><?php
					}
					else
					{
						echo "<p>Application de la modification</p>";
						
						//SQL -> requête de modification
						$sql = "UPDATE texte SET texte = ? WHERE ( ID = ? AND (ID_personne = ? || ID_amis = ?))";
						
						//SQL-> exécution de la requête
						$update = $bdd->prepare($sql);
						$update->execute(array($_POST['message'], $id_texte, $_SESSION['ID'], $_SESSION['ID']));
						
						//Si il y a un sondage dans le texte
						if(isset($_POST['question_sondage']))
						{
							$question_sondage = $_POST['question_sondage'];
							
							//Si il y a une requête de modification de la question du sondage
							if($question_sondage != "")
							{
								//Mise à jour de la question du sondage
								update_sql("sondage", "question = ?", "ID_utilisateurs = ? AND ID_texte = ?", $bdd, array($question_sondage, $_SESSION['ID'], $id_texte));
							}
						}
						
						//Messsage de succès
						echo "<p>Application de la modification termin&eacute;e.</p>";
						echo "<p><a href='index.php'>Retour &agrave; votre page d'acceuil</a></p>";
						
						//On vérifie si une redirection automatique est possible
						if(isset($_GET['iduser']))
						{
							echo "<p>Redirection automatique en cours...</p>";
							?><meta http-equiv="refresh" content="0;URL=index.php?id=<?php echo $_GET['iduser'].(isset($_GET['page']) ? "&page=".$_GET['page'] : "" ); ?>"><?php
						}
						
						//Fermeture du script courant
						exit();
					}
				}
			}
			
			//Inclusion de TinyMce
			include(relativePath_assets('html/tinymce.html'));
			
			//Affichage de l'éditon du message
			echo "<form action='editpost.php?id=".$afficher_texte['ID'].(isset($_GET['iduser']) ? "&iduser=".$_GET['iduser'] : "" ).(isset($_GET['page']) ? "&page=".$_GET['page'] : "" )."' method='post'>";
			echo "<table style='text-align: center; margin: auto;'>";
				echo "<tr><td>";
				echo "<textarea name='message' id='textarea'>".$afficher_texte['texte']."</textarea>";
				//echo "<p>L'édition ne peut temporairement pas aboutir. Veuillez nous excuser du désagrément encouru.</p>"; //Bug ou développement SELEUMENT, ce message peut pertuber l'utilisateur final.
				echo "</td></tr>";
				
				if($afficher_texte['type'] == "sondage")
				{
					//On récupère les informations sur le sondage
					$infos_sondage = get_sondage_by_text_id($afficher_texte['ID'], $bdd);
					
					if(!$infos_sondage)
						echo "<p><b>Une erreur temporaire emp&ecirc;che de modifier le sondage rattach&eacute; au texte.</b></p>";
					else
					{
						?><tr>
							<td>
								<label>Question du sondage : </label>
								<div class="input-control text">
									<input type="text" name="question_sondage" value="<?php echo $infos_sondage[0]['question']; ?>" />
								</div>
							</td>
						</tr><?php
					}
				}
				
				echo "<tr><td>";
				echo "<input type='submit' value='Modifier' />";
				echo "</td></tr>";
			echo "</table>";
			echo "</form>";
		?>
		
		<hr /><?php
		//Pieds de page
		include(pagesRelativePath('common/pageBottom.php'));
		?>
	</body>
</html>