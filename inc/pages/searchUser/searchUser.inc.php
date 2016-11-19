<?php
/**
 * Search somebody
 *
 * @author Pierre HUBERT
 */

isset($_SESSION) OR exit('Invalid call - '.$_SERVER['PHP_SELF']);

//On détermine un ID de session pour l'utilisateur
$id_session = (isset($_SESSION['ID']) ? $_SESSION['ID'] : 0);

//On corrige la requête si nécessaire
if(isset($_GET['q']) AND !isset($_POST['nom']))
		$_POST['nom'] = $_GET['q'];
?>
<!DOCTYPE html>
<html>
	<head>
		<?php include(pagesRelativePath('common/head.php')); ?>
		<title><?php echo $lang[39]; ?></title>
	</head>
	<body>
		<?php include(pagesRelativePath('common/pageTop.php')); ?>
		<h1 class='titre'><?php echo $lang[40]; ?></h1>
		<table align='center'>
			<tr>
				<td>
					<form action='recherche.php' name="Recherche d'utilisateurs" method='post'>
							<input type='text' name='nom' size="53" autocomplete="off" id="searchuser" value="<?php echo(isset($_POST['nom']) ? $_POST['nom']: ""); ?>"  placeholder="<?php echo $lang[40]; ?>" style="color:black;" /> 
						<input type='submit' value='<?php echo $lang[39]; ?>' />
					</form>
				</td>
			</tr>
			<tr>
				<td>
					<?php
					if(isset($_POST['nom']))
					{
						//Mettons "" à la place de "%"
						$nom = str_replace("%", "", $_POST['nom']);
						
						//Vérifions si la personne ne veut pas tout afficher
						if(($nom != '') && ($nom != '%')&& ($nom != ' '))
						{
							//Continuons de protéger les personnes et la table
							$nom = str_replace("'", '"', $nom);
							
							//Rendons flexible la requete
							$nom = str_replace(' ', '%', $nom);
							
							//Nous pouvons maintenant faire la recherche
							//Requête SQL
							//$sql = "SELECT * FROM utilisateurs WHERE (nom LIKE '%".$nom."%') || (prenom LIKE '%".$nom."%') || (CONCAT(prenom, '%', nom) LIKE '%".$nom."%' ) || (CONCAT(prenom, ' ', nom) LIKE '%".$nom."%' ) || (mail LIKE '%".$nom."%') "; //Avec E-mail
							$sql = "SELECT * FROM utilisateurs WHERE (nom LIKE '%".$nom."%') || (prenom LIKE '%".$nom."%') || (CONCAT(prenom, '%', nom) LIKE '%".$nom."%' ) || (CONCAT(prenom, ' ', nom) LIKE '%".$nom."%' ) "; //Sans E-mail
							
							//Exécution de la requete SQL
							$recherche = $bdd->query($sql);
							
							//Affichage des résultats
							echo "<table>";
							while($afficherrecherche = $recherche->fetch())
							{
								//On vérifie qu'il ne s'agit pas de l'utilisateur effectuant la recherche
								if($afficherrecherche['ID'] != $id_session AND ($id_session == 0 ? ( ($afficherrecherche['pageouverte'] == 1 ? true : false) ) : true))
								{
									?>
									<tr>
										<td>
											<table>
												<tr>
													<td><?php
														echo avatar($afficherrecherche['ID'], "./", 64, 64);
													?></td>
													<td><a href='index.php?id=<?php echo $afficherrecherche['ID']; ?>' title='Voire sa page'><?php echo $afficherrecherche['prenom']." ".$afficherrecherche['nom']; ?></a></td>
													<td>
														<?php
															//On vérifie si la page est vérifiée
															if($afficherrecherche['page_verifiee'] == 1)
															{
																message_checked_page();
															}
														?>
													</td>
												</tr>
											</table>
										</td>
									</tr>
									<?php
								}
							}
							echo "</table>";
						}
					}
					?>
				</td>
			</tr>
		</table>
		
		<p style="text-align: center;"><?php
			//Si l'utilisateur n'est pas connecté, on lui propose de se connecter
			if($id_session == 0 AND isset($_POST['nom']))
			{
				//Message de proposition avec redirection automatique
				echo "<a href='connecter.php?redirect=".urlencode("recherche.php?q=".$_POST['nom'])."'>Connectez-vous et acc&eacute;dez &agrave; plus de r&eacute;sultats</a>";
			}
		?></p>
		<?php if(isset($_POST['nom'])) { ?><p style="text-align:center;"><a href="http://www.bing.com/search?q=<?php echo urlencode($_POST['nom']); ?>" target="_blank">Elargir la recherche au web avec Bing</a></p><?php } ?>
		<hr />
		<?php
		//Inclusion du pied de page
		include(pagesRelativePath('common/pageBottom.php'));
		?>
	</body>
</html>
