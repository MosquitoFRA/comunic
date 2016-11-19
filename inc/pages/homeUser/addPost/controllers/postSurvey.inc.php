<?php
/**
 * Post a survey
 *
 *	@author Pierre HUBERT
 */

isset($_SESSION) OR exit('Invalid call - '.$_SERVER['PHP_SELF']);

//Enregistrement des variables
$reponses_sondage = bloquebalise($_POST['reponses_sondage']);
$question_sondage = bloquebalise($_POST['question_sondage']);
$commentaire_sondage = $_POST['commentaire_sondage'];

//Choix par défaut pour les réponses au sondage si il n'y en a aucune
if(str_replace("\n", "", $reponses_sondage) == "")
	$reponses_sondage = "None";

//On commence par enregistrer le texte dans la bdd
if($_SESSION['ID'] == $idPersonn)
	ajouttexte($_SESSION['ID'], $commentaire_sondage, $bdd, $niveau_visibilite, "sondage");
else //Si c'est un amis
	ajouttexte_amis($_SESSION['ID'], $idPersonn, $commentaire_sondage, $bdd, $niveau_visibilite, "sondage");

//Récupération des informations sur le texte précédemment posté
$infos_texte = select_sql("texte", "texte = ? AND (ID_personne = ? OR ID_amis = ?) AND niveau_visibilite = ? ORDER BY ID DESC", $bdd, array(
																																			$commentaire_sondage, 
																																			$_SESSION['ID'], 
																																			$_SESSION['ID'], 
																																			$niveau_visibilite
));

//On vérifie que l'on a bien notre texte
if(count($infos_texte) == 0)
{
	//Rapport d'erreur à l'administration
	report_error('if(count($infos_texte) == 0)', 'La variable $infos_texte ne compte aucune entr&eacute;e, alors qu\'elle devrait en contenir au moins une (permet l\'ajout de sondages) dans addpost.php (inc).');
	
	//Affichage d'un message d'erreur
	affiche_message_erreur("Nous avons rencontr&eacute; une erreur en interne. (Err Sondage Inser 1) L'administration a &eacute;t&eacute; inform&eacute;e de cette erreur. Nous ferons en sorte de la r&eacute;parer au plus vite, veuillez r&eacute;ssayer d'envoyer votre sondage.");
}
else
{
	//Poursuite
	
	//Récupération de l'ID du sondage
	$idPersonn_texte = $infos_texte[0]['ID'];
	
	//Création du sondage
	insert_sql("sondage", "ID_utilisateurs, ID_texte, date_creation, question", "?, ?, NOW(), ?", $bdd, array($_SESSION['ID'], $idPersonn_texte, $question_sondage));
	
	$infos_sondage = select_sql("sondage", "ID_utilisateurs = ? AND ID_texte = ? AND question = ? ORDER BY ID DESC", $bdd, array(
																													$_SESSION['ID'], 
																													$idPersonn_texte, 
																													$question_sondage 
	));
	
	if(count($infos_sondage) == 0)
	{
		//Rapport d'erreur à l'administration
		report_error('if(count($infos_sondage) == 0)', 'La variable $infos_sondage ne compte aucune entr&eacute;e, alors qu\'elle devrait en contenir au moins une (permet l\'ajout de sondages) dans addpost.php (inc).');
		
		//Affichage d'un message d'erreur
		affiche_message_erreur("Nous avons rencontr&eacute; une erreur en interne. (Err Sondage Inser 2) L'administration a &eacute;t&eacute; inform&eacute;e de cette erreur. Nous ferons en sorte de la r&eacute;parer au plus vite, veuillez r&eacute;ssayer d'envoyer votre sondage.");
		
		exit();
	}
	
	//Récupération de l'ID du sondage
	$idPersonn_sondage = $infos_sondage[0]['ID'];
	
	//Ajout des réponses au sondage
	$array_reponses = explode("\n", $reponses_sondage);
	foreach($array_reponses as $traiter)
	{
		//Vérification de la réponse
		if($traiter != "" AND $traiter != " ")
		{
			//Ajout du choix à la BDD
			insert_sql("sondage_choix", "ID_sondage, date_creation, choix", "?, NOW(), ?", $bdd, array($idPersonn_sondage, $traiter));
		}
		
	}
	
	//Message de succès
	?><script>affiche_notification_succes("Le sondage a bien &eacute;t&eacute; enregistr&eacute;.");</script><?php
}