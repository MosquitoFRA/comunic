<?php
	if(!isset($verification))
		die(); //Sécurité
		
	//Inclusion de la liste des types de fichiers vidéo autorisés
	include('video_type_allowed_list.php');
	
	//Informations sur les fichiers
	//print_r($_FILES); //Développement seleument.
	
	//Vérification de l'existence du fichier
	if(!isset($_FILES["file"]['name']))
		die("Pas de vid&eacute;o post&eacute;e.");
		
	if($_FILES["file"]['error'] != 0)
		die("Une erreur a survenue lors de l'envoi. Merci de r&eacute;essayer");
		
	if(!in_array($_FILES["file"]['type'], $liste_type_video_autorises))
		die("Le type de video envoy&eacute; n'est pas accept&eacute;.");
		
	//Enregistrement de la vidéo
	//Génération du nom et de l'URL
	$nom_video = "mv";
	$nom_video = ($_FILES["file"]['type'] == "video/mp4" ? "mp4" : $nom_video);
	$nom_video = time().sha1($_SESSION['ID']).".".$nom_video;
	$folder_user_video = checkPersonnalFolder(relativeUserDataFolder("video_upload/"), $_SESSION['ID']);
	$url_video = str_replace(relativeUserDataFolder(), '', $folder_user_video.$nom_video);
	
	//Préparation du déplacement de la vidéo
	$fichier_source = $_FILES["file"]['tmp_name'];
	$fichier_destination = relativeUserDataFolder($url_video);
	
	//Copie de la vidéo
	if(move_uploaded_file($fichier_source, $fichier_destination))
		echo "La vid&eacute;o a &eacute;t&eacute; ajout&eacute;e &agrave; la galerie.";
	else
		die("Une erreur est survenue. Veuillez r&eacute;essayer. Fichier: ".$fichier_source." Destination: ".$fichier_destination);
		
	//Enregistrement dans la base de données
	$sql = "INSERT INTO galerie_video (URL, ID_user, nom_video, file_type, size) VALUES (?, ?, ?, ?, ?)";
	
	//Exécution de la requête
	$requete = $bdd->prepare($sql);
	$requete->execute(array($url_video, $_SESSION['ID'], $_FILES["file"]['name'], $_FILES["file"]['type'], $_FILES["file"]['size']));

?>