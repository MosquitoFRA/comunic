//Script d'affichage des commentaires mobile avec une requete ajax
function requeteajaxcommentairesmobiles(file, balisescommentaires) {
				
var xhr = new XMLHttpRequest();

// Récupération du contenu du fichier
 xhr.open('GET', file);

 xhr.onreadystatechange = function() {

 if (xhr.readyState == 4 && xhr.status == 200) {

 document.getElementById(balisescommentaires).innerHTML = xhr.responseText;

 } else if (xhr.readyState == 4 && xhr.status != 200) { 
					
document.getElementById(balisescommentaires).innerHTML =  'Une erreur est survenue. Merci de réessayer ultérieurment. ';

 }

 }

xhr.send(null);

}

function affichecommentaire(file, id)
{
	var pathfile = file + '?id=' + id;
	var balisescommentaires = 'commentaires' + id;
	var commentaires = requeteajaxcommentairesmobiles(pathfile, balisescommentaires);	 
}