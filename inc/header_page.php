<?php
	//Vérification de sécurité
	if(!isset($header_page_ok))
		die("Invalid call !");
		
	//Vérification de l'existence de la variable d'une personne
	if(!isset($_GET['id']))
		die("Missing arguments!");
		
	//Détermination de l'ID de la personne
	$id = $_GET['id']*1;
	
	//Si c'est le fil d'actualité, on quitte immédiatement
	if($id == 0)
		goto end_of_header_page;
	
	//Récupération des informations de la personne
	$afficher = cherchenomprenom($id, $bdd);
	
	//Si la personne n'est pas connectée, on vérifie si on peut ajouter la suite de la page
	if(!isset($_SESSION['ID']))
	{
		if($afficher['pageouverte'] != 1)
			die("Le contenu ne peut &ecirc;tre charg&eacute; : La page n'est pas ouverte.");
	}
?><div class='headercontenu' style='background-image: url("<?php
		//Requete de l'existence d'une image de fond
		$id_img_fond = $id;
		//On vérifie si un complément est disponible
		echo imgfond($id_img_fond, null, null, null, null, true);
		
		?>");' <?php
			//Ajoute des entrées dans le menu contextuel si nécessaire
			if(isset($_SESSION['ID']) AND $bloque_clic_droit == "oui")
			{
				if($_SESSION['ID'] == $id)
				{
					//Ajout des entrées dans la balise div
					?> onmouseover="AddMenuContextuelTop('parametres.php?c=imgfond','Modifier l\'image de fond');" onmouseout="DelMenuContextuelTop();"<?php
				}
			}
		?>>
		<!-- Panneau d'informations -->
		<?php
			//Uniquement si l'utilisateur est connecté
			if(isset($_SESSION['ID']))
			{
				//Liste d'amis si disponible
				?><div class="infopage">
					<?php
						//Liste d'amis si disponible
						if($afficher['liste_amis_publique'] == 1)
							?><div class="element_infopage">
								<a href="listeamis.php?id=<?php echo $afficher['ID']; ?>">
									<?php echo code_inc_img(path_img_asset('users_3.png'), "Liste d'amis"); ?>
									<span class="element_infopage_long_label">Liste d'amis</span>
									<span class="element_infopage_short_label">Amis</span>
								</a>
							</div><?php
					?>
						<div class="element_infopage">
							<a href="galerieimages.php?id=<?php echo $afficher['ID']; ?>">
								<?php echo code_inc_img(path_img_asset('small/image.png'), "Galerie d'images"); ?>
								<span class="element_infopage_long_label">Galerie d'images</span>
								<span class="element_infopage_short_label">Images</span>
							</a>
						</div>
					<?php
						//Private chat. Only if the person isn't on his page
						//And  if the personn is connected.
						if(isset($_SESSION['ID']))
						{
							if($id != $_SESSION['ID'])
							{
								?><div class="element_infopage">
									<a onClick="affiche_chat_prive(<?php echo $id; ?>)" class="a">
										<?php echo code_inc_img(path_img_asset('prive.png'), "Chat priv&eacute;", "16"); ?>
										<span class="element_infopage_long_label">Ouvrir le chat priv&eacute;</span>
										<span class="element_infopage_short_label">Chat Priv&eacute;</span>
									</a>
								</div><?php
							}
						}
					?>
				</div><?php
			}
			else
			{
				//Sinon, un petit formulaire de connexion...
				?><div class='infopage'>
					<form action='connecter.php?redirect=<?php echo urlencode('index.php?id='.$id) ?>' name='connexion' method='post'>
						<table>
							<tr><td>Mail :</td><td><input type='text' name='mail' /> </td></tr>
							<tr><td>Mot de passe </td><td> <input type='password' name='motdepasse'></td></tr>
							<tr><td></td><td><input type='submit' value='Connexion' /> </td></tr>
							<tr><td>Creer un compte</td><td><a href='creercompte.php' title='Creer un nouveau compte'>Nouveau compte</a></td></tr>
						</table>
					</form>
				</div>
				<?php
			}
			?>
		<!-- Fin de: panneau d'informations-->
		<table class='header_infos_user'>
		<tr class="first_tr">
			<td	width="200">
			<?php
			  //Requete de recherche d'image
			  echo avatar($id, "./", 128, 128);
			  
			  //Uniquement si l'utilisateur est connecté
			  if(isset($_SESSION['ID']))
			  {
				//Modification de l'avatar uniquement si c'est la page de la personne
				if($_SESSION['ID'] == $id)
				{
					?><br /><font class="metro"><a class="button" href="parametres.php?c=avatar">Modifier l'avatar</a></font><?php
					
					?> <menu type="context" id="header_page">
						<menuitem label="Modifier l'image de fond" onclick="window.location='<?php echo $urlsite; ?>parametres.php?c=imgfond';"></menuitem>
					</menu><?php
				}
			  }
			  
			  ?></td><td>
			  <?php
			  //Affichage du nom de la personne
			  echo "<h2 id='nom_personne'>".corrige_caracteres_speciaux($afficher['prenom']." ".$afficher['nom'])."</h2>";
			  
			//On vérifie si la personne a un site web
			if($afficher['site_web'] != "") {
				echo "<div class='site_web'>";
					echo "<a href='".$afficher['site_web']."' title='Ouvrir le site web de la personne' target='_blank'>";
						echo code_inc_img(path_img_asset('small/world.png'));
						echo " Site web";
					echo "</a>";
				echo "</div>";
			}
			
			//On vérifie si la page est vérifiée
			if($afficher['page_verifiee'] == 1)
			{
				echo "</td><td><img style='vertical-align: middle;' src='".path_img_asset('tick.png')."' title='Page v&eacute;rifi&eacute;e' />";
			}
			  

				
				
				//On vérifie si c'est un amis uniquement si l'utilisateur est connecte
				if (isset($_SESSION['ID']))
				{
					//On voit si c'est un amis
					if($id != $_SESSION['ID'])
					{
						if(!detectesilapersonneestamie($_SESSION['ID'], $id, $bdd))
						{
							$detectesilapersonneestamie_false = true;
							
							//On détermine si la personne est autorisée ou pas à afficher la page
							if($afficher['public'] != 1)
							{
								//La personne n'est pas autorisée à voire la page
								$non_amis = true;
								
							}
						
							//On vérifie si cette personne l'a demandée en ami
							if(!isset_demande_amis($_SESSION['ID'], $id,  $bdd))
							{
								//L'autre personne ne l'a pas demandée en ami
								if(!isset_demande_amis($id, $_SESSION['ID'], $bdd) AND (!isset($_GET['demanderamis'])))
								{
									echo "<td class='invite_amis'><a href='index.php?id=".$id."&demanderamis=1'>Demander en amis</a></td>";
								}
								else
								{
									//On vérifie si il existe une demande à devenir amis
									if(isset($_GET['demanderamis']))
									{
										//Exécution de la demande
										demandeamis($_GET['id'], $_SESSION['ID'], $bdd);
									}
									
									echo "<td class='invite_amis'>";
										echo code_inc_img(path_img_asset('succes.png'), "Succ&egrave;");
										echo" Demande envoy&eacute;e (<a href='action.php?actionid=23&id=".$id."'>supprimer</a>)";
									echo "</td>"; 
								}
							}
							else
							{
								//L'autre personne l'a demandée en amie
								echo "<td class='invite_amis'>";
									echo "Invitation re&ccedil;ue: ";
									echo "<a href='amis.php?action=activer&id=".$id."'>";
										echo code_inc_img(path_img_asset('succes.png'), "Accepter l'invitation");
									echo "</a>";
										echo "<a href='amis.php?action=supp&id=".$id."'>";
										echo code_inc_img(path_img_asset('supp.png'), "REfuser l'invitation");
									echo "</a>";
								echo "</td>";
							}
						}
						else
						{
							//On affiche le bouton d'abonnement
							echo "<td><div id='abonnement_".$id."' class='bouton_abonnement' onClick='get_abonnement(".$id.", 1);'></div></td>";
						}
					}
				}
				else
				{
					if($afficher['pageouverte'] == 1)
					{
						$non_amis = true;
					}
				}
			  ?>
		  
			</tr>
			<tr>
				<td colspan="2" class="aimepage"><?php 
					//Recherche des j'aimes
					//On vérifie déja si il faut modifier un j'aime - uniquement si l'utilisateur est connecté
					if(isset($_SESSION['ID']))
					{
						if((isset($_GET['like'])) && (isset($_GET['aime'])) && (isset($_GET['typeaime_page'])))
						{
							if($_GET['like'] == $id)
							{
								aimeaimeplus($_GET['aime'], $_GET['like'], $_SESSION['ID'], $bdd, "page");
							}
						}
					}
							
					//Requete des j'aimes
					$retour = requeteaime($id, $bdd, "page");
						
					$vousaimez = $retour['vousaimez'];
					$personnesaiment = $retour['personnesaiment'];
					
					//Ouverture du contenur des j'aimes
					echo "<span class='aime_contener' id='aime_page'>";
					
						if(isset($_SESSION['ID']))
						{
							if($vousaimez == 0)
							{			
								echo "<span class='aime' ><a onClick='like_text_page(".$id.", \"page\", 0)'>";
									echo code_inc_img(path_img_asset('aime.png'));
								echo " ".$lang[33]."</a> </span>";
							}
							else
							{
								echo "<span class='aime' ><a onClick='like_text_page(".$id.", \"page\", 1)'>";
									echo code_inc_img(path_img_asset('aimeplus.png'));
								echo " ".$lang[34]."</a> </span> ";
							}
						}
									
						if ($personnesaiment == 1)
						{
							echo "<font class='aimepage_texte'>Une personne aime.</font>";
						}
						elseif ($personnesaiment != 0)
						{
							echo "<font class='aimepage_texte'>".$personnesaiment." personnes aiment.</font>";
						}
						else
						{
							echo "<font class='aimepage_texte'>Soyez le premier &agrave; aimer</font>";
						}
					
					//Fermeture du conteneur des j'aime
					echo "</span>";
				?>
			  </td>
		  </tr>
		</table>
</div><?php
	
	//Fin du fichier
	end_of_header_page:
	//EOF