<?php
	//Sécurité
	if(!isset($_SESSION['ID']))
		die("Login required.");
	if(!isset($ok_for_recent_private_chat))
		die("404 File not found.");

	//Recherche des 5 dernières conversations dans la base de données
	$array_last_conversations = array();
	$supp_sql = "ID_destination != 0 ";
	for($i = 0; $i < 5; $i++)
	{
		//Sélection SQL
		$result_sql = select_sql("chatprive", "(ID_personne = ? OR ID_destination = ?) AND (".$supp_sql.") ORDER BY date_envoi DESC LIMIT 1", $bdd, $tableau_valeurs = array($_SESSION['ID'], $_SESSION['ID']));
		
		//On vérifie si il reste des entrées
		if(count($result_sql) == 0)
			break;
		
		//Détermination de l'ID de la personne distante
		$temp_id_personne = ($result_sql[0]['ID_personne'] == $_SESSION['ID'] ? $result_sql[0]['ID_destination'] : $result_sql[0]['ID_personne']);
		
		//Enregistrement des informations
		$array_last_conversations[$temp_id_personne] = array(
			'ID_personne' => $temp_id_personne, 
			'message' => $result_sql[0]['contenu'], 
			'vu' => ($result_sql[0]['ID_personne'] == $_SESSION['ID'] ? 1 : $result_sql[0]['vu']), 
			'date' => $result_sql[0]['date_envoi']
		);
		
		//Rajout de la condition SQL
		$supp_sql .= "AND ID_destination != ".$temp_id_personne." AND ID_personne != ".$temp_id_personne." ";
	}
?><!DOCTYPE html>
<html>
	<head>
		<title>Conversations r&eacute;centes du chat priv&eacute;</title>
		<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
		
		<!-- Appel des feuilles de style -->
		<?php echo code_inc_css(path_css_asset('global.php/'.$last_update_website)); ?>
		
		<style type="text/css">
			.conversations_contener {
				text-align: center;
			}
			
			.a_conversation {
				padding-top: 15px;
			}
			
			.open_conversation {
				width: 98% !important;
				margin: auto;
			}
			
			.a_conversation small {
				font-size: 70% !important;
			}
		</style>
	</head>
	<body class="metro">
	
		<!-- Affichage de la liste des conversations -->
		<?php
		if(count($array_last_conversations) == 0)
			echo "<p>Vous n'avez aucune conversation r&eacute;cente &agrave; afficher</p>";
		else
		{
			echo "<div class='conversations_contener'>";
			
			foreach($array_last_conversations as $afficher)
			{

				?><div class="a_conversation">
					<a class="open_conversation command-button" href="<?php echo $urlsite; ?>action.php?actionid=19&id=<?php echo $afficher['ID_personne']; ?>&autoredirect">
						<i class="icon-<?php echo($afficher['vu'] == 1 ? "comments-2" : "new"); ?> on-left"></i>
						<?php echo corrige_caracteres_speciaux(return_nom_prenom_user($afficher['ID_personne'], $bdd)); ?>
						<small><?php echo corrige_caracteres_speciaux(affiche_smile(bloquebalise(wordwrap(str_replace(')', ') ', $afficher['message']), 30, " ", true)), $urlsite)); ?><br /><i><?php echo adapte_date($afficher['date']); ?></i></small>
					</a>
				</div><?php
			}
			
			echo "</div>";
		}
		?>
	</body>
</html>