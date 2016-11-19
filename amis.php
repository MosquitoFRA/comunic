<?php
	//Init page
	include('inc/initPage.php');
	
	//On enregistre l'activité
	update_last_activity($_SESSION['ID'], $bdd);
	
	//Vérifions qu'il s'agit d'une requete ajax
	if(!isset($_GET['ajax']))
	{
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Liste d'amis</title>
		<?php include(pagesRelativePath('common/head.php')); ?>
	</head>
	<body>
		<?php include(pagesRelativePath('common/pageTop.php')); ?>
		<h1 class='titre'>Liste d'amis</h1>
		<?php
			//Vérification de l'existence d'une demande d'action
			if(isset($_GET['action']))
			{
				if(($_GET['action'] == 'supp') && (isset($_GET['id'])))
				{
					//Suppression d'un amis
					$sql1 = "DELETE FROM amis WHERE (ID_personne = ".$_GET['id'].") && (ID_amis = ".$_SESSION['ID'].") ";
					$sql2 = "DELETE FROM amis WHERE (ID_personne = ".$_SESSION['ID'].") && (ID_amis = ".$_GET['id'].") ";
					
					//Execution des suppressions
					$supp1 = $bdd->query($sql1);
					$supp2 = $bdd->query($sql2);
				}
				
				if(($_GET['action'] == 'activer') && (isset($_GET['id'])))
				{
					//On vérifie que la demande existe
					if(isset_demande_amis($_SESSION['ID'], $_GET['id'], $bdd))
					{
						//Insertion d'un amis
						$sql1 = "UPDATE amis SET actif = 1 WHERE (ID_personne = ".$_SESSION['ID'].") && (ID_amis = ".$_GET['id'].")";
						$sql2 = "INSERT INTO amis (ID_personne, ID_amis, actif) VALUES (".$_GET['id'].", ".$_SESSION['ID'].", 1) ";
						
						//Execution des suppressions
						$activ1 = $bdd->query($sql1);
						$activ2 = $bdd->query($sql2);
					}
					else
					{
						//Message d'erreur
						?><script>affiche_notification_erreur("Cette personne ne vous a pas demand&eacute;e en ami !", "Erreur", 5);</script><?php
					}
				}
			}
			
			//Requete de recherche d'amis
			$sql = "SELECT * FROM amis, utilisateurs WHERE amis.ID_amis = utilisateurs.ID AND amis.ID_personne = ".$_SESSION['ID']." ORDER BY utilisateurs.prenom";
			
			//Exécution de la requete
			$requeteamis = $bdd->query($sql);
			
			//Affichage des résultats
			echo "<table>";
			echo "<tr><td>Amis</td><td>Actif</td></tr>";
			while($afficheramis = $requeteamis->fetch())
			{
				//Si nécessaire, on ajoute ou supprime à un ami le droit de poster un texte sur ma page
				if(isset($_GET['autoriser_post_page']))
				{
					//On vérifie si il s'agit de cet ami
					if($_GET['autoriser_post_page'] == $afficheramis['ID_amis'])
					{
						//On inverse le droit
						//Détermination du droit
						$droit = ($afficheramis['autoriser_post_page'] == 1 ? 0 : 1);
						
						//Modification de la base de données
						$sql = "UPDATE amis SET autoriser_post_page = ? WHERE ID = ?";
						$requete = $bdd->prepare($sql);
						$requete->execute(array($droit, $afficheramis['ID']));
						
						//Modification de la variable
						$afficheramis['autoriser_post_page'] = $droit;
					}
				}
				
				?>
				<tr>
					<td><?php
						//Requete de recherche de l'avatar de la personne
						echo avatar($afficheramis['ID_amis']);
					 
						echo $afficheramis['prenom']." ".$afficheramis['nom'];
						
					?></td>
					<td>
						<?php
						//Affichage de amis 
						if($afficheramis['actif'] == 0)
						{
							echo "Ami non accept&eacute;. <a href='".$_SERVER['PHP_SELF']."?action=activer&id=".$afficheramis['ID_amis']."'>Accepter</a>";
						}
						else
						{
							echo "Accept&eacute;";
						}
						?>
					</td>
					<td>
						<a onClick='confirmaction("<?php echo $_SERVER['PHP_SELF']; ?>?action=supp&id=<?php echo $afficheramis['ID_amis']; ?>", "Supprimer cet ami ?");'>Supprimer cet ami</a>
					</td>
					<td>
						<a href='index.php?id=<?php echo $afficheramis['ID_amis']; ?>'>Voir sa page</a>
					</td>
					<td><a onClick="affiche_chat_prive(<?php echo $afficheramis['ID_amis']; ?>)"><div class="img-private-chat-small"></div></td>
					<td>
						<div id='abonnement_<?php echo $afficheramis['ID_amis']; ?>' class='bouton_abonnement' onClick='get_abonnement(<?php echo $afficheramis['ID_amis']; ?>, 1);'>
							<?php echo ($afficheramis['abonnement'] == 0 ? "S'abonner" : "<img src='".path_img_asset('succes.png')."' /> Abonn&eacute;"); ?>
						</div>
					</td>
					<td>
						<!-- Autoriser ou non mes amis a faire des posts sur ma page -->
						<a id='post_page_<?php echo $afficheramis['ID_amis']; ?>' 
						   class='bouton_abonnement'
						   href='<?php echo $_SERVER['PHP_SELF']; ?>?autoriser_post_page=<?php echo $afficheramis["ID_amis"]; ?>'>

							<?php if($afficheramis['autoriser_post_page'] == 0)
								echo  "Autoriser de faire des post sur ma page.";
							else {
								echo code_inc_img(path_img_asset('succes.png'), "Succ&egrave;");
								echo " Cet amis a le droit de faire des posts sur ma page.";
							} ?>
						</a>
					</td>
				</tr>
				<?php
				
				$amis = 1;
			}
			echo "</table>";
			
			//Fermeture de la requete
			$requeteamis->closeCursor();
			
			if(!isset($amis))
			{
				echo "Vous n'avez pour le moment aucun amis. <br />";
			}
		?>
		<hr>
		<?php include(pagesRelativePath('common/pageBottom.php')); ?>
	</body>
</html>
<?php
	}
	else
	{
		//Il s'agit bien d'une requete ajax
		//On vérifie qu'il existe bien un HTTP_REFERER
		if(!isset($_SERVER['HTTP_REFERER']))
			die('Invalid call !');
		
		//Requete de recherche d'amis
			$sql = "SELECT * FROM amis, utilisateurs WHERE amis.ID_amis = utilisateurs.ID AND amis.ID_personne = ".$_SESSION['ID']." ORDER BY utilisateurs.last_activity DESC";
			
			//Exécution de la requete
			$requeteamis = $bdd->query($sql);
			
			//Affichage des résultats
			$amis = 0;
			$liste_amis = array();
			while($afficheramis = $requeteamis->fetch())
			{
				?>
				<tr>
					<td><?php
						//Requete de recherche de l'avatar de la personne
						$urlavatar = avatar($afficheramis['ID_amis'], "./", 1, 1, "", true);
						echo "<img src='".$urlavatar."' style='height: ".(isset($_GET['grandavatar']) ? '32' : '16')."px; max-width: none;' />";
					
					  ?></td><td class="nomlisteamis"><?php
					  
						echo "<a ";
							//Pour le lien, on vérifie si l'appel vient de la page d'une personne
							if(preg_match('/index.php/', $_SERVER['HTTP_REFERER']) OR !preg_match('/.php/', $_SERVER['HTTP_REFERER']))
							{
								//On est bien sur la page d'une personne, le chargement peut être optimisé
								echo "href='#' onClick='change_page_personne(".$afficheramis['ID_amis'].");'";
							}
							else
							{
								//On n'est pas sur la page d'un ami, lien normal
								echo "href='index.php?id=".$afficheramis['ID_amis']."' ";
							}
						echo " class='lien'>".corrige_caracteres_speciaux($afficheramis['prenom']." ".$afficheramis['nom'])."</a>";
						
					?></td>
					<td>
						<?php
						//Demande d'amis
						if($afficheramis['actif'] == 0)
						{
							//Question
							echo "Accepter ? ";

							//Accept
							echo "<a href='".$_SERVER['PHP_SELF']."?action=activer&id=".$afficheramis['ID_amis']."'>";
								echo code_inc_img(path_img_asset('succes.png'), "Accepter l'invitation");
							echo "</a> ";
							
							//Refuse
							echo "<a href='".$_SERVER['PHP_SELF']."?action=supp&id=".$afficheramis['ID_amis']."'>";
								echo code_inc_img(path_img_asset('supp.png'), "Refuser l'invitation");
							echo "</a>";
						}
						?>
					</td>
					<td>
						<td>
							<!-- Private chat -->
							<a onClick="affiche_chat_prive(<?php echo $afficheramis['ID']; ?>); affiche_chat_prive(<?php echo $afficheramis['ID']; ?>);" >
								<img class="fast_chat_prive_img" src='<?php echo path_img_asset('prive.png'); ?>' width="16" height="16" border='0'/>
							</a>
						</td>
					</td>
					<td>
						<!-- Check availability -->
						<?php
						//On détermine si la personne est connectée
						$time = time();
						$time = $time-35;
						
						if($time > $afficheramis['last_activity'])
							//Looged in
							echo "<img src='".path_img_asset('indisponible.png')."' title=\"Derni&egrave;re connexion : ".adapte_date("plus de 35 secondes", $afficheramis['last_activity']).".\" class='indicateur_connexion' />";
						else 
							echo "<img src='".path_img_asset('disponible.png')."' title=\"Connect&eacute;\" class='indicateur_connexion' />";
						?>
					</td>
				</tr>
				<?php
				
				if(verifier_si_post_chat_prive_non_vu($_SESSION['ID'], $afficheramis['ID_amis'], $bdd))
				{
					?><tr>
						<td colspan="5" class="new_private_chat_friend_list" onClick="affiche_chat_prive(<?php echo $afficheramis['ID_amis']; ?>)">
							<span class="new_private_chat_friend_list">
								<?php echo code_inc_img(path_img_asset('email.png')); ?>
								 Message priv&eacute; non lu
							</span>
						</td>
					</tr><?php
				}				
				
				$amis++;
				$liste_amis[$afficheramis['ID_amis']] = 1;
			}
			
			if($amis == 0)
			{
				echo "<td colspan='4'>";
					echo "<p>Vos amis appara&icirc;tront ici lorsque vous en aurez. Utilisez le formulaire de recherche afin de les chercher sur le site.</p>"; 
					echo code_inc_img(path_img_asset('formulaire_recherche.png'), "Formulaire de recherche des amis");
				echo "</td>";
			}
			
			//On fait maintenant la liste des non_amis qui ont déjà utilisé le chat privé avec la personne
			//Récupération de la liste
			$listpersonn = list_all_person_private_chatted($_SESSION['ID'], $bdd);
			
			//On commence par "nettoyer la liste"
			foreach($listpersonn as $id=>$valid)
			{
				//On vérifie que c'est n'est pas un amis
				if(isset($liste_amis[$id]))
					//On supprime la personne de la liste
					unset($listpersonn[$id]);
			}
			
			//On vérifie si il reste des personnes
			if(count($listpersonn) != 0)
			{
				//Message d'information
				echo "<tr><td colspan='6' style='font-size: 70%;'>Autres personnes ayant d&eacute;j&agrave; utilis&eacute; le chat priv&eacute; avec vous :</td></tr>";
				
				foreach($listpersonn as $id=>$valid)
					{
						//We show the persons
						//We search the informations about the personn
						$infoperson = cherchenomprenom($id, $bdd);
						
						//We send to the user the informations
						echo "<tr>";
							echo "<td>";
								echo avatar($id, $urlsite, 16, 16);
							echo"</td><td colspan='2'>";
								echo "<a href='index.php?id=".$id."' class='lien'>";
									echo corrige_caracteres_speciaux($infoperson['prenom']." ".$infoperson['nom']);
								echo "</a>";
							echo "</td><td>";
								echo "<a onClick='affiche_chat_prive(".$id.");' >";
									echo code_inc_img(path_img_asset('prive.png'), "Ouvrir le chat priv&eacute;", "16", "16");
								echo "</a>";
							echo "</td>";
						echo "</tr>";
					}
			}
	}
?>