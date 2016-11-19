<?php
	//Inclusion de la sécurité
	include('securite.php');
	
	//Init page
	include('inc/initPage.php');
	
	//On vérifie si il faut mettre toute les notification en vu
	if(isset($_GET['all_vu']))
	{
		$sql = "DELETE FROM notification WHERE ID_personne = ".$_SESSION['ID'];
		$requete = $bdd->query($sql);
	}
	
	if(isset($_GET['loading']))
	{
		//Inclusion du template
		include('inc/loading.html');
		
		//Fermeture de la page
		exit();
	}
	
	//On vérifie si l'utilisateur recherche uniquement les nouveautées
	if(isset($_GET['rapide']))
	{
		//On recherche les notifications
		$sql = "SELECT * FROM `notification` WHERE `ID_personne` = ? AND `vu` = 0 ORDER BY `ID` LIMIT 1";
		$requete = $bdd->prepare($sql);
		$requete->execute(array($_SESSION['ID']));
		
		ob_start();
		if($notification = $requete->fetch())
		{
			//Arrêt de la sécurité des erreurs!
			ob_end_clean();
			
			//On met la notification en vu
			$sql = "UPDATE notification SET vu = 1 WHERE ID = ".$notification['ID'];
			$update = $bdd->query($sql);
			
			//We search the informations about the personn
			$info = cherchenomprenom($notification['ID_createur'], $bdd);
			
			//We show the notification
			echo corrige_caracteres_speciaux($info['prenom']." ".$info['nom']." ".$notification['message'])."\n";
		}
		else
		{
			ob_end_clean();
		}
		
		//Fermeture du curseur
		$requete->closeCursor();
		
		//On quitte le script courant
		exit();
	}

	//Searching the last notifications
	$notifications = searchnotification($_SESSION['ID'], $bdd, 10, 0, '0');
			
			if(count($notifications) == 0)
			{
				echo "<div>Il n'y a rien &agrave; afficher pour le moment.</div>";
			}
			else
			{
				//We show the notifications
				foreach($notifications as $show)
				{
					//We search the informations about the personn
					$info = cherchenomprenom($show['ID_createur'], $bdd);
					
					//We show the notification
					if($show['adresse'] != "")
					{
						if(preg_match('<page:>', $show['adresse']))
						{
							$adresse = str_replace('page:', '', $show['adresse']);
							
							if(preg_match('<post:>', $show['adresse']))
							{
								$adresse = str_replace("post:", "|", $adresse);
								
								$array_adresse = explode('|', $adresse);
								
								echo "<a href='#' onClick='ajax_rapide(\"action.php?actionid=6&idnotification=".$show['ID']."\"); affiche_notifications(\"0\"); change_page_personne_with_post(".$array_adresse[0].", ".$array_adresse[1].")'>";
							}
							else
							{
								echo "<a href='#' onClick='ajax_rapide(\"action.php?actionid=6&idnotification=".$show['ID']."\"); affiche_notifications(\"0\"); change_page_personne(".$adresse.")'>";
							}
						}
						elseif(preg_match('<private_chat:>', $show['adresse']))
						{
							$adresse = str_replace('private_chat:', '', $show['adresse']);
							
							echo "<a href='#' onClick='ajax_rapide(\"action.php?actionid=6&idnotification=".$show['ID']."\"); affiche_notifications(\"0\"); affiche_chat_prive(".$adresse.")'>";
						}
						else
							echo "<a href='action.php?actionid=5&idnotification=".$show['ID']."'>";
					}
						
					echo "<div>".avatar($show['ID_createur'], "./", 32, 32);
					echo ($show['vu'] == 0 ? "<strong>" : "");
					echo corrige_caracteres_speciaux($info['prenom']." ".$info['nom']." ".$show['message']);
					echo ($show['vu'] == 0 ? "</strong>" : "")	;
					
					
					echo " <a title='Mettre la notification en vu' href='#' onClick='if(check_if_we_are_on_user_page()) { ajax_rapide(\"action.php?actionid=6&idnotification=".$show['ID']."\"); affiche_notifications(\"0\"); } else { location.href=\"action.php?actionid=5&idnotification=".$show['ID']."\" }'>vu</a></div>";
					
					if($show['adresse'] != "")
						echo "</a>";
				}
			}
