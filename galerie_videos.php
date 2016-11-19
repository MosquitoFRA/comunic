<?php
//Inclusion du script de sécurité
include('securite.php');

//Init page
include('inc/initPage.php');

//Vérification de la demande de suppression d'une vidéo
if(isset($_GET['delete']) && isset($_GET['confirm']))
{
	if($_GET['delete'] != "")
	{
		//On supprime la vidéo
		delete_movie($_GET['delete'], $_SESSION['ID'], $bdd);
		
		//Message d'information
		?><script type="text/javascript">alert("La video a ete supprimee."); </script><?php
	}
}
?>
<!DOCTYPE html>
<html>
	<head>
		<?php include(pagesRelativePath('common/head.php')); ?>
		<title>Galerie de vid&eacute;os</title>
	</head>
	<body>
		<?php include(pagesRelativePath('common/pageTop.php')); ?>
		<h1 class='titre'>Galerie de vid&eacute;os</h1>
		
		<?php
		//Listage de l'ensemble des vidéos
		$liste_video = liste_videos_user($_SESSION['ID'], $bdd);
		
		//Bouton d'upload
		?><div style="text-align: center;" class="metro"><input type="button" value="Envoi d'une vid&eacute;o" onclick="ouvre_fenetre_upload_video();" /></div><?php
		
		//Définition du poids total à 0
		$poids_total = 0;
		
		//Affichage de la liste
		echo "<table cellpadding='4' width='90%' border='#C0C0C0'>";
		foreach($liste_video as $video)
		{
			//Détermination du poids de la vidéo
			$poids_video = convertis_octets_vers_mo($video['size']);
			
			echo "<tr>";
				echo "<td width='180'>";
				affiche_video(array(array($video['URL'], $video['file_type'])), "none", "160", "106", "none");
				echo "</td>";
				echo "<td>";
					echo corrige_caracteres_speciaux(corrige_accent_javascript($video['nom_video']));
					echo " <img src='".path_img_asset('edit.png')."' width='16' height='16' onClick='change_nom_video(\"".$video['ID']."\", \"".$_SERVER['PHP_SELF']."\");' />";
				echo "</td>";
				echo "<td><i>".$poids_video." MO</i></td>";
				echo "<td>";
					echo "<img src='".path_img_asset('supp.png')."' onClick='confirm_delete_video(\"".$_SERVER['PHP_SELF']."?delete=".$video['ID']."&confirm=yes\");' />";
				echo "</td>";
			echo "</tr>";
		
			//Ajout du poids de la vidéo au poids total
			$poids_total = $poids_total + $poids_video;
		}
		
		//Affichage du poids total
		echo "<tr><td></td><td></td><td>".$poids_total." MO</td><td></td></tr>";
		
		echo "</table>";
		?>
		
		<hr>
		<?php
		//Inclusion du pied de page
		include(pagesRelativePath('common/pageBottom.php'));
		?>
	</body>
</html>