<?php
/**
 * Share file handler
 *
 * @author Pierre HUBERT
 */

isset($_SESSION) OR exit("Invalid call - ".$_SERVER['PHP_SELF']);

if(!isset($_GET['address']))
{
	header('location: index.php');
}

//On adapte l'URL si nécessaire
if($_GET['address'] == "referer" AND (isset($_SERVER['HTTP_REFERER'])))
	$_GET['address'] = $_SERVER['HTTP_REFERER'];

if(!isset($_SESSION['ID']))
{
	?><!DOCTYPE html>
	<html>
		<head>
			<title>Comunic</title>
			<?php include(pagesRelativePath('common/head.php')); ?>
		</head>
		<body>
			<?php include(pagesRelativePath('common/pageTop.php')); ?>
			<p>Vous voulez partager la page : <?php echo $_GET['address']; ?></p>
			<p><form action="connecter.php?light" method="post"><input type="hidden" id="logout_address" name="logout_address" value="<?php echo $_SERVER['REQUEST_URI']; ?>" ><input type='hidden' name="redirect" value="<?php echo $_SERVER['REQUEST_URI']; ?>" />Veuillez vous <input type='submit' value='connecter' /></form></p>
			<script type="text/javascript">
				//On cache le formulaire de connexion
				document.getElementById('loginuser').style.display = "none";
			</script>
		</body>
	</html><?php
}
else
{
		?><!DOCTYPE html>
		<html>
			<head>
				<title>Edition de votre partage</title>
				<?php include(pagesRelativePath('common/head.php')); ?>
			</head>
			<body>
				<?php $menu_light = 1; include(pagesRelativePath('common/pageTop.php')); ?>
				<?php
					if(!isset($_POST['texte']) OR !isset($_POST['adresse']))
					{
						?><p>Editez votre texte tel qu'il appara&icirc;tra (optionnel) :</p>
							<div class="editshare">
								<form action="<?php echo $_SERVER['PHP_SELF']; ?>?address=<?php echo $_GET['address']; ?>" method="post">
									<p><?php echo $_GET['address']; ?> <input type="hidden" name="adresse" value="<?php echo $_GET['address']; ?>" /></p>
									<textarea name="texte" id="ajoutevolue"></textarea>
									<br />
									<input type='submit' value='Envoyer' />
									<!-- Inclusion des scripts d'ajout évolué -->
									<script type="text/javascript" src="<?php echo path_js_asset('tiny_mce/tiny_mce.js'); ?>"></script>
									<script type="text/javascript">
										// O2k7 skin (silver)
										tinyMCE.init({
											// General options
											mode : "exact",
											elements : "ajoutevolue",
											theme : "advanced",
											skin : "o2k7",
											skin_variant : "silver",
											plugins : "lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,inlinepopups,autosave",

											// Theme options
											theme_advanced_buttons1 : "save,newdocument,print,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,fontselect,fontsizeselect",
											theme_advanced_buttons2 : "pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,removeformat,image,help,|,insertdate,inserttime,preview,|,forecolor,backcolor",
											theme_advanced_buttons3 : "tablecontrols,|,hr,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,fullscreen",
											theme_advanced_toolbar_location : "top",
											theme_advanced_toolbar_align : "left",
											theme_advanced_statusbar_location : "bottom",
											theme_advanced_resizing : false,

										});
										</script>
										<!-- Fin de: Inclusion des scripts d'ajout évolué -->
								</form>
							</div><?php
						}
						else
						{
								//Enregistrement de l'URL
								$url = $_POST['adresse'];
								$description = ($_POST['texte'] != "" ? "<p>".$_POST['texte']."</p>" : "");
								$niveau_visibilite = 2; //Choix arbitraire
								
								//Inclusion de la fonction d'analyse
								require_once(relativePath_3rdparty('analysing_page/analyser_fr.php'));
								
								//Contrôle de l'URL
								if(!preg_match('<http://>', $url) OR !preg_match('<http://>', $url))
								{
									echo affiche_message_erreur("L'URL saisie est invalide !"); //L'URL donnée est invalide
								}
								else
								{
									//On commence par récupérer le code source de l'URL
									ob_start();
									$source = file_get_contents($url);
									ob_end_clean();
									
									//Contrôle de la source
									if($source == "")
									{
										echo affiche_message_erreur("La page demand&eacute;e n'a pas &eacute;t&eacute; trouv&eacute;e !"); //Page non trouvée (404)
									}
									else
									{
										//On peut tenter d'extraire les informations
										$infos_page = analyse_source_page_extrait_description($source);
										
										//On prépare l'enregistrement de la page
										$infos_page['titre'] = ($infos_page['titre'] == null ? "default" : $infos_page['titre']);
										$infos_page['description'] = ($infos_page['description'] == null ? "default" : $infos_page['description']);
										$infos_page['image'] = ($infos_page['image'] == null ? "default" : $infos_page['image']);
										
										//On enregistre la page
										//Ajout du texte
										ajouttexte($_SESSION['ID'], $description, $bdd, $niveau_visibilite, "webpage_link", "", 0, 0, 0, $url, $infos_page['titre'], $infos_page['description'], $infos_page['image']);
										
										//Message de succès
										echo "<p><img src='".path_img_asset('succes.png')."' title='succès' alt='V' />Le lien vers la page a bien &eacute;t&eacute; ajout&eacute;.</p>";
									}
								}
							
							echo "<p><a href='JavaScript:window.close()'>Fermer la fen&ecirc;tre</a></p>";
						}
						?>	
			</body>
		</html><?php
}