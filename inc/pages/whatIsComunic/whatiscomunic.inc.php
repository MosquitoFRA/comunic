<?php
/**
 * This page present the project
 *
 * @author Pierre HUBERT
 */

isset($_SESSION) OR exit("Invalid call - ".$_SERVER['PHP_SELF']);

?><!DOCTYPE html>
<html>
	<head>
		<title>Qu'est-ce que Comunic ?</title>
		<?php include(pagesRelativePath('common/head.php')); ?>
		
		<!-- Feuille de style interne -->
		<style type="text/css">
			body {
				background-color: rgba(27, 161, 226, 0.64) !important;
				color: #FFF !important;
				text-align: justify;
			}
			.what_is_comunic_page h1, .what_is_comunic_page p, .what_is_comunic_page h2 {
				color: #FFF !important;
			}
				
			.what_is_comunic_page .img_what_is_comunic_page_contener{
				margin: auto;
				width: 1311px;
				max-width: 100%;
			}
				
			.what_is_comunic_page .form_first_login {
				margin: auto !important;
				width: 500px;
				text-align: center;
			}
		</style>
		<!-- Fin de: Feuille de style interne -->
	</head>
	<body class="metro">
		<?php include(pagesRelativePath('common/pageTop.php')); ?>
		<div class="what_is_comunic_page">
			<h1 class="titre">D&eacute;couverte</h1>
			<p>Comunic est un r&eacute;seau social gratuit et ouvert &agrave; tous. D&eacute;couvrez d&egrave;s maintenant quelques unes de ses fonctionnalit&eacute;s principales &agrave; l'aide de l'image ci-dessous :</p>
			<div class="img_what_is_comunic_page_contener">
				<?php echo code_inc_img(path_img_asset('discover_comunic_modifie.png')); ?>
			</div>
			
			<h2>Communication</h2>
			<p>Comunic propose d&eacute;sormais de nouvelle fonctionalit&eacute;s de partage d'information. Vous pouvez poster des textes sur votre page ou celles de vos amis, envoyer des photos en tant que post ou que commentaire, poster une vid&eacute;o h&eacute;berg&eacute;e sur Comunic ou sur Youtube, poster des PDF, int&eacute;grer des compteurs &agrave; rebours ou encore cr&eacute;er un sondage. Le chat priv&eacute; vous permet de ne vous adresser qu'&agrave; une seule personne et la messagerie interne vous permettra d'&eacute;crire un message d'une longueur plus importante que celle du chat priv&eacute; &agrave; une seule personne.</p>
			
			<h2> <i class="icon-locked on-left"></i> Vie priv&eacute;e</h2>
			<p>Comunic s'engage &agrave; respecter votre vie priv&eacute;e. Pour cela, aucune des informations personnelles que vous posterez sur votre page, dans vos commentaires, dans le chat priv&eacute;, par la messagerie, par l'interm&eacute;diaire d'images ou de vid&eacute;os ne seront soumises &agrave; une analyse informatique ou humaine. Lorsque vous supprimez un contenu que vous avez mis en ligne ou votre compte, leur suppression est d&eacute;finitive et donc non annulable. Seule une plainte (contenu agressif / violation de droits d'auteur) effectu&eacute;e par le formulaire de contact avec l'administration pourrait donner suite &agrave; un contr&ocirc;le du compte pour la v&eacute;rification et / ou la suppression du contenu. Il n'existe dans Comunic aucun outil &agrave; but statistique bas&eacute;s sur vos donn&eacute;es personnelles. Vous &ecirc;tes tranquille !</p>
		</div>
		
		<?php
			if(!isset($_SESSION['ID']))
			{
				?><div style="margin: auto;" class="button_create_account">
					<a class="command-button info" href="creercompte.php">
						<i class="icon-pencil on-left"></i>
						Cr&eacute;ez-vous un compte
						<small>D&egrave;s maintenant, commencez &agrave; communiquer.</small>
					</a>
				</div><?php
			}
		?>
		
		<hr />
		<?php include(pagesRelativePath('common/pageBottom.php')); ?>
	</body>
</html>