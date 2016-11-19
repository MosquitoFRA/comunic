<?php
//Init page
include('inc/initPage.php');
	
	//On vérifie si le chat est activé
	if($activer_publique_chat == "non")
	{
		//Si il ne l'est pas, redirection vers la page d'acceuil
		header('location: index.php');
	}

	if(!isset($_GET['ajax']) && (!isset($_GET['more'])))
	{
		if(!isset($_SESSION['ID']))
		{
			//Redirection vers la page d'acceuil
			header('location: index.php');
		}
		?>
			<!-- Inclusion des ressources nécessaires au chat -->
			<?php echo code_inc_css(path_css_asset('chat.css'));
			echo code_inc_js(path_js_asset('chat.js')); ?>
			<!-- Fin de: Inclusion des ressources nécessaires au chat -->
			<!-- Chat -->
			<script type="text/javascript">
				function hidechat() {
				MM_showHideLayers('chat','','hide');
				MM_showHideLayers('boutonchatouvert','','hide');
				MM_showHideLayers('boutonchatferme','','show');
				}
			</script>
			<div class='boutonchat' id='boutonchatferme' onClick="MM_showHideLayers('chat','','show'); MM_showHideLayers('boutonchatferme','','hide'); MM_showHideLayers('boutonchatouvert','','show');">
			<?php echo $lang[29]; ?>
			</div>
			<div class='boutonchat' id='boutonchatouvert' onClick="MM_showHideLayers('chat','','hide'); MM_showHideLayers('boutonchatferme','','show'); MM_showHideLayers('boutonchatouvert','','hide');">
			<?php echo $lang[30]; ?>
			</div>
			<div class='chat' id='chat'>
				<div id="affichercontenuchat">
					<table id="contenuchat">
					<!--Image de chargement -->
					<!--<img src='img/wait.gif' title='Chargement en cours...' />-->
					<!--Fin de: image de chargement -->
					<!-- Chargment du chat -->
					<script>
					//Affichage du chat
					refreshChat();
					
					<?php
						if(verifierouvertureautomatiquechat($_SESSION['ID'], $bdd))
						{
							?>
							//On laisse ouvert le chat ouvert
							MM_showHideLayers('chat','','show');
							MM_showHideLayers('boutonchatferme','','hide');
							MM_showHideLayers('boutonchatouvert','','show');
							<?php
						}
						else
						{
						?>
							//Masquage du chat
							MM_showHideLayers('chat','','hide');
							MM_showHideLayers('boutonchatouvert','','hide');
						<?php
						}
						?>
					</script>
					<!-- Fin de: chargement du chat --->
					</table>
				</div>
				<!-- Formulaire d'envoi de post pour le chat -->
				<div id='formulairechat'>
					Message : <?php /*<img src="img/prive.png" title="Ouvrir chat priv&eacute;" width="16" height="16"  /><a href="#"  onclick="MM_showHideLayers('chatprive', '', 'show'); hidechat();">Chat priv&eacute;</a>*/ ?><br/><input type='text' name="message" cols="20" id="message"><br />
					<input type="button" value="<?php echo $lang[31]; ?>" onclick="submitChat();" />
				</div>
				<!--Fin de: formulaire d'envoi de post pour le chat -->
			</div>
			<!-- Fin de: Chat -->
		<?php
	}
	elseif(isset($_GET['ajax']))
	{	
		//Inclusion de la sécurité
		include_once('securite.php');
		
		// Pour l'historique du chat
		if(isset($_GET['nb']))
		{
			$nb = $_GET['nb'];
		}
		else
		{
			$nb = 10;
		}
		
		//Setup language
		$lang = detecteinstallelangue();


		if (isset($_POST['message'])) 
		{
			if ($_POST['message'] != '') 
			{
				$message = str_replace("\'", "'", $_POST['message']);
				//On coupe les mots si ils sont trop longs
				//$message = wordwrap($_POST['message'], 10, "\n", true);
				
				//On corrige les caractères spéciaux
				//$message = corrige_caracteres_speciaux($message);
				
				//On enregistre le chat
				postchat($_SESSION['ID'], $message, $bdd);
			}
		}
		
		//Recherche du contenu du chat
		$contenuchat = recuperecontenuchat($bdd, $nb);
		
		//Affichage des résultats
		foreach($contenuchat as $afficherchat)
		{
			?>
			<tr>
				<td><?php
			//On recherche l'avatar de la personne
			echo avatar($afficherchat['ID_personne'], './', 32, 32);
			
			?></td><td><?php
			
			//Requete de recherche de nom
			$affichernompersonne = cherchenomprenom($afficherchat['ID_personne'], $bdd);
			echo $affichernompersonne['prenom'].' '.$affichernompersonne['nom'];
			
			?></td><td>
			<?php echo affiche_smile(corrige_caracteres_speciaux(bloquebalise($afficherchat['message']))); ?></td></tr><?php
		}
		if(!isset($_GET['nb'])) { ?><tr><td></td><td></td><td><a href='chat.php?more'><?php echo $lang[62]; ?></a></td></tr><?php }
	}
	else
	{
		//Inclusion de la sécurité
		include_once('securite.php');
		
		
		//On prépare l'exécution du script
		unset($_GET['more']);
		?><!DOCTYPE html>
		<html>
			<head>
				<title>Historique du chat (50 derni&egrave;res entr&eacute;es)</title>
				<?php include(pagesRelativePath('common/head.php')); ?>
			</head>
			<body>
				<?php include(pagesRelativePath('common/pageTop.php')); ?>
				<h1 class='titre'>Historique du chat</h1>
				<table align='center' id='archivechat'></table>
				<script type='text/javascript'>
				var xhr = getXMLHttpRequest();
				xhr.onreadystatechange = function() {
						if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) {
								document.getElementById('archivechat').innerHTML = xhr.responseText; // Données textuelles récupérées
						}
				};

				xhr.open("GET", "chat.php?ajax=1&nb=50", true);
				xhr.send(null);
				</script>
				<hr />
				<?php include(pagesRelativePath('common/pageBottom.php')); ?>
			</body>
		</html>
		<?php
	}
?>
