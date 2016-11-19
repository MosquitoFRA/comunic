<?php
/**
 * Add post main view
 *
 * @author Pierre HUBERT
 */

isset($_SESSION) OR exit('Invalid call - '.$_SERVER['PHP_SELF']);

?><font class="metro">
	<div class="tab-control" data-role="tab-control" id="tabs_form_add">
		<ul class="tabs" onmouseover="$('#tabs_form_add').tabcontrol();">
			<li class="active"><a href="#_page_1">Texte / <i class="icon-pictures"></i></a></li>
			<li><a href="#_youtube_add"><img src="<?php echo path_img_asset('logo-youtube.png'); ?>" height="16px"></a></li>
			<li id='tab_add_a_movie'><a href="#_movie_add"><i class="icon-film"></i></a></li>
			<li><a href="#_webpage_add"><i class="icon-link"></i></a></li>
			<li id="tab_add_a_pdf"><a href="#_post_with_pdf_add"><i class="icon-file-pdf"></i></a></li>
			<li><a href="#_count_down_add" onClick='$("#datepicker").datepicker();'><i class="icon-alarm-clock"></i></a></li>
			<li><a href="#_sondage_add" ><i class="icon-pie"></i></a></li>
		</ul>
		 
		<div class="frames">
			<div class="frame" id="_page_1">
				<div class="ajout_post">
					<div id="button_form_ajout_simple">
						<button class="command-button info" onClick="document.getElementById('button_form_ajout_simple').style.display = 'none'; document.getElementById('form_ajout_simple').style.display = 'block';">
							<i class="icon-plus on-left"></i>
							Ajouter un post
							<small>Cliquez ici afin d'ajouter un post</small>
						</button>
					</div>
					<div id="form_ajout_simple" style="display: none; height: 316px;">
						<!-- Ajout simple -->
						<form action='<?php echo $_SERVER['PHP_SELF']; ?>?id=<?php echo $idPersonn; ?>' method='post' enctype='multipart/form-data'>
							<p>Image (optionelle) : <input type='file' class='filenouvimage' name='image' /></p>
						
							<textarea name='texte' id='ajoutsimple'></textarea><br />
							
							<!-- Niveau de visibilité -->
							<?php choisir_niveau_visibilite(); ?>
							<!-- Fin de: Niveau de visibilité -->
							
							<!-- Boutons de validation -->
							<input type='submit' value='<?php echo $lang[32]; ?>' />
							<!-- Fin de: Boutons de validation -->
							
							<!-- TinyMCE -->
							<?php echo code_inc_js(path_js_asset('ajoutsimple.js')); ?>
							<!-- /TinyMCE -->
						</form>
						<!-- Fin de: ajout simple -->
					</div>
				</div>
			</div>
			<div class="frame" id="_youtube_add">
				Ajout d'une vid&eacute;o Youtube
				<p>Pour ajouter une vid&eacute;o YouTube sur votre page, veuillez copier son adresse web puis la coller dans le champs ci-dessous.</p>
				<form action="<?php echo $_SERVER['PHP_SELF']; ?>?id=<?php echo $idPersonn; ?>" method="post">
					<input type="text" name="youtube" />
					<p>Vous pouvez &eacute;galement ajouter un petit commentaire pour vos amis....</p>
					<input type="text" name="commentyoutube" />
					<p>Une fois que vous &ecirc;tes pr&ecirc;t, cliquez sur Ajouter.</p>
					
					<!-- Niveau de visibilité -->
					<?php choisir_niveau_visibilite(); ?>
					<!-- Fin de: Niveau de visibilité -->
					
					<!-- Bouton de validation -->
					<p><input type="submit" value="Ajouter" /></p>
					<!-- Fin de : Bouton de validation -->
				</form>
			</div>
			<div class="frame" id="_movie_add">
				<form action='<?php echo $_SERVER['PHP_SELF']; ?>?id=<?php echo $idPersonn; ?>' method='post' name="Envoi d'une video">
					Envoi d'une vid&eacute;o personnelle
					<div style="text-align: center;" class="metro"><input type="button" value="Envoi d'une vid&eacute;o" onclick="ouvre_fenetre_upload_video();" /></div>
					<?php
						//Listing de toute les vidéos disponibles
						//Listage de l'ensemble des vidéos
						$liste_video = liste_videos_user($_SESSION['ID'], $bdd);
						
						//Affichage de la liste
						echo "<div class='list_choix_video'>";
						echo "<input type='radio' name='idvideo' checked value='last_one' /> La plus r&eacute;cente <br />"; //Proposition de lecture de la vidéo la plus récente
						foreach($liste_video as $view)
						{
							echo "<input type='radio' name='idvideo' value='".$view['ID']."' /> ".corrige_caracteres_speciaux(corrige_accent_javascript($view['nom_video']))."<br />";
						}
						echo "</div>";
					?>
					
					<textarea name="commentaire_video" placeholder="Commentaire sur la vid&eacute;o..."></textarea>
					
					<!-- Niveau de visibilité -->
					<?php choisir_niveau_visibilite(); ?>
					<!-- Fin de: Niveau de visibilité -->
					
					<!-- Bouton de validation -->
					<p><input type="submit" value="Ajouter" /></p>
					<!-- Fin de : Bouton de validation -->
					
					<p>Note : Lorsque vous envoyez une vid&eacute;o, vous prenez la responsabilit&eacute; du contenu envoy&eacute;. En cas de violation de droit d'auteur, vous serez d&eacute;sign&eacute; responsable du vol. Veillez donc &agrave; ne pas envoyer de vid&eacute;os susceptibles d'&ecirc;tre prot&eacute;g&eacute;e par droit d'auteur. <a href="http://192.168.1.5/divers/comunic/about.php?cgu" target="_blank"> R&eacute;f&eacute;rez-vous au conditions d'utilisation pour de plus amples informations. </a></p>
					<p>Si vous n'arrivez pas &agrave; envoyer une vid&eacute;o &agrave; cause de son poids trop &eacute;lev&eacute;, nous vous recommandons de la poster sur YouTube puis de l'int&eacute;grer &agrave; Comunic.</a></p>
				</form>
			</div>
			<div class="frame" id="_webpage_add">
				<p>Ajout d'un lien vers une page web</p>
				<form action="<?php echo $_SERVER['PHP_SELF']; ?>?id=<?php echo $idPersonn; ?>" method="post" name="Envoi d'un lien vers une URL">
					<div class="input-control text size4">
						<input type="text" name="adresse_page" value="" placeholder="URL de la page"/>
					</div>
					<div class="input-control text size4">
						<input type="text" name="texte_lien_page" value="" placeholder="Commentaire personnel"/>
					</div>
					
					<!-- Niveau de visibilité -->
					<?php choisir_niveau_visibilite(); ?>
					<!-- Fin de: Niveau de visibilité -->
						
					<!-- Bouton de validation -->
					<p><input type="submit" value="Ajouter" /></p>
					<!-- Fin de : Bouton de validation -->
					
					<!-- Propostion d'ajout aux favoris -->
					<p>
						Vous pouvez &eacute;galement ajouter ce lien dans vos favoris en le glissant sur votre liste de favoris afin de pouvoir partager plus facilement des pages web : <br />
						<a href="javascript:(function(){%20window.open('<?php echo $urlsite; ?>share.php?address=referer',%20'share_comunic',%20'width=600px,height=520px');%20})();" title="Partager sur Comunic">Partager sur Comunic</a>
					</p>
					<!-- Fin de: Propostion d'ajout aux favoris -->
				</form>
			</div>
			<div class="frame" id="_post_with_pdf_add">
				<p>Envoi d'un pdf</p>
				<form action="<?php echo $_SERVER['PHP_SELF']; ?>?id=<?php echo $idPersonn; ?>" method="post" name="Envoi d'un post avec un PDF" enctype="multipart/form-data">
					<input type="file" name="fichier_pdf" value="Choix du fichier PDF"/>
					<div class="input-control text size4">
						<input type="text" name="texte_post_with_pdf" value="" placeholder="Commentaire personnel"/>
					</div>
					
					<!-- Niveau de visibilité -->
					<?php choisir_niveau_visibilite(); ?>
					<!-- Fin de: Niveau de visibilité -->
						
					<!-- Bouton de validation -->
					<p><input type="submit" value="Envoyer" /></p>
					<!-- Fin de : Bouton de validation -->
					
					<!-- Rappel sur les copyright -->
					<p>
						<u>Note :</u> Vous &ecirc;tes responsables des contenus que vous envoyez sur Comunic. Ceux-ci ne doivent pas &ecirc;tre prot&eacute;g&eacute;s par droits d'auteur.
					</p>
					<!-- Fin de: Rappel sur les copyright  -->
				</form>
			</div>
			<div class="frame" id="_count_down_add">
				<form action='<?php echo $_SERVER['PHP_SELF']; ?>?id=<?php echo $idPersonn; ?>' method='post' name="Envoi d'un evenement">
					Ajout d'un &eacute;v&eacute;nement avec un compteur &agrave; rebours
					<div style="text-align: center;" class="metro">
					
					<!-- Choix de la date -->
					    <div class="input-control text size3" id="datepicker">
							<input name="date" type="text" required placeholder="Cliquez ici pour choisir la date...">
						</div>
					<!-- Fin de: Choix de la date -->
					
					<!-- Nom de l'événement -->
					<br /><div class="input-control text size4">
						<input type="text" name="nom_evenement" value="" placeholder="Nom de l'&eacute;v&eacute;nement"/>
					</div>
					<!-- Fin de: Nom de l'événement -->
					
					<!-- Niveau de visibilité -->
					<?php choisir_niveau_visibilite(); ?>
					<!-- Fin de: Niveau de visibilité -->
					
					<!-- Bouton de validation -->
					<p><input type="submit" value="Ajouter" /></p>
					<!-- Fin de : Bouton de validation -->
					</div>
				</form>
			</div>
			
			<!-- Création d'un sondage -->
			<div class="frame metro" id="_sondage_add">
				<form action='<?php echo $_SERVER['PHP_SELF']; ?>?id=<?php echo $idPersonn; ?>' method='post' name="Envoi d'un sondage">
					Cr&eacute;ation d'un sondage
					
					<!-- Question du sondage -->
					<br /><div class="input-control text size3">
						<input type="text" name="question_sondage" value="" placeholder="Question du sondage"/>
					</div>
					<!-- Fin de: Question du sondage -->
					
					<!-- Réponses au sondage -->
					<p>Choix du sondage</p>
					<div class="input-control textarea">
						<textarea name="reponses_sondage"><?php
							//Réponses au sondage par défaut
							echo "Choix 1\nChoix 2"; 
						?></textarea>
					</div>
					<p>Veuillez effectuer un retour &agrave; la ligne &agrave; chaque nouvelle r&eacute;ponse</p>
					<!-- Fin de: Réponses au sondage -->
					
					
					<!-- Commentaire du sondage -->
					<p>Commentaire du sondage (optionnel)</p>
					<div class="input-control textarea">
						<textarea name="commentaire_sondage"></textarea>
					</div>
					<!-- Fin de: Commentaire au sondage -->
					
					<!-- Niveau de visibilité -->
					<?php choisir_niveau_visibilite(); ?>
					<!-- Fin de: Niveau de visibilité -->
					
					<!-- Bouton de validation -->
					<p><input type="submit" value="Cr&eacute;er le sondage" /></p>
					<!-- Fin de : Bouton de validation -->
				</form>
			</div>
			<!-- Fin de: Création d'un sondage -->
		</div>
	</div>
</font>