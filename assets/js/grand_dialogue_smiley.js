//Fichier de gestion de l'affichage de la liste de propostions de smiley pour le chat privé entre autre...
//Tous droits réservés à Pierre HUBERT, créateur de Comunic. Ce fichier a été créé en avril 2015

//Ouverture de la boîte de dialogues
function ouvre_grande_liste_smiley(id)
{
	dialogue = document.createElement("div");
	dialogue.id = "grande_boite_dialogues_smiley";
	dialogue.innerHTML = "";
	
	//On parcours la liste
	var source = "<input type='button' value='Fermer' class='close_grand_dialogue_smiley' onClick='this.parentNode.id=\"none\"; this.parentNode.style.display=\"none\"; this.parentNode.style.position=\"absolute\";' />";
	for (var i = 0; i < liste_smile.length; i++) {
	
		source = source + "<div><img onClick='" + 'document.getElementById("' + id + '").innerHTML = document.getElementById("' + id + '").innerHTML + "'+liste_smile[i][0]+'"' + "' src='" + liste_smile[i][1] + "' title='" + liste_smile[i][2] + "' /></div>";
	}
	
	dialogue.innerHTML = dialogue.innerHTML + source;
	
	document.body.appendChild(dialogue);
}