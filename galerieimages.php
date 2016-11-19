<?php
//Démarrage de la session
session_start();

//Initializate page
include('inc/initPage.php');

//On vérifie qu'il y a bien un ID
if(!isset($_GET['id']))
{
	header('Location: index.php');
	die();
}

//On extrait l'ID de la personne et on le vérifie
$id = $_GET['id']*1;
if(1 > $id)
{
	$url_saisie = $_SERVER['REQUEST_URI'];
	$uri_base = $urlsite;
	include('inc/404.html');
	die();
}

//On récupère les informations sur la personne
$infos_personne = cherchenomprenom($id, $bdd);

//On définit le niveau de visibilité dont on dispose
$niveau_visibilite_autorise = is_allowed_to_view_page($id, $bdd);

//On vérifie si la personne est autorisée à visualiser la page
if(!$niveau_visibilite_autorise)
{
	//On vérifie si l'utilisateur est connecté
	if(isset($_SESSION['ID']))
	{
		//die('<p>'.corrige_caracteres_speciaux(return_nom_prenom_user($id, $bdd))." refuse que vous voyez sa page sans &ecirc;tre son amis.</p> <p>Vous pouvez lui envoyer une demande pour devenir son ami <a href='index.php?id=".$id."'>ici</a>.</p>");
	}
	else
		die("Vous n'&ecirc;tes pas autoris&eacute;s &agrave; voir le contenu de cette page.");
}

//On vérifie si l'utilisateur appartient à un des groupes du propriétaire de la page
$liste_groupes = (isset($_SESSION['ID']) ? search_appartenance_groupes($id, $_SESSION['ID'], $bdd) : array());

//Récupération de toutes les images
$liste_images = affichertextes($id, $bdd, 0, 10, $niveau_visibilite_autorise, true, false, $liste_groupes, "image")
	
?><!DOCTYPE html>
<html>
	<head>
		<title>Galerie d'images de <?php echo corrige_caracteres_speciaux($infos_personne['prenom']." ".$infos_personne['nom']); ?></title>
		<?php include(pagesRelativePath('common/head.php')); ?>
	</head>
	<body class="metro">
		<?php include(pagesRelativePath('common/pageTop.php')); ?>
		
		<div class="nouveau_corps_page">
			<h2 class="titre">
				<a href="index.php?id=<?php echo $id; ?>"><i class="icon-arrow-left-3 fg-darker smaller"></i></a>
				Images de <?php echo $infos_personne['prenom']." ".$infos_personne['nom']; ?>
			</h2>
			
			<?php
				foreach($liste_images as $afficher_image)
				{
					?><a class="fancybox" rel="group" href="<?php echo webUserDataFolder($afficher_image['path']); ?>" title="<a href='index.php?id=<?php echo $id;0 ?>&amp;post=<?php echo $afficher_image['ID']; ?>'> Voir plus </a>">
						<img src="<?php echo webUserDataFolder($afficher_image['path']); ?>" alt="" height="200">
					</a><?php
				}
			?>
			
			<!-- En cas de présence d'une seule image -->
			<?php
				if(count($liste_images) == 0)
				{
					echo "<p>Il n'y a aucune image pour le moment.</p>";
				}
			?>
		</div>
		
		<hr />
		<?php include(pagesRelativePath('common/pageBottom.php')); ?>
	</body>
</html>