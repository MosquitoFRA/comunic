<?php
/**
 * Show user's comments
 *
 * @author Pierre HUBERT
 */

//Sécurité
include('securite.php');

//Initializate page
include('inc/initPage.php');

//Check login
if(!verifieconnnexionutilisateur())
{
	header('location: index.php');
}

//Check if it is a valid call
if(!isset($_GET['idtexte']) || (!isset($_GET['id'])))
{
	header('location: index.php');
}

//On recherche les commentaires et on les affiche
//Connexion à la base de données
include_once('connexiondb.php');

//On recherche les commentaires
$commentaires = affichecommentaire($_GET['idtexte'], $bdd);

//On affiche la liste des commentaires
foreach($commentaires as $affichercommentaires)
{
	echo '<tr><td class="commentaire">';
	echo avatar($affichercommentaires['ID_personne'], './', 32, 32);
	$infopersonne =  cherchenomprenom($affichercommentaires['ID_personne'], $bdd);
	echo corrige_caracteres_speciaux($infopersonne['prenom'].' '.$infopersonne['nom']);
	
	//Affichage de l'image du commentaire(si il y en a une)
	if($affichercommentaires['image_commentaire'] != "")
	{
		// Ancienne version
		// echo "<img src='data:image/png;base64,".$affichercommentaires['image_commentaire']."' />";
		
		//Adresse de l'image
		$adresse_image = $urlsite."imgcommentaire.php/".base64_encode($affichercommentaires['ID'])."/".sha1($affichercommentaires['commentaire']);
		
		echo "<a style='cursor: pointer' onClick='$.fancybox.open({href :\"".$adresse_image."\", type : \"image\"});'><img src='".$adresse_image."' height='100' /></a> ";
	}
	
	echo afficher_lien(corrige_caracteres_speciaux(decorrige_accent_javascript(affiche_smile(bloquebalise($affichercommentaires['commentaire'], "commentaire")))));
	
	//Bouton du j'aime pour commentaire
	bouton_aime_commentaire($affichercommentaires['ID'], $_GET['idtexte'], $bdd);
	
	echo "<span>";
	if($affichercommentaires['ID_personne'] == $_SESSION['ID'] || $_SESSION['ID'] == $_GET['id'])
	{
		//La personne est autorisée à supprimer le commentaire
		echo "<a onClick='delete_comment(\"".$affichercommentaires['ID']."\", \"".$_GET['idtexte']."\", \"".$_GET['id']."\", \"".(isset($_GET['page']) ? $_GET['page'] : 0)."\")";
		echo "'>";
		echo code_inc_img(path_img_asset('supp.png'), "Supprimer le commentaire");
		echo "</a>";
	}
	
	//On vérifie si la personne est bien celle qui est l'auteur du commentaire pour pouvoir le modifier
	if($_SESSION['ID'] == $affichercommentaires['ID_personne'])
	{
		echo "<a onClick='editcommentaire(\"".$affichercommentaires['ID']."\", \"".$_GET['idtexte']."\", \"".$_GET['id']."\", \"".(isset($_GET['page']) ? $_GET['page'] : 0)."\");'>";
			echo code_inc_img(path_img_asset('edit.png'), "Editer le commentaire", "16", "16");

		echo "</a>";
	}
	
	//Date d'envoi du commentaire
	echo adapte_date($affichercommentaires['date_envoi']);
	
	echo '</span></td></tr>';
}
?><!-- Nouveau commentaire -->
<tr class="add_comment">
	<td>
		<form id="addcommentaire_<?php echo $_GET['idtexte']; ?>" enctype="multipart/form-data" method="post">
			<!-- Avatar -->
			<?php echo avatar($_SESSION['ID'], "./", 32, 32); ?>

			<!-- Comment input -->
			<input type='text' placeholder="Ajout d'un commentaire..." name='commentaire' id="addcommentaire<?php echo $_GET['idtexte']; ?>" /><?php echo source_ajout_smiley($_GET['idtexte']); ?>

			<!-- Optionnal: Add an image -->
			<label class="input_commentaire_image">
				<?php echo code_inc_img(path_img_asset('small/image.png'), "Optionnel: ajout d'une image"); ?>
				<input type="file" id="image_<?php echo $_GET['idtexte']; ?>" name="image" />
			</label>

			<!-- ID of the text -->
			<input type='hidden' name='idtexte' value='<?php echo $_GET['idtexte']; ?>' />

			<!-- Submit comment -->
			<input onClick="submitCommentaire('action.php?actionid=29&id=<?php echo $_GET['id']; if(isset($_GET['page'])) echo "&page=".$_GET['page']; ?>', 'addcommentaire<?php echo $_GET['idtexte']; ?>', 'tablecommentaire<?php echo $_GET['idtexte']; ?>', <?php echo $_GET['idtexte']; ?>, <?php if(isset($_GET['page'])) echo $_GET['page']; else echo 0; ?>, <?php echo $_GET['id']; ?> );" type='button' value='<?php echo $lang[32]; ?>' />
		</form>
	</td>
</tr>
