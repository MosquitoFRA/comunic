<!DOCTYPE html>
<html>
	<head>
		<title>SVGEdit - Comunic</title>
		
		<!-- Styles de la page -->
		<style type="text/css">
			.container {
				text-align: justify
			}
			
			.container .button-container {
				text-align: center;
			}
		</style>
	</head>
	<body>
		<?php
			//Inclusion du menu
			$sub_folder = true;
			include('../menu.php');
		?>
		
		<div class="container">
			<h1><a href="../" class="nav-button transform"><span></span></a>&nbsp;SVGEdit</h1>
			<p>Cet &eacute;diteur en ligne va vous permettre de cr&eacute;er des images vectorielles, c'est-&agrave;-dire des images constitu&eacute;es de formules math&eacute;matiques qui font que celles-ci peuvent &ecirc;tre augment&eacute;es &agrave; l'infini. Ce logiciel vous permettra &eacute;galement d'exporter votre image dans divers formats tels que le format PDF ou le format PNG. Cet &eacute;diteur n'a pas besoin d'envoyer des informations au serveur. Votre confidentialit&eacute; est ainsi prot&eacute;g&eacute;e. Ce logiciel est disponible en OpenSource sur <a href="https://github.com/SVG-Edit/svgedit" target="_blank">GitHub</a></p>
			
			<div class="button-container">
				<a href="svg-editor.html" class="button command-button success" target="_blank">
					<span class="icon mif-arrow-right"></span>
					Acc&eacute;der &agrave; l'&eacute;diteur
					<small>Commencez d&egrave;s maintenant &agrave; &eacute;diter vos images avec SVGEdit</small>
				</a>
			</div>
		</div>
	</body>
</html>