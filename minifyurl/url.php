<?php
//Initializate page
include('../inc/initPage.php');

//Détermination du chemin
$id = str_replace(array('.php', '/'), '', strstr($_SERVER['REQUEST_URI'], ".php"));

if($id != "")
{
	//On recherche l'ID dans la base de données
	//On vérifie qu'il n'existe pas d'id portant ce nom dans la bdd
	$sql = "SELECT * FROM minifyURL WHERE ID = ?";
	$requete = $bdd->prepare($sql);
	$requete->execute(array($id));
	
	//Enregistrement des résultats
	while($resultats = $requete->fetch())
		$infos_id = $resultats;
	
	//Fermeture de la requête
	$requete->closeCursor();
	
	if(!isset($infos_id))
	{
		header('location: ../index.php');
		die();
	}
	
	//Si une redirection automatique est autorisée, on la fait
	if($infos_id['auto_redirect'] == 1)
		header('location: '.$infos_id['url']);
		
	//Affichage de la page
	$url = $infos_id['url'];
	?><!DOCTYPE html>
	<html>
		<head>
			<title>Redirection vers la page</title>
			<?php echo code_inc_css(path_css_asset('minifyURL.css')); ?>
		</head>
		<body>
			<div class="corps_redirection">
				<p>Vous allez &ecirc;tre redirig&eacute; vers la page <br /><i><?php echo $url ;?></i></p>
				<br />
				<a class="button" href="javascript:history.back(1)">Page pr&eacute;c&eacute;dente</a>
				<!--<a class="button" href="JavaScript:window.close()">Fermer l'onglet</a>-->
				<a class="button" href="<?php echo $url; ?>">Ouvrir la page</a>
			</div>
		</body>
	</html><?php
}
else
	header('location: ../index.php');