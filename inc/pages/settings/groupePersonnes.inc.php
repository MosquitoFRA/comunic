<?php
/**
 * Change groups settings
 *
 * @author Pierre HUBERT
 */

if(!isset($_SESSION))
	exit("Invalid call !");

//On vérifie si il faut ajouter (ou modifier) un groupe
if(isset($_POST['nom_groupe']) AND isset($_POST['choixpersonne']))
{
	if($_POST['nom_groupe'] != "")
	{
		if(is_array($_POST['choixpersonne']))
		{
			if(count($_POST['choixpersonne'] > 0))
			{
				//On enregistre le groupe
				$nom_groupe = $_POST['nom_groupe'];
				
				//Listing de toute les personnes
				$liste_id = array();
				foreach($_POST['choixpersonne'] as $id=>$value)
					$liste_id[]= $id;
				$liste_id = implode("|", $liste_id);
				
				if(!isset($_POST['edit']))
				{
					//Enregistrement dans la base de données
					$sql = "INSERT INTO groupe_personnes (ID_personne, nom, date_ajout, liste_ID) VALUES (?, ?, NOW(), ?)";
					$insertion = $bdd->prepare($sql);
					$insertion->execute(array($_SESSION['ID'], $nom_groupe, $liste_id));
					
					//Message de succès
					?><script type="text/javascript">affiche_notification_succes("Le groupe <?php echo $nom_groupe; ?> vient d'&ecirc;tre enregistr&eacute; avec succ&egrave;s.");</script><?php
				}
				else
				{
					$edit = $_POST['edit']*1;
					if($edit > 0)
					{
						//Modification de la base de données
						$sql = "UPDATE groupe_personnes SET nom = ?, liste_ID = ? WHERE ID_personne = ? AND ID = ?";
						$insertion = $bdd->prepare($sql);
						$insertion->execute(array($nom_groupe, $liste_id, $_SESSION['ID'], $edit));
						
						//Message de succès
						?><script type="text/javascript">affiche_notification_succes("Le groupe <?php echo $nom_groupe; ?> vient d'&ecirc;tre modifi&eacute; avec succ&egrave;s.");</script><?php
					}
				}
			}
		}
	}
}

?><h3>Groupe de personnes</h3>
<div class="metro gestion_groupe_personnes"><?php

//Ajout (ou modification) d'un groupe de personnes
//Recherche des information sur le groupe (si modification)
$nom_groupe = "";
$liste_amis_checked = array();
if(isset($_GET['edit']))
{
	$sql = "SELECT * FROM groupe_personnes WHERE ID = ? AND ID_personne = ?";
	$requete = $bdd->prepare($sql);
	$requete->execute(array($_GET['edit']*1, $_SESSION['ID']));
	
	//Enregistrement du résultat
	if(!$info_groupe = $requete->fetch())
	{
		unset($_GET['edit']);
	}
	else
	{
		//Préparation du traitement
		$nom_groupe = $info_groupe['nom'];
		$liste_amis_checked = explode('|', $info_groupe['liste_ID']);
	}
	
	//Fermeture de la requête
	$requete->closeCursor();
}

//Titre du formulaire
if(!isset($info_groupe['ID'])) { 
	?><p>Ajout d'un nouveau groupe :</p><?php 
} else { 
	?><p>Modification du groupe :</p><?php 
}

//Ouverture du formulaire
?><form action="<?php echo $_SERVER['PHP_SELF']; ?>?c=<?php echo $_GET['c']; ?>" method="post">
	<div class="input-control text">
		<input type="text" name="nom_groupe" value="<?php echo $nom_groupe; ?>" placeholder="Nom du groupe" required />
	</div><?php
	
//Si nécessaire, on indique qu'il s'agit d'une modification de groupe
if(isset($info_groupe['ID'])) echo "<input type='hidden' name='edit' value='".$info_groupe['ID']."' />";
	
//Listing des amis
$liste_amis = liste_amis($_SESSION['ID'], $bdd, 1);

//Affichage de la liste en tant que formulaire
foreach($liste_amis as $afficher_liste)
{
	//Récupération des informations si nécessaire
	$id_amis = $afficher_liste;
	if(!isset($info_users[$id_amis]['table_utilisateurs']) OR !isset($info_users[$id_amis]['avatar_32_32']))
	{
		$info_users[$id_amis]['table_utilisateurs'] = cherchenomprenom($id_amis, $bdd);
		$info_users[$id_amis]['avatar_32_32'] = avatar($id_amis, "./", 32, 32);
	}
	
	//Affichage de la proposition
	?><div class="input-control checkbox">
		<label>
			<input type="checkbox" <?php if(in_array($id_amis, $liste_amis_checked)) echo "checked"; ?> name="choixpersonne[<?php echo $id_amis; ?>]" />
			<span class="check"></span>
			<?php echo $info_users[$id_amis]['avatar_32_32']." ".$info_users[$id_amis]['table_utilisateurs']['prenom']." ".$info_users[$id_amis]['table_utilisateurs']['nom']; ?>&nbsp;
		</label>
	</div><?php
}

//Fin du formulaire
?><br /><input type="submit" value="<?php if(!isset($info_groupe['ID'])) echo "Ajouter"; else echo "Modifier"; ?> le groupe" /><?php
?></form><?php

//Listing des groupes de personnes
$liste = list_groupes_personnes($_SESSION['ID'], $bdd);

//Affichage de la liste
foreach($liste as $afficher)
{
	//On vérifie si il faut supprimer le groupe
	if(isset($_GET['delete_groupe']))
	{
		//Suppression du groupe
		$groupe = $_GET['delete_groupe']*1;
		
		if($groupe == $afficher['ID'])
		{
			//Requête de suppression
			$sql = "DELETE FROM groupe_personnes WHERE ID_personne = ? AND ID = ?";
			$delete = $bdd->prepare($sql);
			$delete->execute(array($_SESSION['ID'], $groupe));
			
			//On saute le groupe
			continue;
		}
	}
	
	//Affichage du nom du groupe
	echo "<h3>".$afficher['nom']." <a href='".$_SERVER['PHP_SELF']."?c=".$_GET['c']."&edit=".$afficher['ID']."'><img src='".path_img_asset("edit.png")."' title='Modifier le groupe' /></a>";
	echo "<small class='on_right'><a href='#' onClick='confirmaction(\"".$_SERVER['PHP_SELF']."?c=".$_GET['c']."&delete_groupe=".$afficher['ID']."\", \"Voulez-vous vraiment supprimer ce groupe ?\");'> Supprimer le groupe </a></small></h3>";
	
	//Récupération de la liste des personnes
	$liste_personnes = explode("|", $afficher['liste_ID']);
	
	foreach($liste_personnes as $nom_personnes)
	{
		$id_amis = $nom_personnes;
		if(!isset($info_users[$id_amis]['table_utilisateurs']) OR !isset($info_users[$id_amis]['avatar_32_32']))
		{
			$info_users[$id_amis]['table_utilisateurs'] = cherchenomprenom($id_amis, $bdd);
			$info_users[$id_amis]['avatar_32_32'] = avatar($id_amis, "./", 32, 32);
		}
		
		echo "<span>".$info_users[$id_amis]['avatar_32_32']." ".$info_users[$id_amis]['table_utilisateurs']['prenom']." ".$info_users[$id_amis]['table_utilisateurs']['nom']."&nbsp;</span>";
	}
}