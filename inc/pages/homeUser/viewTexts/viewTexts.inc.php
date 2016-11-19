<?php
isset($_SESSION) OR exit("Invalid call - viewTexts.inc.php");

//Vérification de la présence des variables requises
if(!isset($_GET['id']))
	die("Missing arguments !");
	
// Si c'est un appel AJAX pour le fil d'actualité
if($_GET['id'] == "0")
{
	$_GET['id'] = "fil";
	$id = 0;
}
else
	//Getting personn's ID
	$id = $_GET['id']*1;

//Définition du numéro de page
$numero_page = (isset($_GET['page']) ? $_GET['page']*1 : 0);

//On vérifie si la personne est autorisée à visualiser la page
$niveau_visibilite_autorise = is_allowed_to_view_page($id, $bdd);

//On vérifie si la liste des smiley a bien été chargée
if(!isset($liste_smiley))
{
	//Inclusion de la liste des smiley
	include('liste_smile.php');
}

//On vérifie si la personne est autorisée à visualiser la page
if(!$niveau_visibilite_autorise)
{
	//On vérifie si l'utilisateur est connecté
	if(isset($_SESSION['ID']))
	{
		die('<p>'.corrige_caracteres_speciaux(return_nom_prenom_user($id, $bdd))." refuse que vous voyez sa page sans &ecirc;tre son amis.</p> <p>Vous pouvez lui envoyer une demande pour devenir son ami ci-dessus.</p>");
	}
	else
		die("Vous n'&ecirc;tes pas autoris&eacute;s &agrave; voir le contenu de cette page.");
}

//Si il s'agit du fil, on restreint au niveau des amis
if($_GET['id'] == "fil")
{
	$niveau_visibilite_autorise = 2; //Niveau arbitraire
}

//On vérifie si l'utilisateur appartient à un des groupes du propriétaire de la page
$liste_groupes = (isset($_SESSION['ID']) ? search_appartenance_groupes($id, $_SESSION['ID'], $bdd) : array());

//Détermination des informations de la personne
$afficher = cherchenomprenom($id, $bdd);

//Définition de la variable de stockage des informations sur les utilisateurs
$info_users = array();
$info_users[$id]['table_utilisateurs'] = $afficher;
$info_users[$id]['avatar_32_32'] = avatar($id, "./", 32, 32);

//Définition de l'id de la personne de recherche
$id_personne_recherche = $id;

//On vérifie si il s'agit du fil d'actualité
if($_GET['id'] == "fil")
{
	//On vérifie si l'utilisateur est connecté
	if(!isset($_SESSION['ID']))
		die("Le fil d'actualit&eacute; n'est pas encore accessible aux personnes non connect&eacute;es.");
		
	//Récupération de la liste des amis
	$liste_amis = liste_amis($_SESSION['ID'], $bdd);
	
	$complement_personnes = " OR ID_Personne = ".implode($liste_amis, " OR ID_Personne = ");
	
	$id_personne_recherche = $_SESSION['ID']." ".$complement_personnes;
}

//On vérifie si il faut recharger tous les textes ultérieurs ou non ainsi que si il faut chercher un post précis
if(isset($_GET['post']))
{
	//Contrôle de sécurité
	$num_post = $_GET['post']*1;
	if($num_post <= 0)
	{
		die('valeur incorrecte');
	}
	
	//Recherche du texte précis
	$textes = affichertextes($id_personne_recherche, $bdd, false, false, $niveau_visibilite_autorise, false, $num_post, $liste_groupes);
	
	//On compte le nombre de textes dont dispose l'utilisateur
	$sql = "SELECT COUNT(*) AS nb_textes FROM texte WHERE ID_personne = ? AND niveau_visibilite <= ? AND ID >= ?";
	
	//Exécution de la requête
	$requete = $bdd->prepare($sql);
	$requete->execute(array($id_personne_recherche, $niveau_visibilite_autorise, $num_post));
	
	//On enregistre le résultat
	if(!$enregistrer = $requete->fetch())
		$fatal_error = true;
	
	//Fermeture de la requête
	$requete->closeCursor();
	
	//On vérifie si il y a une erreur
	if(isset($fatal_error))
		die("Une erreur a survenue, merci de r&eacute;essayer ult&eacute;rieurement.");
		
	//On divise la valeur par 10
	$nb_pages = floor($enregistrer['nb_textes']/10);
	
	if($enregistrer['nb_textes'] == $nb_pages*10)
		$nb_pages--;
	
	$numero_page = $nb_pages;
	$_GET['page'] = $nb_pages;
	
	echo "<tr><td><a class='a' onClick='open_page_ameliore(".$id.");'>Retour &agrave la page principale</a></td></tr>";
}
elseif(!isset($_GET['reload']))
{
	//Recherche des textes demandés
	$textes = affichertextes($id_personne_recherche, $bdd, $numero_page*10, 10, $niveau_visibilite_autorise, false, false, $liste_groupes);
}
else
{
	//Recherche de tous les textes de la personne (jusqu'à la page actuelle)
	$textes = affichertextes($id_personne_recherche, $bdd, 0, $numero_page*10 + 10, $niveau_visibilite_autorise, false, false, $liste_groupes);
}

if(count($textes) == 0)
{
	if($numero_page != 0)
	{
		echo "<p>Il n'y a plus de texte &agrave; afficher.</p>";
	}
	elseif(isset($_SESSION['ID']))
	{
		if($id != $_SESSION['ID'] OR isset($_GET['reload']) OR isset($_GET['post']))
		{
			echo "<tr><td><img src='img/erreur.png' title='Avertissement' /></td><td><p>".$lang[51]."</p></td></tr></table>";
			echo "</div>";
			
			if($ok_textes_check != "source:index.php")
				die();
		}
		else
		{
			?><tr><td><div class="no_texte">
				<?php echo code_inc_img(path_img_asset('add_text.gif')); ?>
				<span class="titre_no_texte">Bienvenue dans Comunic !</span><br />
				<p>Vous n'avez pas encore ajout&eacute; de texte sur votre page. Vous pouvez, si vous souhaitez vous manifester d&egrave;s maintenant, en ajouter depuis le formulaire ci-dessus. Par quoi allez-vous commencer ? Un texte simple, une image, une vid&eacute;o de votre cru ou de Youtube ou encore un compteur &agrave; rebours ? Vous avez le choix ! N'oubliez pas, apr&egrave;s cela, de rechercher vos amis qui se sont d&eacute;j&agrave; inscrit sur le site ! Bonne route dans Comunic, le nouveau moyen de communication !</p> 
			</div></td></tr><?php
			
			if($ok_textes_check != "source:index.php")
				die();
		}
	}
	else
	{
		if($ok_textes_check != "source:index.php")
			die();
	}
}
			
//On prépare l'affichage des résultats
$afficherfond = 1;

//Affichage des résultats
for($i = 0; isset($textes[$i]); $i++)
{
	$afficherresultats = $textes[$i];
	
	if($afficherresultats['ID_amis'] != 0)
	{
		//C'est l'ami qui a mis le post
		$id_posteur = $afficherresultats['ID_amis'];
		
		//On vérifie si on connait les informations sur cette personne
		if(!isset($info_users[$id_posteur]['table_utilisateurs']))
		{
			$info_users[$id_posteur]['table_utilisateurs'] = cherchenomprenom($afficherresultats['ID_amis'], $bdd);
			$info_users[$id_posteur]['avatar_32_32'] = avatar($id_posteur, "./", 32, 32);
		}
		
		//C'est un amis qui a posté le texte
		$infoami = $info_users[$afficherresultats['ID_amis']]['table_utilisateurs'];
	}
	else
	{
		$id_posteur = $afficherresultats['ID_personne']; //C'est le possesseur de la page qui a posté le texte
	
		//On vérifie si on connait les informations sur cette personne
		if(!isset($info_users[$id_posteur]['table_utilisateurs']))
		{
			$info_users[$id_posteur]['table_utilisateurs'] = cherchenomprenom($id_posteur, $bdd);
			$info_users[$id_posteur]['avatar_32_32'] = avatar($id_posteur, "./", 32, 32);
		}
	}
	
	if($_GET['id'] == "fil")
	{
		//C'est un amis qui a posté le texte
		$infoami = $info_users[$id_posteur]['table_utilisateurs'];
	}
	
	?>
	<tr class="separateur_texte"><td>&nbsp;</td></tr>
	<tr class='post <?php if($afficherfond == 1) {echo "texte_bg_grey"; }  ?>'>
		<td class="td_left">
		<?php
			//Recherche de l'avatar de la personne
			echo $info_users[$id_posteur]['avatar_32_32'];
			  
			  echo corrige_caracteres_speciaux((isset($infoami) ? "<span onClick='change_page_personne(".$infoami['ID'].");'>".$infoami['prenom']." ".$infoami['nom']."</span>" : $afficher['prenom']." ".$afficher['nom']));
			  
			  //Uniquement si l'utilisateur est connecté
			  if (isset($_SESSION['ID']))
			  {
				  //On vérifie si l'utilisateur est autorisé à supprimer le texte
				  if($id == $_SESSION['ID'] || (isset($infoami['ID']) ? ($infoami['ID'] == $_SESSION['ID'] ? true : false) : false) )
				  {
						//On vérifie d'abord l'existence d'une demande de suppression de texte
						if(isset($_GET['suppidtxt']))
						{
							if($afficherresultats['ID'] == $_GET['suppidtxt'])
							{
								
								//On supprime le texte
								deletetexte($afficherresultats['ID'], $afficherresultats['texte'], $bdd, $afficherresultats);
								
								//On redirige vers la meme page pour éviter les collisions
								echo "Suppression termin&eacutee, redirection en cours....<br />";
								echo '<meta http-equiv="refresh" content="0;URL=index.php?id='.$id.'">';
								die();
							}
						}
										
						//La personne est autorisée à supprimer ou modifier le texte (la modification du texte doit être réservée uniquement à celui qui a posté le texte)
						echo " <a onClick='confirmaction(\"action.php?actionid=29&id=".$id;
						echo "&page=".$numero_page;
						echo "&suppidtxt=".$afficherresultats['ID']."\", \"".$lang[49]."\");'>";
							echo code_inc_img(path_img_asset('supp.png'), "Supprimer le texte", "16", "16");
						echo "</a>";
						
						//On affiche le lien d'édition de post uniquement si l'utilisateur est le posteur du texte
						if($afficherresultats['ID_amis'] != 0 ? ($_SESSION['ID'] == $afficherresultats['ID_amis'] ? true : false) : true)
							echo "<a href='editpost.php?id=".$afficherresultats['ID']."&iduser=".$id."&page=".$numero_page."'>";
								echo code_inc_img(path_img_asset('edit.png'), "Editer le texte", "16", "16");
							echo "</a>";
				  }
			  }
		?>
		</td>
		<td>
			<?php echo adapte_date($afficherresultats['date_envoi']); ?>
			<?php 
				$droit_modification_niveau_visibilite = false;
				if(isset($_SESSION['ID'])) //L'utilisateur doit être connecté
				{
					if($id == $_SESSION['ID'] || (isset($infoami['ID']) ? ($infoami['ID'] == $_SESSION['ID'] ? true : false) : false)) //Réservé au propriétaire du post.
						$droit_modification_niveau_visibilite = true;
				}
				
				affiche_menu_change_niveau_visibilite($afficherresultats['niveau_visibilite'], $afficherresultats['ID'], $droit_modification_niveau_visibilite);
			?>
		</td>
	</tr>
	<tr class='post <?php if($afficherfond == 1) {echo "texte_bg_grey"; }  ?>'>
		<td colspan="2"><?php
			
			//On vérifie si il y a une vidéo intégrée
			if($afficherresultats['type'] == "video")
			{
				//Récupération des informations de la vidéo
				$info_video = get_info_video($afficherresultats['idvideo'], $bdd);
				
				//Préparation de l'affichage des vidéos
				echo "<div class='video_contener'>";
				
				//Affichage de la vidéo
				affiche_video(array(array($info_video['URL'], $info_video['file_type'])), "metadata", "640", "264", "none", "controls", true, sha1($info_video['URL'].$info_video['file_type'].time().$_SERVER['REQUEST_URI']));
				
				//Fermeture de l'affichage de la vidéo
				echo "</div>";
			}
			
			//On adapte le texte au cas où il y aurait une image intégrée
			if($afficherresultats['type'] == "image" || $afficherresultats['type'] == "web_image")
			{
				//Affichage de l'image
				echo '<a class="fancybox" rel="group" href="';
				echo webUserDataFolder($afficherresultats['path']);
				echo '" title="'.str_replace('"', "'", affiche_smile(corrige_caracteres_speciaux(bloque_javascript_css(afficher_lien($afficherresultats['texte']))), $urlsite, $liste_smiley));
				echo ' <a href=\'index.php?id='.$id.'&post='.$afficherresultats['ID'].'\'> Voir plus </a>"><img class="img_from_upload" alt="Image envoy&eacute;e par l\'utilisateur" src="';
				echo webUserDataFolder($afficherresultats['path']);
				echo '" alt="" /></a> <br />';
			}
			
			//On adapte l'affichage au cas où il y aurait un compte à rebours
			if($afficherresultats['type'] == "count_down")
			{
				//Affichage du compte à rebours
				affiche_compte_rebours($afficherresultats);
			}
			
			//On adapte l'affichage au cas où il y aurait un lien vers une page web
			if($afficherresultats['type'] == "webpage_link")
			{
				//Affichage du compte à rebours
				affiche_lien_webpage($afficherresultats);
			}
			
			//On adapte l'affichage au cas où il y aurait un pdf
			if($afficherresultats['type'] == "pdf")
			{
				//Affichage du compte à rebours
				affiche_lien_pdf($afficherresultats);
			}
			
			//On adapte le texte au cas où il y aurait une vidéos YouTube
			if($afficherresultats['type'] == "youtube")
			{
				//Affichage de l'image
				echo code_video_youtube($afficherresultats['path']);
			}
			
			//On adapte le texte si un sondage est présent dans le post
			if($afficherresultats['type'] == "sondage")
			{
				//Affichage du sondage
				//Récupération des informations sur le sondage
				$infos_sondage = get_sondage_by_text_id($afficherresultats['ID'], $bdd);
				
				if(!$infos_sondage)
					continue;
				
				//Ouverture du sondage
				?><div class="metro sondage"><?php
				
				//On simplifie la variable $infos_sondage pour la suite du script
				$infos_sondage = $infos_sondage[0];
				
				//Affichage de la question du sondage
				echo "<div class='question_sondage'>".corrige_caracteres_speciaux($infos_sondage['question'])."</div>";
				
				//Récupération des choix du sondage
				$choix_sondage = select_sql("sondage_choix", "ID_sondage = ?", $bdd, array($infos_sondage['ID']));
				
				//On vérifie si la personne est autorisée à prendre part au sondage
				if(isset($_SESSION['ID']))
					$allow_participation = 1;
				else
					$allow_participation = 0;
				
				//On propose le vote si celui-ci est possible
				if($allow_participation == 1)
				{
					$reponse_user_sondage = vote_personne_sondage($_SESSION['ID'], $infos_sondage['ID'], $bdd);
					
					if(!$reponse_user_sondage)
					{
						//Affichage du formulaire de vote
						?><div class="reponse_sondage" id="reponse_sondage_<?php echo $infos_sondage['ID']; ?>_contener">
							<?php
								//Parcours des options de vote
								echo '<div class="input-control select">';
									echo "<select id='reponse_sondage_".$infos_sondage['ID']."' >";
										foreach($choix_sondage as $afficher_choix)
										{
											?><option value="<?php echo $afficher_choix['ID']; ?>"><?php echo $afficher_choix['Choix'];
										}
									echo "</select>";
								echo "</div>";
							?>
							
							<!-- Bouton de vote -->
							<input type="button" class="primary" value="Voter" onClick="voteSondage(<?php echo $infos_sondage['ID']; ?>, <?php echo $infos_sondage['ID_utilisateurs']; ?>);" />
							<!-- Fin de: Bouton de vote -->
						</div><?php
					}
					else
					{
						?>Votre r&eacute;ponse au sondage : <?php
						
						foreach($choix_sondage as $afficher_choix)
						{
							if($afficher_choix['ID'] == $reponse_user_sondage)
							{
								echo $afficher_choix['Choix'];
							}
						}
						
						?> <span class="a" onClick="if(confirm('Voulez-vous annuler votre vote ?')) { ajax_rapide('<?php echo $urlsite; ?>action.php?actionid=37&type=cancel_vote&id_choix=<?php echo $reponse_user_sondage; ?>&id_sondage=<?php echo $infos_sondage['ID']; ?>'); this.innerHTML = 'Annul&eacute;'; this.style.color='black'; }">Annuler</span><?php
					}
				}
				
				//On affiche les réponses au sondage
				$reponse_sondages = select_sql("sondage_reponse", "ID_sondage = ?", $bdd, array($infos_sondage['ID']));
				
				//On vérifie si il y a des réponses au sonde
				if(count($reponse_sondages) == 0)
				{
					echo "<p>Personne n'a r&eacute;pondu pour le moment au sondage.</p>";
				}
				else
				{
					//Retour à la ligne
					echo "<br />";
					
					//Préparation à la comptabilité
					$liste_reponses = array();
					
					//Traitement des choix proposés
					foreach($choix_sondage as $traiter_choix)
					{
						$liste_reponses[$traiter_choix['ID']] = array('hit' => 0, 'nom' => $traiter_choix['Choix']);
					}
					
					//Traitement des réponses
					foreach($reponse_sondages as $traiter_reponse)
					{
						//On vérifie si la réponse proposée existe
						if(isset($liste_reponses[$traiter_reponse['ID_sondage_choix']]))
							//On incrémente le nombre de réponses
							$liste_reponses[$traiter_reponse['ID_sondage_choix']]['hit']++;
					}
					
					//Définition des ID d'affichage
					$id_tableau_valeurs = "sondage_tableau_resultat_".$infos_sondage['ID']."_".time();
					$id_canvas_camembert = "sondage_tableau_resultat_".$infos_sondage['ID']."_".time()."_canvas";
					
					echo "<div class='main_resultat_sondage_contener'>"; // class="grid" enlevé
						//echo "<div class='row'>";
							//echo "<div class='span2'>";
								//Tableau de valeurs
								echo "<table id='".$id_tableau_valeurs."'>";
								echo "<tr><th>Choix</th><th>Total</th></tr>";
									//Traitement des résultat
									foreach($liste_reponses as $afficher_reponse)
									{
										echo "<tr><td>".$afficher_reponse['nom']."</td><td>".$afficher_reponse['hit']."</td></tr>";
									}
								echo "</table>";
							//echo "</div><div class='span4'>";
								//Affichage du tableau des résultats
								//Canvas de destination du camembert
								echo '<canvas id="'.$id_canvas_camembert.'" width="300" height="300"></canvas>';
							//echo "</div>";
						//echo "</div>";
					echo "</div>";
					
					
					//On indique qu'il faut afficher le sondage au chargement de la page
					echo "<sondage_result>".$id_tableau_valeurs."|".$id_canvas_camembert."</sondage_result>";
					echo "<script>draw_camembert('".$id_tableau_valeurs."', '".$id_canvas_camembert."');</script>";
				}
				
				?></div><?php
			}
			


		//Showing text
		$texte = $afficherresultats['texte'];
		echo affiche_smile(corrige_caracteres_speciaux(bloque_javascript_css(afficher_lien($texte))), $urlsite, $liste_smiley)."<!-- '\" -->"; 
	

		//Recherche des j'aimes
		//On vérifie déja si il faut modifier un j'aime - uniquement si l'utilisateur est connecté
		if(isset($_SESSION['ID']))
		{
			if((isset($_GET['like'])) && (isset($_GET['aime'])) && (isset($_GET['typeaime_commentaire'])))
			{
				if($_GET['like'] == $afficherresultats['ID'])
				{
					aimeaimeplus($_GET['aime'], $_GET['like'], $_SESSION['ID'], $bdd);
				}
			}
		}
		
			//Ouverture du conteneur des j'aime
			echo "<span class='aime_contener' id='aime_texte_".$afficherresultats['ID']."'>";
			
			//Requete des j'aimes
			$retour = requeteaime($afficherresultats['ID'], $bdd);
			
			$vousaimez = $retour['vousaimez'];
			$personnesaiment = $retour['personnesaiment'];
			
			if(isset($_SESSION['ID']))
			{
				if($vousaimez == 0)
				{			
					echo "<span class='aime' ><a onClick='like_text_page(".$afficherresultats['ID'].", \"texte\", 0);' >";
						echo code_inc_img(path_img_asset('aime.png'));
					echo " ".$lang[33]."</a> </span> ";
				}
				else
				{
					echo "<span class='aime' ><a onClick='like_text_page(".$afficherresultats['ID'].", \"texte\", 1);' >";
						echo code_inc_img(path_img_asset('aimeplus.png'));
					echo " ".$lang[34]."</a> </span> ";
				}
			}
			
			if ($personnesaiment == 1)
			{
				echo " Une personne aime &ccedil;a.";
			}
			elseif ($personnesaiment != 0)
			{
				echo " ".$personnesaiment." ".$lang[61];
			}
			
			//Fermeture du conteneur des j'aime
			echo "</span>";
		?></td>
	</tr>
	<?php
	$ok_commentaire = true;
	if(isset($infoami)) { if($infoami['bloquecommentaire'] == 1) $ok_commentaire = false; }
	if($afficher['bloquecommentaire'] == 0 AND $ok_commentaire == true)
	{
		?>
			<tr class='commentaires <?php if($afficherfond == 1) {echo "texte_bg_grey"; $afficherfond = 1;} else $afficherfond=1; ?>'>
				<!--<td class="td_left"><?php echo $lang[35]; ?></td>-->
				<td colspan="2">
					<table id="tablecommentaire<?php echo $afficherresultats['ID']; ?>" class="tablecommentaire">
							<?php
								//Vérification de l'existence du post d'un commentaire
								if ((isset($_POST['commentaire'])) && (isset($_POST['idtexte'])))
								{
									if($_POST['idtexte'] == $afficherresultats['ID'])
									{
										//On vérifie si le commentaire n'est pas vide
										if($_POST['commentaire'] != "" || isset($_FILES['image']))
										{
											//Insertion du commentaire
											ajoutcommentaire($_SESSION['ID'], $afficherresultats['ID'], corrige_accent_javascript($_POST['commentaire']), $bdd);
											
											//Texte de la notification
											if($afficher['ID'] == $_SESSION['ID'])
												$texte_notification = "a ajout&eacute; un commentaire sur sa page.";
											else
												$texte_notification = "a ajout&eacute; un commentaire sur la page de ".$afficher['prenom']." ".$afficher['nom'];
											
											//And the notification
											if(!visibilite_privee($afficherresultats["niveau_visibilite"]))
											{
												sendnotification($_SESSION['ID'], $texte_notification, $bdd, "page:".$afficher['ID']."post:".$afficherresultats['ID'], $afficher['ID'], "commentaire");
											}
											else
											{
												//Envoi de la notification aux personnes autorisées uniquemement
												sendnotification($_SESSION['ID'], $texte_notification, $bdd, "page:".$afficher['ID']."post:".$afficherresultats['ID'], $afficher['ID'], "commentaire", list_personnes_groupes($afficherresultats["niveau_visibilite"], $bdd));
											}
										}
										else
										{
											//On affiche un message d'erreur
											?><script type='text/javascript'>alert("L'ajout de commentaires vides est interdit!");</script><?php
										}
										
										//On ne quitte pas le script
										echo("<!-- Add comment -->");
									}
								}
								
								//Affichage des commentaires
								$commentaires = affichecommentaire($afficherresultats['ID'], $bdd);
								
								//Affichage des commentaires
								foreach($commentaires as $affichercommentaires)
								{
									//Affichage du commentaire
									?>
									<tr>
										<td class="commentaire"><?php
											//Recherche de l'avatar et du nom de la personne (si nécessaire
											$id_posteur_commentaire = $affichercommentaires['ID_personne'];
											if(!isset($info_users[$id_posteur_commentaire]['table_utilisateurs']))
											{
												$info_users[$id_posteur_commentaire]['table_utilisateurs'] = cherchenomprenom($id_posteur_commentaire, $bdd);
												$info_users[$id_posteur_commentaire]['avatar_32_32'] = avatar($id_posteur_commentaire, "./", 32, 32);
											}
											
											echo $info_users[$id_posteur_commentaire]['avatar_32_32'];
											$infopersonne = $info_users[$id_posteur_commentaire]['table_utilisateurs'];
											echo corrige_caracteres_speciaux($infopersonne['prenom']." ".$infopersonne['nom']);
											?>
										
										<?php
										//Affichage de l'image du commentaire(si il y en a une)
										if($affichercommentaires['image_commentaire'] != "")
										{
											// Ancienne version
											// echo "<img src='data:image/png;base64,".$affichercommentaires['image_commentaire']."' />";
											
											//Adresse de l'image
											$adresse_image = $urlsite."imgcommentaire.php/".base64_encode($affichercommentaires['ID'])."/".sha1($affichercommentaires['commentaire']);
											
											echo "<a style='cursor: pointer' onClick='$.fancybox.open({href :\"".$adresse_image."\", type : \"image\"});'><img src='".$adresse_image."' height='100' /></a> ";
										}
										
										//Affichage du commentaire
										if($ok_textes_check != "source:index.php")
											echo afficher_lien(corrige_caracteres_speciaux(decorrige_accent_javascript(affiche_smile(bloquebalise($affichercommentaires['commentaire'], "commentaire"), $urlsite, $liste_smiley))));
										else
											echo afficher_lien(corrige_caracteres_speciaux(corrige_accent_javascript(affiche_smile(bloquebalise($affichercommentaires['commentaire'], "commentaire"), $urlsite, $liste_smiley))));
										
										//Bouton du j'aime pour commentaire
										bouton_aime_commentaire($affichercommentaires['ID'], $afficherresultats['ID'], $bdd);
										
										echo "<span>";
										//Uniquement si l'utilisateur est connecté
										if(isset($_SESSION['ID']))
										{
											//Bouton de suppression de commentaire, si c'est autorisé...
											if(($_SESSION['ID'] == $id) || ($affichercommentaires['ID_personne'] == $_SESSION['ID']))
											{
												//On vérifie d'abord l'existence d'une demande de suppression de commentaire
												if(isset($_GET['suppidcom']))
												{
													if($affichercommentaires['ID'] == $_GET['suppidcom'])
													{
														//On supprime le commentaire
														echo suppcom($affichercommentaires['ID'], $bdd);
														die();
													}
												}
												
												//La personne est autorisée à supprimer le commentaire
												echo "<a onClick='delete_comment(\"".$affichercommentaires['ID']."\", \"".$afficherresultats['ID']."\", \"".$id."\", \"".(isset($_GET['page']) ? $_GET['page'] : 0)."\")";
												echo "'>";
												echo code_inc_img(path_img_asset('supp.png'), "Supprimer le commentaire");
												echo "</a>";
												
												//On vérifie si la personne est bien celle qui est l'auteur du commentaire pour pouvoir le modifier
												if($_SESSION['ID'] == $affichercommentaires['ID_personne'])
												{
													echo "<a onClick='editcommentaire(\"".$affichercommentaires['ID']."\", \"".$afficherresultats['ID']."\", \"".$id."\", \"".(isset($_GET['page']) ? $_GET['page'] : 0)."\");'>";
														echo code_inc_img(path_img_asset('edit.png'), "Modifier le commentaire", "16", "16");
													echo "</a>";
												}
											}
										}
										?>
										<?php echo adapte_date($affichercommentaires['date_envoi']); ?></span>
										</td>
									</tr>
									<?php
								}
								
								//Formulaire d'envoi de nouveau commentaire - uniquement si l'utilisateur est connecté
								if(isset($_SESSION['ID']))
								{
									//On vérifie que l'avatar de l'utilisateur connecté est disponible
									if(!isset($info_users[$_SESSION['ID']]['avatar_32_32']) OR !isset($info_users[$_SESSION['ID']]['table_utilisateurs']))
									{
										$info_users[$_SESSION['ID']]['avatar_32_32'] = avatar($_SESSION['ID'], "./", 32, 32);
										$info_users[$_SESSION['ID']]['table_utilisateurs'] = cherchenomprenom($_SESSION['ID'], $bdd);
									}
								
								/**
								 * Add a comment
								 */
								?>
								<tr class="add_comment">
									<td colspan="2">
										<form id="addcommentaire_<?php echo $afficherresultats['ID']; ?>" enctype="multipart/form-data" method="post">
											<!-- Avatar -->
											<?php echo $info_users[$_SESSION['ID']]['avatar_32_32']; ?>

											<!-- Text comment -->
											<input type='text' placeholder="Ajout d'un commentaire..." name='commentaire' id="addcommentaire<?php echo $afficherresultats['ID']; ?>" /><?php echo source_ajout_smiley($afficherresultats['ID']); ?>

											<!-- ID of text -->
											<input type='hidden' name='idtexte' value='<?php echo $afficherresultats['ID']; ?>' />
											
											<!-- Optionnal: image -->
											<label class="input_commentaire_image">
												<?php echo code_inc_img(path_img_asset('small/image.png'), "Optionnel: ajout d'une image"); ?>
												<input type="file" id="image_<?php echo $afficherresultats['ID']; ?>" name="image" />
											</label>
											
											<!-- Submit comment -->
											<input onClick="submitCommentaire('action.php?actionid=29&id=<?php echo $id; if(isset($_GET['page'])) echo "&page=".$_GET['page']; ?>', 'addcommentaire<?php echo $afficherresultats['ID']; ?>', 'tablecommentaire<?php echo $afficherresultats['ID']; ?>', <?php echo $afficherresultats['ID']; ?>, <?php if(isset($_GET['page'])) echo $_GET['page']; else echo 0; ?>, <?php echo $id; ?> );" type='button' value='<?php echo $lang[32]; ?>' />
										</form>
									</td>
								</tr>
								<?php
								}
							?>
					</table>
				</td>
			</tr>
		<?php
		}
	
	//On supprime l'ID du posteur pour le prochain affichage
	unset($id_posteur);
	
	//On supprime l'ID de l'ami si il existe pour le prochain post
	if(isset($infoami))
		unset($infoami);
}
				
//Création de l'ID du bouton "Afficher plus"
//$id_bouton_afficher_plus = "view_more_".time();
if(!isset($no_view_more))
{
	?><tr id="view_more"><?php echo $id; ?>|<?php echo $numero_page+1; ?><tr><?php
}

//Fin de fichier 
//die();