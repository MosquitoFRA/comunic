<?php
/**
 * List all opened users
 *
 * @author Pierre HUBERT
 */

isset($_SESSION) OR exit("Invalid call - ".$_SERVER['PHP_SELF']);

?><!DOCTYPE html>
<html>
	<head>
		<?php include(pagesRelativePath('common/head.php')); ?>
		<title><?php echo $lang[11]; ?> - Comunic</title>
	</head>
	<body>
		<?php include(pagesRelativePath('common/pageTop.php')); ?>
		<h1 class='titre'><?php echo $lang[11]; ?></h1>
		<?php
			//On récupère la liste des pages publiques
			$liste = get_page_publique($bdd);
			
			//On affiche le résultat
			?><p>Voici l'ensemble de la liste des pages ouvertes :</p>
			<table><?php
			foreach($liste as $afficher)
			{
				//On recherche les informations de la personne
				$infopersonne = cherchenomprenom($afficher['ID'], $bdd);
				
				//On affiche le résultat
				echo "<tr><td>".avatar($afficher['ID'], './')."</td><td><a href='./?id=".$afficher['ID']."'>".$afficher['prenom']." ".$afficher['nom']."</a></td></tr>";
			}
			?></table><?php
		?><hr />
		<?php include(pagesRelativePath('common/pageBottom.php')); ?>
	</body>
</html>