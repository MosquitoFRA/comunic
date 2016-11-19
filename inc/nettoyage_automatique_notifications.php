<?php
	//Sécurité
	if(!isset($_SESSION['ID']))
		die("Login required!");
		
	//On vérifie si il faut changer les paramètres
	if(isset($_POST['mois_nettoyage_automatique_notifications']) AND isset($_POST['jour_nettoyage_automatique_notifications']) AND isset($_POST['heure_nettoyage_automatique_notifications']))
	{
		//On vérifie si il faut activer ou non le système
		$activer_systeme = (isset($_POST['nettoyage_automatique_notifications']) ? 1 : 0);
		
		//On contrôle les valeurs données
		//Préparation des données
		$mois_nettoyage = $_POST['mois_nettoyage_automatique_notifications']*1;
		$jour_nettoyage = $_POST['jour_nettoyage_automatique_notifications']*1;
		$heure_nettoyage = $_POST['heure_nettoyage_automatique_notifications']*1;
		
		//Vérification des valeurs
		if(($mois_nettoyage > -1 AND $mois_nettoyage <= 12) AND ($jour_nettoyage > -1 AND $jour_nettoyage <= 31) AND ($heure_nettoyage > -1 AND $heure_nettoyage <= 24))
		{
			//On peut mettre à jour la base de données
			$sql = "UPDATE utilisateurs SET nettoyage_automatique_notifications = ?, mois_nettoyage_automatique_notifications = ?, jour_nettoyage_automatique_notifications = ?, heure_nettoyage_automatique_notifications = ? WHERE ID = ?";
			
			//Exécution de la requête
			$update = $bdd->prepare($sql);
			$update->execute(array($activer_systeme, $mois_nettoyage, $jour_nettoyage, $heure_nettoyage, $_SESSION['ID']));
			
			//Rechargement des informations
			$afficher = cherchenomprenom($_SESSION['ID'], $bdd);
			
			//Affichage d'un message de succès
			?><script type="text/javascript">affiche_notification_succes("Les param&egrave;tres de suppression des anciennes notifications ont &eacute;t&eacute; mis &agrave; jour.", "", 5);</script><?php
		}
		else
		{
			//Message d'erreur
			?><script type="text/javascript">affiche_notification_erreur("Les valeurs saisies pour la suppression automatique des anciennes notifications sont incorrectes.", "Erreur", 5);</script><?php
		}
		
	}
	
?><div class="nettoyage_automatique_notifications">
	<p><b>Nettoyage automatique des notifications :</b></p>
	<form action="parametres.php?c=<?php echo $_GET['c']; ?>" method="post">
		<label><input type='checkbox' name='nettoyage_automatique_notifications' <?php if($afficher['nettoyage_automatique_notifications'] == 1) echo 'checked'; ?> /> Activer le nettoyage automatique des anciennes notifications </label>
		<p>Supprimer automatiquement les notifications ayant plus de ...</p>
		<p>
			<input type="text" size="2" required maxlength="2" value="<?php echo $afficher['mois_nettoyage_automatique_notifications']; ?>" name="mois_nettoyage_automatique_notifications" /> mois(s),
			<input type="text" size="2" required maxlength="2" value="<?php echo $afficher['jour_nettoyage_automatique_notifications']; ?>" name="jour_nettoyage_automatique_notifications" /> jour(s) et
			<input type="text" size="2" required maxlength="2" value="<?php echo $afficher['heure_nettoyage_automatique_notifications']; ?>" name="heure_nettoyage_automatique_notifications" /> heure(s)
		</p>
		<p><input type="submit" value="Enregistrer les modifications" />
		<p>Note : Suivant les param&egrave;tres que vous avez choisi les anciennes notifications seront supprim&eacute;es lors de votre connexion au service.</p>
	</form>
</div>