//Fonction de recherche de commentaires (n�cessaire pour le post de commentaire
function requeteajaxcommentairesmobiles(file, balisescommentaires) {
				
var xhr = new XMLHttpRequest();

// R�cup�ration du contenu du fichier
 xhr.open('GET', file);

 xhr.onreadystatechange = function() {

 if (xhr.readyState == 4 && xhr.status == 200) {

 document.getElementById(balisescommentaires).innerHTML = xhr.responseText;

 } else if (xhr.readyState == 4 && xhr.status != 200) { 
					
document.getElementById(balisescommentaires).innerHTML =  'Une erreur est survenue. Merci de r�essayer ult�rieurment. ';

 }

 }

xhr.send(null);

}

//Fonction de post de commentaires
function submitCommentaire(destination, idcommentaireform, iddestination, idtexte, page, iduser)
{
	var commentaire = encodeURIComponent(document.getElementById(idcommentaireform).value);
	document.getElementById(idcommentaireform).value = ""; //Commentaire (forumlaire) vid�

	//var touscommentaires = document.getElementById(iddestination);
	
	if(commentaire == "")
	{
		affiche_notification_erreur("L'ajout de commentaires vides est interdit !", "Erreur", 5);
	}
	else
	{
		//Nouvel affichage des commentaires
		//touscommentaires = touscommentaires + "<tr><td>Vous</td><td>" + commentaire + "</td></tr>";
		document.getElementById(iddestination).innerHTML = "<tr><td>Veuillez patienter, chargement en cours...</td></tr>";;
		
		if(page == 0)
		{
			//Pr�parationn de la recherche des nouveaux ccommentaires
			filegetcommentaire = "commentaire.php?idtexte=" + idtexte + "&id=" + iduser;
		}
		else
		{
			//Pr�parationn de la recherche des nouveaux ccommentaires
			filegetcommentaire = "commentaire.php?idtexte="+idtexte+"&page="+page+ "&id=" + iduser;
		}
		
		//Requ�te AJAX
		var xhr = getXMLHttpRequest();
		xhr.onreadystatechange = function() {
				if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) {
					affiche_notification_succes("Commentaire enregistr�.", "");
					requeteajaxcommentairesmobiles(filegetcommentaire, iddestination); //Affiche tous les commentaires
				}
		};

		xhr.open("POST", destination, true);
		xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		xhr.send("commentaire="+commentaire+"&idtexte="+idtexte);
	}
}