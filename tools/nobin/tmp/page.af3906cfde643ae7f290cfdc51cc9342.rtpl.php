<?php if(!class_exists('raintpl')){exit;}?><!DOCTYPE html>
<html lang="fr">
	<head>
		<title>ZeroBin - Comunic</title>
		<meta name="robots" content="noindex" />
		<link type="text/css" rel="stylesheet" href="css/zerobin-for-comunic.css" /> 
		<script src="js/jquery.js"></script>
		<script src="js/sjcl.js"></script>
		<script src="js/base64.js"></script>
		<script src="js/rawdeflate.js"></script>
		<script src="js/rawinflate.js"></script>
		<script src="js/zerobin-fr.js"></script>
		<link type="text/css" rel="stylesheet" href="js/highlight.styles/monokai.css">
		<script src="js/highlight.pack.js"></script>
		
		<!-- Metro UI CSS 3.0 -->
		<link type="text/css" rel="stylesheet" href="../3rdparty/metrouicss/css/metro.min.css" />

		<!--[if lt IE 10]>
		<style> body {padding-left:60px;padding-right:60px;} div#ienotice {display:block;} </style>
		<![endif]-->

		<!--[if lt IE 10]>
		<style> div#ienotice {display:block; }  div#oldienotice {display:block; } </style>
		<![endif]-->

	</head>
	<body>
	
		<!-- Barre de menu -->
		<div class="app-bar" data-role="appbar">
			<a class="app-bar-element" href="../">Outils</a>
			<span class="app-bar-divider"></span>
			<a class="app-bar-element" onClick="window.location.href=scriptLocation();return false;" >ZeroBin</a>
			<a class="app-bar-element" href="../../">Retour &agrave; Comunic</a>
		</div>

		
		<div class="window info-zerobin">
            <div class="window-caption">
                <span class="window-caption-icon"><span class="mif-windows"></span></span>
                <span class="window-caption-title">A propos de ZeroBin</span>
                <span class="btn-close" onClick="this.parentNode.parentNode.style.visibility='hidden';"></span>
            </div>
            <div class="window-content" style="height: 100px">
                ZeroBin est un service en ligne libre et minimaliste qui permet à n’importe qui de partager des textes de manière confidentielle et sécurisée.
				Les données sont chiffrées/déchiffrées <i>dans le navigateur</i> en utilisant l’algorithme AES 256 bits. 
				Plus d’informations sur le <a href="http://sebsauvage.net/wiki/doku.php?id=php:zerobin">site du projet</a>.
            </div>
        </div>
		
		<noscript><div class="nonworking">Zerobin requiert l’activation du Javascript pour fonctionner.<br>Désolé pour le désagrément.</div></noscript>
		<div id="oldienotice" class="nonworking">Un navigateur web moderne est requis pour utiliser ZeroBin.</div>
		<div id="ienotice">Vous utilisez encore Internet Explorer ? &nbsp;Rendez-vous service, basculez sur un navigateur web moderne : 
			<a href="http://www.mozilla.org/firefox/">Firefox (Recommand&eacute;)</a>, 
			<a href="http://www.opera.com/">Opera</a>, 
			<a href="http://www.google.com/chrome">Chrome</a>, 
			<a href="http://www.apple.com/safari">Safari</a>…
		</div>
		
		<!-- Containeur principal -->
		<div class="container">
			<!-- Affichage du status -->
			<div id="status"><?php echo $STATUS;?></div>
			
			<!-- Message d'erreur -->
			<div id="errormessage" style="display:none"><?php echo htmlspecialchars( $ERRORMESSAGE );?></div>
			
			<!-- Barre d'outils -->
			<div id="toolbar">
				<!-- Bouton de création d'un nouveau texte et de nettoyage -->
				<button id="newbutton" class="button success" onclick="window.location.href=scriptLocation();return false;" style="display:none;"><img src="img/icon_new.png"/> Nouveau</button>
				
				<!-- Bouton de confirmation d'envoi -->
				<button id="sendbutton" class="button primary" onclick="send_data();return false;" style="display:none;"><img src="img/icon_send.png"/> Envoyer</button>
				
				<!-- Bouton permettant de cloner le message actuel dans un nouveau -->
				<button id="clonebutton" class="button danger" onclick="clonePaste();return false;" style="display:none;"><img src="img/icon_clone.png" /> Cloner</button>
				
				<!-- Bouton permettant d'afficher le texte brut -->
				<button id="rawtextbutton" class="button info" onclick="rawText();return false;" style="display:none; "><img src="img/icon_raw.png" width="15" height="15" style="padding:1px 0px 1px 0px;"/> Texte brut</button>
				
				<!-- Définition d'une date d'expiration -->
				<div id="expiration" class="input-control select" style="display:none;">
					<select id="pasteExpiration" name="pasteExpiration">
						<option value="5min">5 minutes</option>
						<option value="10min">10 minutes</option>
						<option value="1hour">1 heure</option>
						<option value="1day">1 jour</option>
						<option value="1week">1 semaine</option>
						<option value="1month" selected="selected">Expiration : 1 mois</option>
						<option value="1year">1 an</option>
						<option value="never">Jamais</option>
					</select>
				</div>
				
				<!-- Affichage du temps restant (si nécessaire) -->
				<div id="remainingtime" style="display:none;"></div>
				
				
				<!-- Détruire après la première lecture -->
				<div id="burnafterreadingoption" class="button" style="display:none;">
					<input type="checkbox" id="burnafterreading" name="burnafterreading" />
					<label for="burnafterreading">Une seule lecture</label>
				</div>
				
				<!-- Bouton permettant la création d'une discussion -->
				<div id="opendisc" class="button" style="display:none;">
					<input type="checkbox" id="opendiscussion" name="opendiscussion" />
					<label for="opendiscussion">Discussion</label>
				</div>
				
				<!-- Bouton permettant la coloration syntaxique -->
				<div id="syntaxcoloringoption" class="button" style="display:none;">
					<input type="checkbox" id="syntaxcoloring" name="syntaxcoloring" />
					<label for="syntaxcoloring">Coloration syntaxique</label>
				</div>
			</div>
			
			
			<div id="pasteresult" style="display:none;">
			  <div class="padding10 bg-emerald fg-white pastelink" id="pastelink"></div>
			  <div id="deletelink"></div>
			</div>
			<div id="cleartext" style="display:none;"></div>
			<textarea id="message" name="message" cols="80" rows="25" style="display:none;"></textarea>
			<div id="discussion" style="display:none;">
				<h4>Discussion</h4>
				<div id="comments"></div>
			</div>
			<div id="cipherdata" style="display:none;"><?php echo $CIPHERDATA;?></div>
		</div>
	</body>
</html>
