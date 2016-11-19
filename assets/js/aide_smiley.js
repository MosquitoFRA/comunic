//Affiche la liste des smiley pour l'aide
function affiche_liste_smile_aide(destination, liste = liste_smile)
{
	var source = "";
	
	//On parcours la liste
	for (var i = 0; i < liste.length; i++) {
		
		source = source + "<tr><td><img src='" + liste[i][1] + "' title='" + liste[i][2] + "' /></td><td>" + liste[i][2] + "</td></tr>";
	}
	
	
	//Enregistrement de la source générée
	document.getElementById(destination).innerHTML = source;
}