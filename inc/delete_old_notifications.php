<?php
	//Sécurité
	if(!isset($delete_old_notifications))
		die("Invalid request.");
	if(!isset($_SESSION['ID']))
		die("Login required.");

?><!DOCTYPE html>
<html>
	<head>
		<title>Suppression des anciennes notifications</title>
		<?php include(pagesRelativePath('common/head.php')); ?>
	</head>
	<body class="metro">
		<?php
			//On vérifie si une demande a été postée
			$ok = false;
			if(isset($_POST['choix']))
			{
				$choix = $_POST['choix'];
				if($choix != "")
					$ok = true; //On valide le choix
			}
			
			if(!$ok)
			{
				?><form action="action.php?actionid=<?php echo $action; ?>" method="post">
					<div class="input-control text" id="datepicker">
						<input type="text" name="choix" placeholder="Cliquez ici" />
					</div>
					<p>Cliquez ci-dessus pour choisir la date avant laquelle les notifications cr&eacute;&eacute;es seront supprim&eacute;es</p>
					<p style="text-align: center"><input type="submit" value="Supprimer" /></p>
						 
					<script>
						$("#datepicker").datepicker();
					</script>
					</form><?php
			}
			else
			{
				//Détermination de la date
				$date = normalise_datepicker($choix);
				
				//On supprime les entrées antérieures à cette date
				$sql = "DELETE FROM notification WHERE date_envoi <= ? AND ID_personne = ?";
				
				//Exécution de la requête
				$requete = $bdd->prepare($sql);
				$requete->execute(array($date, $_SESSION['ID']));
				
				//Message de succès
				?><p class="bg-lighterBlue padding20 fg-white">
					Les notifications ant&eacute;rieures &agrave; cette date ont &eacute;t&eacute; supprim&eacute;es.
				</p><?php
			}
		?>
	</body>
</html>