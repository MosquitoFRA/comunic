<?php
/**
 * Project's help center
 *
 * @author Pierre HUBERT
 */

//Démarrage de la session
session_start();
	
//Init page
include('inc/initPage.php');

?><!DOCTYPE html>
<html>
	<head>
		<?php include(pagesRelativePath('common/head.php')); ?>
		<title><?php echo $lang[10]; ?> - Comunic</title>
	</head>
	<body>
		<?php include(pagesRelativePath('common/pageTop.php')); ?>
		<h1 class='titre'><?php echo $lang[10]; ?></h1>
		<div style='text-align:center'><form action='<?php echo $_SERVER['PHP_SELF']; ?>' method='get'><input type='text' name='q' /><input type='submit' value="Rechercher" /></form></div>
		<?php 
			if(!isset($_GET['id']))
			{
				//On affiche l'acceuil de l'aide
				?><div class='corpspageaide'>
				<?php 
					//Uniquement si il n'y a pas de recherche
					if((!isset($_GET['q'])) AND (!isset($_GET['help_list_all_articles'])))
						include('acceuil-aide-'.$lang['nomlangue_raccourcis'].'.html');
				?>
				<p><?php echo $lang[25]; ?></p>
				<ul><?php 
					if(!isset($_GET['q']))
					{
						//On vérifie si il faut afficher tout les articles ou non
						if(isset($_GET['help_list_all_articles']))
						{
							$sql = 'SELECT titre, ID FROM aide ORDER BY titre'; //Recherche de tous les articles
							echo "<p>Voici la liste de tous les articles d'aide disponibles : <a href='aide.php'>Retour &agrave; l'acceuil</a></p>"; //Message d'information
						}
						else
							$sql = 'SELECT titre, ID FROM aide WHERE affiche_acceuil = 1 ORDER BY titre'; //Recherche des articles affichables en page d'acceuil
						
						//Exécution de la requete
						$requete = $bdd->query($sql);
					}
					else
					{
						//On effectue la recherche
						$sql = 'SELECT titre, ID FROM aide WHERE aide LIKE ? || titre LIKE ?';
						
						//Préparation de la requête
						$recherche = "%".$_GET['q']."%";
						
						//Exécution de la requete
						$requete = $bdd->prepare($sql);
						$requete->execute(array($recherche, $recherche));
						
						echo "<p>R&eacute;sultats de la recherche <a href='aide.php'>Retour &agrave; l'acceuil</a></p>";
					}
					
					//Affichage des résultats
					$count = 0; //Démarrage du compteur
					while($afficher_aide = $requete->fetch())
					{
						?><li>
							<a href='<?php echo $_SERVER['PHP_SELF']; ?>?id=<?php echo $afficher_aide['ID']; ?>' title='Cliquez ici pour ouvrir la rubrique'>
								<?php echo corrige_caracteres_speciaux($afficher_aide['titre']); ?></a>
							</li><?php
						$count++; //Augmentation du compteur
					}
					
					//Fermeture de la requete
					$requete->closeCursor();
				?></ul></div><?php
				
				//Affichage des résultats du compteur
				if($count == 0)
					echo "<p class='nb_results_help_search'>Aucun r&eacute;sultat trouv&eacute; pour cette recherche.</p>"; //Dans le cas d'aucun résultats
				elseif(isset($_GET['q']) AND $count == 1)
					echo "<p class='nb_results_help_search'>Un r&eacute;sultat trouv&eacute; pour cette recherche.</p>"; //1 résultat
				elseif(isset($_GET['q']))
					echo "<p class='nb_results_help_search'>".$count." r&eacute;sultats trouv&eacute;s pour cette recherche.</p>"; //Plus d'un résultat
			}
			else
			{
				//Message de retour à l'acceuil
				echo '<p style="text-align:center"><a href="'.$_SERVER['PHP_SELF'].'">Retour &agrave; l\'acceuil</a></p>';
				?><div class='corpspageaide'><?php
				
				//On affiche la rubrique sélectionnée
				$sql = 'SELECT * FROM aide WHERE id = ?';
				
				//Exécution de la requete
				$requete = $bdd->prepare($sql);
				$requete->execute(array($_GET['id']));
				
				if(!$afficher_aide = $requete->fetch())
				{
					//Message d'erreur
					?><table>
						<tr>
							<td>
								<?php echo code_inc_img(path_img_asset('erreur.png')); ?>
							</td>
							<td>
								<p><b>Erreur :</b> La rubrique d'aide demand&eacute;e n'a pas &eacute;t&eacute; trouv&eacute;e ou n'existe pas.</p>
								<p>Pour r&eacute;soudre le probl&egrave;me, retournez &agrave; la page pr&eacute;c&eacute;dente et actualisez-la.</p>
								<p>N'h&eacute;sitez pas &agrave; nous <a href='contact.php'>contacter</a> pour de plus amples informations.</p>
							</td>
					</table><?php
				}
				else
				{
					echo '<p><b>'.corrige_caracteres_speciaux($afficher_aide['titre']).'</b></p>';
					echo $afficher_aide['aide'];
				}
				?></div><?php
			}
			
			//Si nécessaire on affiche le bouton 'Afficher tous les articles d'aide'
			if(!isset($_GET['help_list_all_articles']))
				echo "<div class='help_list_all_articles'><a href='".$_SERVER['PHP_SELF']."?help_list_all_articles'>Lister tout les articles d'aide</a></div>";
			
		?>
		<hr />
		<?php include(pagesRelativePath('common/pageBottom.php')); ?>
	</body>
</html>