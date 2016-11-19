<?php
/**
 * User's home
 *
 * @author Pierre HUBERT
 */
isset($_SESSION) OR exit('Invalid call! - homeUser.inc.php');

//En-tête autorisé par défaut
$allow_entete = true;

//Vérification de l'existence de la demande d'un autre profil
if(isset($_GET['id']))
{
	$id = $_GET['id']*1; //Sécurité
}
else
{
	//Sinon on prend la page de la personne
	//$id = $_SESSION['ID'];
	//Fil d'actualité
	$id = "fil";
	
	//Désactivation de l'en-tête
	$allow_entete = false;
	$page_fil = true;
	
	if($afficher['vu_message_info_fil'] == 0)
	{
		?><script type="text/javascript">
			$.Dialog({
				overlay: true,
				shadow: true,
				draggable: true,
				flat: true,
				icon: '<span class="icon-newspaper"></span>',
				title: 'Voici le fil d\'actualit&eacute;',
				content: '<p>Avec le fil d\'actualit&eacute;, vous pouvez voir les derni&egrave;res publications auxquelles vous avez acc&egrave;s.</p> <br /><br /><br />' +
				'<div style="text-align: right; margin-right: 5px;"><button class="button primary" type="button" onclick="$.Dialog.close(); ajax_rapide(\'action.php?actionid=35\');">C\'est parti !</button></div>',
			});
		</script><?php
	}
}

//Correction de l'erreur de certains scripts appelés
$_GET['id'] = $id;

//Récupération des informations sur la personne
$afficher = cherchenomprenom($id, $bdd);

//Définition de la variable de stockage des informations sur les utilisateurs
$info_users = array();
$info_users[$id]['table_utilisateurs'] = $afficher;
$info_users[$id]['avatar_32_32'] = avatar($id, "./", 32, 32);

//Modification du titre de la page au nom de la personne
?><script type="text/javascript">
		//Changement du titre de la page au nom de la personne
		document.title = "<?php echo $afficher['prenom']." ".$afficher['nom']; ?> - Comunic";
</script>
 <?php

?>
<div class='contenupage'>
<div id="header_contenu"  contextmenu="header_page">
	<?php
	//En-tête si autorisé
	if($allow_entete)
	{
		//Récupération du statut de l'abonnement à la personne
		$header_page_ok = true;
		include('inc/header_page.php'); 
		?><script type="text/javascript">get_abonnement(<?php echo $id; ?>, 0);</script><?php
	}
	
?></div>
  <?php
	//La suite ne s'affiche que si c'est un amis
	if(isset($non_amis))
	{
		//On vérifie si la page est non publique
		if ($afficher['public'] == 0)
		{
			//On bloque le reste du chargment de la page
			echo '<div id="add_form_texte"></div><p>'.$afficher['prenom']." ".$afficher['nom']." refuse que vous voyez sa page sans etre son amis.</p> <p>Vous pouvez lui envoyer une demande pour devenir son ami ci-dessus.</p></table></div></body></html>";
			echo '<hr />';
			include(pagesRelativePath('common/pageBottom.php'));
			die();
			}
	}
  ?>
	<?php
		//Inclusion du script pour l'ajout de posts
		echo "<div id='add_form_texte'>";
			if(!isset($_GET['post']) AND isset($_SESSION['ID']))
				include('inc/pages/homeUser/addPost/addpost.inc.php');
		echo "</div>";
	?>
	  <table align='center' class='corpstextes' id="corpstexte">
		<?php	
		
		//On vérifie si il s'agit d'une page supérieur à la page 1
		if(isset($_GET['page']))
		{
			if($_GET['page'] > 0)
			{
				echo "<tr><td></td><td>";
				
				if($_GET['page'] !== '1')
				{
					$pageprecedent = $_GET['page'] - 1;
					
					echo "<a href='index.php?id=".$id."&page=".$pageprecedent."'><input class='retourpageprincipale' type='button' value=\"".$lang[53]."\" /></a>";
				}
				
				echo "<a href='index.php?id=".$id."'><input class='retourpageprincipale' type='button' value=\"".$lang[52]."\" /></a>";
				
				$page = $_GET['page']*1;
				echo "</td></tr>";
			}
			else
			{
				$page = 0;
			}
		}
		else
		{
			$page = 0;
		}

	//Définition de la requête AJAX de recherche de textes
	$ok_textes_check = "source:index.php";
	$_GET['id'] = $id;
	$_GET['page'] = $page;
	$no_view_more = true;
	include('viewTexts/viewTexts.inc.php');
	
	if(!isset($_GET['post']))
		echo '<tr class="metro"><td colspan="2"><input value="Afficher plus de textes" onclick="get_show_textes('.$id.', \'corpstexte\', '.($page+1).', 0); this.parentNode.parentNode.style.display = \'none\'" type="button"></td></tr>';
	
	//Fermeture du tableau
	?></table><?php
	
	
//Fermeture de la balise <div>
echo "</div>";

//Inclusion du pied de page
include(pagesRelativePath('common/pageBottom.php'));