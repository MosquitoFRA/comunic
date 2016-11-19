<?php
/**
 * SQL functions
 *
 * @author Pierre HUBERT
 */

//Fonction de récupération d'une ou plusieurs entrées dans la base de données
function select_sql($table, $conditions, $bdd, $tableau_valeurs = array())
{
	//Requête SQL de recherche
	$sql = "SELECT * FROM ".$table.($conditions != "" ? " WHERE ".$conditions : "");
	if(!$requete = $bdd->prepare($sql))
	{
		//Envoi d'un rapport d'erreur
		report_sql($sql, 'Un probl&egrave;me ind&eacute;termin&eacute; a survenu. Impossible d\'ex&eacute;cuter <i>if(!$requete = $bdd->prepare($sql))</i>');
		
		die('Un probl&egrave;me ind&eacute;termin&eacute; a survenu. Merci de r&eacute;essayer en rechargeant la page. (F5)');
	}
	if(!$requete->execute($tableau_valeurs))
	{
		//Envoi d'un rapport d'erreur
		report_sql($sql, 'Une erreur a survenue lors de la consultation de la base de donn&eacute;es. Impossible d\'ex&eacute;cuter <i>if(!$requete->execute($tableau_valeurs))</i>');
		
		die(echo_erreur('Une erreur a survenue lors de la consultation de la base de donn&eacute;es. Merci de r&eacute;essayer en rechargeant la page.'));
	}
	
	//Enregistrement des résultats
	$liste = array();
	while($enregistrer = $requete->fetch())
		$liste[] = $enregistrer;
	
	//Fermeture de la requête
	$requete->closeCursor();
	
	//Affichage de la requête SQL ( en mode debug SEULEMENT !!! )
	//echo $sql."<br />";
	
	//Renvoi du résultat
	return $liste;
}

//Fonction de comptage d'une ou plusieurs entrées dans la base de données
function count_sql($table, $conditions, $bdd, $tableau_valeurs = array())
{
	//Requête SQL de recherche
	$sql = "SELECT COUNT(*) AS count FROM ".$table.($conditions != "" ? " WHERE ".$conditions : "");
	if(!$requete = $bdd->prepare($sql))
	{
		//Envoi d'un rapport d'erreur
		report_sql($sql, 'Un probl&egrave;me ind&eacute;termin&eacute; a survenu. Impossible d\'ex&eacute;cuter <i>if(!$requete = $bdd->prepare($sql))</i>');
		
		die('Un probl&egrave;me ind&eacute;termin&eacute; a survenu. Merci de r&eacute;essayer en rechargeant la page. (F5)');
	}
	if(!$requete->execute($tableau_valeurs))
	{
		//Envoi d'un rapport d'erreur
		report_sql($sql, 'Une erreur a survenue lors de la consultation de la base de donn&eacute;es (pour compter des entr&eacute;es). Impossible d\'ex&eacute;cuter <i>if(!$requete->execute($tableau_valeurs))</i>');
		
		die(echo_erreur('Une erreur a survenue lors de la consultation de la base de donn&eacute;es. Merci de r&eacute;essayer en rechargeant la page.'));
	}
	
	//Enregistrement du résultat
	if(!$resultat = $requete->fetch())
	{
		//Envoi d'un rapport d'erreur
		report_sql($sql, 'Une erreur a survenue lors de la consultation de la base de donn&eacute;es (pour compter des entr&eacute;es). Impossible d\'ex&eacute;cuter <i>if(!$resultat = $requete->fetch())</i>');
		
		die(echo_erreur('Une erreur a survenue lors de la consultation de la base de donn&eacute;es. Merci de r&eacute;essayer en rechargeant la page.'));
	}
	
	if(!isset($resultat['count']))
	{
		//Envoi d'un rapport d'erreur
		report_sql($sql, 'Une erreur a survenue lors de la consultation de la base de donn&eacute;es (pour compter des entr&eacute;es). <i>Retour incorrect de la base de donn&eacute;es. (Absence de $resultat["count"])</i>');
		
		die(echo_erreur('Une erreur a survenue lors de la consultation de la base de donn&eacute;es. Merci de r&eacute;essayer en rechargeant la page.'));
	}
	
	//Fermeture de la requête
	$requete->closeCursor();
	
	//Renvoi du résultat
	return $resultat['count'];
}

//Fonction d'ajout d'une entrée dans la base de données
function insert_sql($table, $nom_valeur, $valeurs, $bdd, $tableau_valeurs = array())
{
	//Fonction d'insertion dans la base de données
	$sql = "INSERT INTO ".$table." (".$nom_valeur.") VALUES (".$valeurs.")";
	$requete = $bdd->prepare($sql);
	if(!$requete->execute($tableau_valeurs))
	{
		//Envoi d'un rapport d'erreur
		report_sql($sql, 'L\'enregistrement a &eacute;chou&eacute;');
		
		die('L\'enregistrement a &eacute;chou&eacute;');
	}
}

//Fonction de modification d'une entrée dans la base de données
function update_sql($table, $modifications, $conditions, $bdd, $tableau_valeurs = array())
{
	//Requête SQL de mise à jour
	$sql = "UPDATE ".$table." SET ".$modifications." WHERE ".$conditions;
	if(!$requete = $bdd->prepare($sql))
	{
		//Envoi d'un rapport d'erreur
		report_sql($sql, 'Un probl&egrave;me ind&eacute;termin&eacute; a survenu. Impossible d\'ex&eacute;cuter <i>$requete = $bdd->prepare($sql);</i>');
		
		die('Un probl&egrave;me ind&eacute;termin&eacute; a survenu. Merci de r&eacute;essayer en rechargeant la page. (F5)');
	}
	if(!$requete->execute($tableau_valeurs))
	{
		//Envoi d'un rapport d'erreur
		report_sql($sql, 'Une erreur a survenue lors de la mise &agrave; jour de la base de donn&eacute;es. Impossible d\'executer la requ&ecirc;te.');
		
		die(echo_erreur('Une erreur a survenue lors de la mise &agrave; jour de la base de donn&eacute;es. Merci de r&eacute;essayer en rechargeant la page.'));
	}
}

//Fonction de suppression d'enregistrements dans la base de données
function delete_sql($table, $conditions, $bdd, $tableau_valeurs = array())
{
	//Requête SQL de suppression
	$sql = "DELETE FROM ".$table." WHERE ".$conditions;
	if(!$requete = $bdd->prepare($sql))
	{
		//Envoi d'un rapport d'erreur
		report_sql($sql, 'Un probl&egrave;me ind&eacute;termin&eacute; a survenu. Impossible d\'ex&eacute;cuter <i>$requete = $bdd->prepare($sql);</i>');
		
		die('Un probl&egrave;me ind&eacute;termin&eacute; a survenu. Merci de r&eacute;essayer en rechargeant la page. (F5)');
	}
	if(!$requete->execute($tableau_valeurs))
	{
		//Envoi d'un rapport d'erreur
		report_sql($sql, 'Une erreur a survenue lors de la suppression d\'entr&eacute;es de la base de donn&eacute;es. Impossible d\'ex&eacute;cuter la requ&ecirc;te.');
		
		die(echo_erreur('Une erreur a survenue lors de la suppression d\'entr&eacute;es de la base de donn&eacute;es. Merci de r&eacute;essayer en rechargeant la page.'));
	}
}

//Fonction permettant de rapporter une erreur dans l'exécution d'un script SQL
function report_sql($requete_sql, $raison = "La raison n'a pas &eacute;t&eacute; sp&eacute;cifi&eacute;e.")
{
	//Inclusion de la configuration
	include('inc/config/config.php');
	
	
	//Envoi du message
	//Vérification de l'autorisation d'envoi de mails
	if($active_envoi_mail == "oui")
	{
		//Envoi du message
		$send_mail = true;
		$sujet = "[SQL Comunic] Erreur dans une requête SQL"; //Sujet
		$description_rapide = "Une erreure fatale est arrivee dans une requete pour Comunic.";
		$nom_destinataire = "Dev de Comunic";
		$adresse_mail_destinataire = $mail_envoi_erreur_sql;
		$message = "<h2 style='text-align: center'>Erreur dans la requ&ecirc;te SQL</h2>
		<p>Bonjour, ce message vous a &eacute;t&eacute; adress&eacute; suite &agrave; une erreur fatale dans un script SQL. La requ&ecirc;te SQL est la suivante : <i>".$requete_sql."</i></p>
		<p>La raison de l'erreur est la suivante: ".$raison."</p>
		<p>Les informations compl&eacute;mentaires sont les suivantes :</p>
		<table>
			<tr><td><b>$"."_POST</b></td></tr>";
				//Parcours de la variable $_POST
				foreach($_POST as $nom=>$valeur)
					$message.= "<tr><td>".$nom." :</td><td>".$valeur."</td></tr>";
					
			$message .= "<tr><td><b>$"."_GET</b></td></tr>";
				//Parcours de la variable $_GET
				foreach($_GET as $nom=>$valeur)
					$message.= "<tr><td>".$nom." :</td><td>".$valeur."</td></tr>";
					
			$message .= "<tr><td><b>$"."_SESSION</b></td></tr>";
				//Parcours de la variable $_SESSION
				foreach($_SESSION as $nom=>$valeur)
					$message.= "<tr><td>".$nom." :</td><td>".$valeur."</td></tr>";
				
			$message .= "<tr><td><b>$"."_SERVER</b></td></tr>";
				//Parcours de la variable $_SERVER
				foreach($_SERVER as $nom=>$valeur)
					$message.= "<tr><td>".$nom." :</td><td>".$valeur."</td></tr>";
		$message .= "</table>"; //Message
		$texte_message = $message;
		
		//Envoi du message
		include(websiteRelativePath('inc/envoi_mail.php'));
		
		echo "<!-- MailAdmin sent -->";
	}
	
}