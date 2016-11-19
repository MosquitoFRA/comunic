<?php
/**
 * Comunic improvement forum
 *
 * @author Pierre HUBERT
 */

isset($_SESSION) OR exit("Invalid call - ".$_SERVER['PHP_SELF']);


?><!DOCTYPE html>
<html>
	<head>
		<title>Forum - Comunic</title>
		<style>
			.retour {
				text-align: center;
			}
			
			.infouser {
				width: 25%;
			}
			
			.post {
				width: 75%;
			}
			
			.liresujet {
				width: 950px;
				max-width: 950px;
			}
			
			.infoleft {
				font-size: 75%
			}
		</style>
		<?php include(pagesRelativePath('common/head.php')); ?>
	</head>
	<body>
		<?php include(pagesRelativePath('common/pageTop.php')); ?>
		<?php
			if(!isset($_GET['action']))
			{
				if(isset($_SESSION['ID']))
				{
					?>
						<table style="margin: auto;">
							<tr>
								<td>
									<?php echo code_inc_img(path_img_asset('page_add.png')); ?>
								</td>
								<td>
									<a href='forum.php?action=nouvsujet'>
										Nouveau sujet
									</a>
								</td>
							</tr>
						</table>
					<?php
					
				}
				else
				{
					?>
						<p><i>Vous devez &ecirc;tre connect&eacute; pour pouvoir poster un nouveau sujet</i></p>
					<?php
				}
					//Affichage de tous les sujets
					$sql = 'SELECT * FROM forum_sujet ORDER BY ID DESC';
					
					//Exécution de la requete
					$requete = $bdd->query($sql);
					
					//Affichage des résultat
					echo '<table align="center" border="1" cellspacing="0" cellpadding="4">';
					echo '<tr><td>Titre du sujet</td><td>Identifiant de la personne</td><td>Date d\'envoi</td></tr>';
					while($afficher = $requete->fetch())
					{
						echo '<tr><td><a href="forum.php?action=affichersujet&id='.$afficher['ID'].'">'.$afficher['titre'].'</a></td><td>';
						
						$info = cherchenomprenom($afficher['ID_personne'], $bdd);
						
						echo $info['prenom']." ".$info['nom'];
						
						echo '</td><td>'.$afficher['date_envoi'].'</td></tr>';
					}
					echo '</table>';
			}
			else
			{
				?><p class='retour'><a href='forum.php'>Retour &agrave; l'acceuil du forum</a></p><?php
				
				if($_GET['action'] == 'nouvsujet')
				{
					
					if(isset($_SESSION['ID']))
					{
						if(isset($_POST['titre']) AND isset($_POST['sujet']))
						{
							if($_POST['titre'] != '' AND $_POST['sujet'] != '')
							{
								//On enregistre le sujet dans la base de données
								$sql = 'INSERT INTO forum_sujet (titre, ID_personne, date_envoi, sujet) VALUES (?, ?, NOW(), ?) ';
								
								//Exécution de la requete
								$insertion = $bdd->prepare($sql);
								$insertion->execute(array( $_POST['titre'], $_SESSION['ID'], $_POST['sujet']));
								
								//On affiche un message de succès et on bloque l'affichage du reste de la page
								?><p>Le message a bien &eacute;t&eacute; post&eacute;. <a href='forum.php'> Retour &agrave; l'acceuil du forum</a></p><?php
								exit();
							}
							else
							{
								echo '<p style="color: red;">Certains champs n\'ont pas été remplis !</p>';
							}
						}
						
						include(relativePath_assets('html/tinymce.html'));
						?>
						Nouveau sujet (faites attention aux fautes d'orthographe) :
						<form action='forum.php?action=nouvsujet' method='post'>	
							<table>
								<tr>
									<td>
										Titre du sujet : <input type='text' name='titre' <?php if(isset($_POST['titre'])) { echo 'value="'.$_POST['titre'].'"' ; } ?> />
									</td>
								</tr>
								<tr>
									<td>
										<textarea name='sujet' id='textarea'><?php if(isset($_POST['sujet'])) { echo $_POST['sujet']; } ?></textarea>
									</td>
								</tr>
								<tr>
									<td>	
										<input type='submit' value='Poster le sujet' />
									</td>
								</tr>
							</table>
						</form>
						<?php
					}
					else
					{
						?><p>Erreur : vous devez &ecirc;tre connect&eacute; pour poster un nouveau sujet. <a href='connexion.php'>Connexion</a></p><?php
					}
				}
				elseif($_GET['action'] == 'affichersujet')
				{
					if(isset($_GET['id']))
					{
						if($_GET['id'] != "")
						{
							if(isset($_SESSION['ID']))
							{
								//On vérifie si il ne faut pas poster une réponse
								if(isset($_POST['reponse']))
								{
									if($_POST['reponse'] != '')
									{
										//On enregistre la réponse
										$sql = 'INSERT INTO forum_reponse (ID_personne, ID_sujet, date_envoi, reponse) VALUES (?, ?, NOW(), ?) ';
										
										//Exécution de la requete
										$insertion = $bdd->prepare($sql);
										$insertion->execute(array($_SESSION['ID'], $_GET['id'], $_POST['reponse']));
										
										//On affiche une message de succès
										echo '<p>La r&eacute;ponse a &eacute;t&eacute; post&eacute;e avec succ&egrave;s.</p>';
									}
								}
							}
							
							//On recherche le sujet pour l'afficher
							$info_sujet = info_sujet($_GET['id'], $bdd);
							
							//On affiche le sujet
							
							//On recherche les informations de la personne
							$info_personne = cherchenomprenom($info_sujet['ID_personne'], $bdd); 
							
							
							//On vérifie si la personne est autorisée à supprimer la réponse
							if(isset($_SESSION['ID']))
							{
								if($_SESSION['ID'] == $info_personne['ID'])
								{
									//On vérifie si une demande a déjà été postée
									if(isset($_GET['suppsujet']))
									{
										//On vérifie si c'est le bon commentaire et la bonne personne, ce serai dommage...
										if($_GET['suppsujet'] == $info_sujet['ID'])
										{
											//On supprime alors le sujet
											$sql = 'DELETE FROM forum_sujet WHERE ID = '.$_GET['id'];
											
											//Execution de la requete
											$supp = $bdd->query($sql);
											
											//On supprime les commentaires associés
											$sql = 'DELETE from forum_reponse WHERE ID_sujet = '.$_GET['id'];
											
											//Execution de la requete
											$supp = $bdd->query($sql);
											
											//Rafraîchissement de la page
											echo 'Suppression termin&eacute;e, redirection en cours...';
											echo '<meta http-equiv="refresh" content="0;URL=forum.php">';
											exit();
										}
									}
									
									//On affiche un message de suprression
									?><a href='forum.php?id=<?php echo $_GET['id']; ?>&action=affichersujet&suppsujet=<?php echo $_GET['id']; ?>' title='Supprimer le sujet'>Supprimer le sujet</a> <br /><?php
								}
							}
							
							?>	
							<table align="center" border="1" cellspacing="0" cellpadding="4" class='liresujet'>
								<tr>
									<td class='infouser'>
										Par <b><?php echo $info_personne['prenom']." ".$info_personne['nom']; ?></b><br />
										<span class='infoleft'><i>Date d'inscription :</i> <?php echo $info_personne['date_creation']; ?></span><br />
										<span class='infoleft'><i>Date d'envoi :</i> <?php echo $info_sujet['date_envoi']; ?></span><br />
									</td>
									<td class='post'><?php echo $info_sujet['sujet']; ?></td>
								</tr>
							<?php
							
							//On récupère les réponse qui ont été postées
							$info_reponses = info_reponses($info_sujet['ID'], $bdd);
							
							//On affiche les réponses
							foreach($info_reponses as $afficher)
							{
								$info_personne = cherchenomprenom($afficher['ID_personne'], $bdd);
								?>
									<tr>
										<td class='infouser'>
											<?php
												//On vérifie si la personne est autorisée à supprimer la réponse
												if(isset($_SESSION['ID']))
												{
													if($_SESSION['ID'] == $info_personne['ID'])
													{
														//On vérifie si une demande a déjà été postée
														if(isset($_GET['suppcom']))
														{
															//On vérifie si c'est le bon commentaire et la bonne personne, ce serai dommage...
															if($_GET['suppcom'] == $afficher['ID'])
															{
																//On supprime alors le commentaire
																$sql = 'DELETE FROM forum_reponse WHERE ID = '.$afficher['ID'];
																
																//Execution de la requete
																$supp = $bdd->query($sql);
																
																//Rafraîchissement de la page
																echo 'Suppression termin&eacute;e, redirection en cours...';
																echo '<meta http-equiv="refresh" content="0;URL=forum.php?action=affichersujet&id='.$_GET['id'].'">';
																exit();
															}
														}
														
														//On affiche un message de suprression
														?><a href='forum.php?id=<?php echo $_GET['id']; ?>&action=affichersujet&suppcom=<?php echo $afficher['ID']; ?>' title='Supprimer la r&eacute;ponse'><?php echo code_inc_img(path_img_asset('supp.png')); ?></a> <br /><?php
													}
												}
											?>
											Par <b><?php echo $info_personne['prenom']." ".$info_personne['nom']; ?></b><br />
											<span class='infoleft'><i>Date d'inscription :</i> <?php echo $info_personne['date_creation']; ?></span><br />
											<span class='infoleft'><i>Date d'envoi :</i> <?php echo $afficher['date_envoi']; ?></span><br />
										</td>
										<td class='post'><?php echo $afficher['reponse']; ?></td>
									</tr>
								<?php
							}
							
							 //On vérifie si la personne peut poster une reponse
							 if(isset($_SESSION['ID']))
							 {
								?>
									<tr>
										<td class='infouser'>
											Poster une r&eacute;ponse
										</td>
										<td>
											<form action='forum.php?action=affichersujet&id=<?php echo $info_sujet['ID']; ?>' method='post'>
												<?php include(relativePath_assets('html/tinymce.html')); ?>
												<textarea name='reponse' id='textarea'></textarea><br />
												<span class="text-align: center"><input type='submit' value='Envoyer la r&eacute;ponse' /></span>
											</form>
										</td>
									</tr>
								<?php
							 }
							
							echo '</table>';
						}
						else
						{
							header('location: forum.php');
						}
					}
					else
					{
						header('location: forum.php');
					}
				}
			}
		?>
		<p><b>Astuce:</b> Pour retrouver facilement un sujet ou une r&eacute;ponse qui vous int&eacute;resse vous pouvez utiliser les touches Ctrl + F de votre clavier pour effectuer une recherche dans la page.</p>
		<hr />
		<?php include(pagesRelativePath('common/pageBottom.php')); ?>
	</body>
</html>