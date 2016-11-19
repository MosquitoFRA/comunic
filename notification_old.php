<?php
//Inclusion de la sécurité
include('securite.php');

//Init page
include('inc/initPage.php');

if(!isset($_GET['affiche_notification']))
{
	?><!DOCTYPE html>
	<html>
		<head>
			<title>Notifications</title>
		</head>
		<body>
			<b>Bienvenu dans les notifications !</b> A l'aide de cette fen&ecirc;tre vous pourrez visualiser les derniers posts de vos amis.</p>
			<p style='text-align: center;'><img src="img/nouveaute_user.png" title="Image accompagnant la pr&eacute;sentation des nouveaut&eacute;es" /> <a href="<?php echo $_SERVER['PHP_SELF']; ?>?affiche_notification"> C'est parti!</a></p>			</body>
	</html>
	<?php
}
else
{	
	if(!isset($_GET['id']))
	{
		?><!DOCTYPE html>
		<html>
			<head>
				<title>Rercherche d'une personne</title>
			</head>
			<body>
				<b>Recherche d'une personne</b>
				<table><?php 
				//On récupère la liste des amis
				$listeamis = liste_amis($_SESSION['ID'], $bdd, 1);
				
				//On l'affiche dans le formulaire
				foreach($listeamis as $afficher)
				{
					$infopersonne = cherchenomprenom($afficher, $bdd);
					
					echo "<tr><td>".avatar($infopersonne['ID'])."</td><td><a href='".$_SERVER['PHP_SELF']."?affiche_notification&id=".$afficher."'>".$infopersonne['prenom'].' '.$infopersonne['nom']."</a></td></tr>";
				}
				?></table>
			</body>
		</html><?php
	}
	else
	{
		if(!detectesilapersonneestamie($_SESSION['ID'], $_GET['id'], $bdd))
		{
			//On affiche un message d'erreur
			?><!DOCTYPE html><html><head><title>Erreur</title></head><body><table><tr><td><img src="img/exclamation.png" title="Erreur" /></td><td><b>Erreur !</b> La personne demand&eacute;e n'est pas un(e) de vos ami(e)s.</td></tr></table><p><a href="<?php echo $_SERVER['PHP_SELF']; ?>">Retour</a></p></body></html><?php
			
			//On quitte le script courant
			die();
		}
		
		//On affiche un lien de retour et d'accès à la page
		?><a href='<?php echo $_SERVER['PHP_SELF']; ?>?affiche_notification'>Retour &agrave; la liste d'amis</a> <a href="index.php?id=<?php echo $_GET['id']; ?>" target="_parent">Visiter sa page</a><?php
		
		//On recherche le dernier texte
		$texte = affichertextes($_GET['id'], $bdd, 0, 1);
		
		//On vérifie si un texte a déja été posté
		if(isset($texte[0]['texte']))
		{
			//On récupère les informations de la personne
			$infopersonne = cherchenomprenom($_GET['id'], $bdd);
			
			//On affiche le dernier texte
			echo "<table><tr><td>".avatar($infopersonne['ID']).$infopersonne['prenom'].' '.$infopersonne['nom']."</td><td>".adapte_texte_image($texte[0]['texte'])."</td></tr></table>";
		}
		else
		{
			echo "<p>Aucun texte n'a &eacute;t&eacute; post&eacute; sur cette page.</p>";
		}
	}
}
?>