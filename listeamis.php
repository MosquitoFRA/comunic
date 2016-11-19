<?php
//Démarrage de la session
session_start();

//Inclusion de la sécurité
include('securite.php');

//Init page
include('inc/initPage.php');

//Si il n'y a pas d'ID, redirection vers la liste d'amis de la personne
if(!isset($_GET['id']))
{
	header('Location: amis.php');
	die();
}

//Si l'ID est le même que celui de la personne connectée, redirection vers la page de la personne
if($_GET['id'] == $_SESSION['ID'])
{
	header('Location: amis.php');
	die();
}

//Enregistrement de l'ID dans une variable
$id = $_GET['id']*1;

if($id < 1)
	die("This isn't an account. <a href='index.php'>Home</a>");

//On récupère les informations de la personnes
$infos_personne = cherchenomprenom($id, $bdd);

//On vérifie que sa liste d'amis est publique
if($infos_personne['liste_amis_publique'] != 1)
{
	//Erreur fatale
	$erreur_fatale = "La liste d'amis de la personne n'est pas publique.";
}
else
{
	//On récupère la liste des amis actifs
	$liste_amis = liste_amis($id, $bdd, 1);
}
?><!DOCTYPE html>
<html>
	<head>
		<title>Liste des amis de <?php echo $infos_personne['prenom']." ".$infos_personne['nom']; ?></title>
		<?php include(pagesRelativePath('common/head.php')); ?>
	</head>
	<body class="metro">
		<?php include(pagesRelativePath('common/pageTop.php')); ?>
		
		<div class="nouveau_corps_page">
			<h2 class="titre">
				<a href="index.php?id=<?php echo $id; ?>"><i class="icon-arrow-left-3 fg-darker smaller"></i></a>
				Amis de <?php echo $infos_personne['prenom']." ".$infos_personne['nom']; ?>
			</h2>
			
			<!-- Erreur fatale -->
			<?php
				//On vérifie si il y a une erreur fatale
				if(isset($erreur_fatale))
					die(affiche_message_erreur($erreur_fatale, true)."</div></body></html>");
			?>
			
			<!-- Liste d'amis -->
			<div class="page_liste_amis">
			<?php
				//On affiche la liste d'amis
				foreach($liste_amis as $afficher_ami)
				{
					//Rercherche des informations de la personne
					$infos_ami = cherchenomprenom($afficher_ami, $bdd);
					
					?><div onClick="open_page_ameliore(<?php echo $afficher_ami; ?>);"><?php
					echo avatar($afficher_ami, "./", 32, 32);
					
					echo $infos_ami['prenom']." ";
					echo $infos_ami['nom'];
					?></div><?php
				}
			?></div>
		</div>
		
		<hr />
		<?php include(pagesRelativePath('common/pageBottom.php')); ?>
	</body>
</html>