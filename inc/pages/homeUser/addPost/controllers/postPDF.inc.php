<?php
/**
 * Post a PDF file
 *
 *	@author Pierre HUBERT
 */

isset($_SESSION) OR exit('Invalid call - '.$_SERVER['PHP_SELF']);

//Enregistrement du texte dans une variable
$texte = $_POST['texte_post_with_pdf'];

if(isset($_FILES['fichier_pdf']))
{
	//Enregistrement des informations du PDF dans une variable
	$infos_pdf = $_FILES['fichier_pdf'];
	
	//Contrôle de la présence d'une erreur
	if($infos_pdf['error'] != 0)
	{
		//Une erreur a survenue lors de l'envoi
		$erreur_ajout_pdf = "Une erreur a survenue lors de l'envoi du PDF. Merci de r&eacute;ssayer.";
	}
	elseif($infos_pdf['type'] != "application/pdf")
	{
		//Le fichier choisi n'est pas un PDF
		$erreur_ajout_pdf = "Le fichier envoy&eacute; n'est pas un PDF.";
	}
	else
	{
		//Détermination du nom du pdf
		$nom_pdf = sha1(time().$_SESSION['ID'].$texte.$_SERVER['REMOTE_ADDR']);
		
		while(file_exists(relativeUserDataFolder("post_pdf/".$nom_pdf.".pdf")))
		{
			$nom_pdf = crypt($nom_pdf);
		}
		
		//On vérifie si un dossier a été allouée à la personne, création automatique le cas échéant
		checkPersonnalFolder(relativeUserDataFolder("post_pdf/"), $_SESSION['ID']);
		$folder_user_pdf = "post_pdf/".$_SESSION['ID']."/";
		
		//On finit le nom du PDF
		$nom_pdf = $folder_user_pdf.$nom_pdf.".pdf";
		
		//Copie du PDF
		if(move_uploaded_file($infos_pdf['tmp_name'], relativeUserDataFolder($nom_pdf)))
		{
			//Ajout du texte
			if($_SESSION['ID'] == $idPersonn)
				ajouttexte($_SESSION['ID'], $texte, $bdd, $niveau_visibilite, "pdf", $nom_pdf);
			else //Si c'est un amis
				ajouttexte_amis($_SESSION['ID'], $idPersonn, $texte, $bdd, $niveau_visibilite, "pdf", $nom_pdf);
		}
		else
		{
			//Il y a une erreur lors de la copie
			$erreur_ajout_pdf = "Une erreur a survenue lors de la copie du PDF vers son emplacement d&eacute;finitif. Merci de r&eacute;ssayer.";
		}
	}
	
	//Si il y a une erreur, on l'affiche
	if(isset($erreur_ajout_pdf))
	{
		?><script>affiche_notification_erreur("<?php echo $erreur_ajout_pdf; ?>");</script><?php
	}
	else
	{
		//Sinon on affiche un message de succès
		?><script>affiche_notification_succes("Le PDF a bien &eacute;t&eacute; enregistr&eacute;.");</script><?php
	}
}