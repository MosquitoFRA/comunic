<!DOCTYPE html>
<html>
	<head>
		<title>Speaker.js - Comunic</title>
		
		<!-- Styles de la page -->
		<style type="text/css">
			body {
				text-align: justify;
			}
			
			#speaker_area {
				text-align: center;
				margin-top: 17%;
				background-color: rgba(27, 161, 226, 0.44);
				padding: 20px;
				border-radius: 5px;
			}
			
			.texte_parole {
				width: 55% !important;
			}
		</style>
		
		<!-- Inclusion de Speak.js -->
		<script type="text/javascript" src="js/mespeak.js"></script>
		
		<!-- Initialisation de Speak.js -->
		<script type="text/javascript">
			//Chargement de la voix française
			meSpeak.loadVoice("voices/fr.json");
			
			//Chargement de la configuration
			meSpeak.loadConfig("js/mespeak_config.json");
			
			//Permet de forcer le telechargement d'un fichier en JS
			function forceDownload(fileURL, fileName) {
				// non-IE
				if (!window.ActiveXObject) {
					var save = document.createElement('a');
					save.href = fileURL;
					save.target = '_blank';
					save.download = fileName || 'unknown';
			 
					var event = document.createEvent('Event');
					event.initEvent('click', true, true);
					save.dispatchEvent(event);
					(window.URL || window.webkitURL).revokeObjectURL(save.href);
				}
			 
				// for IE
				else if ( !! window.ActiveXObject && document.execCommand)     {
					var _window = window.open(fileURL, '_blank');
					_window.document.close();
					_window.document.execCommand('SaveAs', true, fileName || fileURL)
					_window.close();
				}
			}
			
			function gospeak() {
				//Récupération du texte de parole
				texte = document.getElementById('text_speak').value;
				
				//On vérifie si il faut utiliser une voix de femme ou non
				use_female_voice = document.getElementById('enable_female_voice').checked;
				
				//On vérifie si il faut télécharger le résultat ou non
				download_result = document.getElementById('download_result').checked;
				
				if(use_female_voice == false)
				{
					//Utilisation d'une voix d'homme
					var variante = "m3";
				}
				else
				{
					//Utilisation d'une voix de femme
					var variante = "f5";
				}
				
				if(download_result == false)
				{
					//On parle
					meSpeak.speak(texte, {voice: "fr", variant: variante});
				}
				else
				{
					//Préparer le fichier pour le téléchargement puis le télécharger
					var myDataUrl = meSpeak.speak(texte, {voice: "fr", variant: variante, 'rawdata': 'data-url' });
					window.open(myDataUrl);
				}
			}
		</script>
	</head>
	<body>
		<?php $sub_folder = true; include('../menu.php'); ?>
		
		<div class="container">
			<h1><a href="../" class="nav-button transform"><span></span></a>&nbsp;Speaker.js</h1>
			<p>Ce logiciel web vous permet d'effectuer la synth&egrave;se vocale de ce que vous saisissez dans le champ de texte. Note: Gr&acirc;ce &agrave; la puissance de Javascript, les paroles sont transform&eacute;es en  fichier vocal localement sur votre navigateur. Votre confidentialit&eacute; est ainsi respect&eacute;e. Ce syst&egrave;me est bas&eacute; sur le projet meSpeak.js, dont le site est <a href="http://www.masswerk.at/mespeak/" target="_blank">accessible ici</a>.</p>
			
			<!-- Zone de gestion de la parole -->
			<div id="speaker_area">
				
				<!-- Saisie la parole -->
				<div class="input-control text texte_parole">
					<input type="text" placeholder="Saissez ici votre texte" id="text_speak" />
				</div>
				
				&nbsp;
				
				<!-- Activer ou non la voix de femme -->
				<label class="switch">
					<input type="checkbox" id="enable_female_voice"/>
					<span class="check"></span>
					Voix de femme
				</label>
				
				<!-- Télécharger ou non -->
				<label class="switch">
					<input type="checkbox" id="download_result"/>
					<span class="check"></span>
					T&eacute;l&eacute;charger
				</label>
				
				<!-- Parler! -->
				<button class="button" onClick="gospeak();">
					<span class="mif-volume-high"></span>
				</button>
			</div>
		</div>
	</body>
</html>