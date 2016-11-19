//Fonctions des groupes
// (c) Comunic 2015

//Fonction d'affichage du menu de choix de groupe
function affiche_formulaire_groupes(input_choice)
{
	parent = input_choice.parentNode.parentNode.parentNode.parentNode;
	
	//On vérifie si un formulaire de changement de groupe a déjà été proposé
	if(parent.innerHTML.indexOf('choix_groupe') != -1)
	{
		//
	}
	else
	{
		//Ajout du formulaire
		var formulaire = document.createElement('div');
		formulaire.innerHTML = "<div class='choix_groupe'>Choix des groupes : <a href='parametres.php?c=groupe_personnes'><img src='img/edit.png' class='img_change_groupes' /></a>";
		
		//Parcous de la liste
		//Parcours du tableau
		for (var i = 0, div ; i < liste_groupes.length ; i++) {
			//On vérifie que le résultat est correct
			if(liste_groupes[i].length == 2)
			{
				formulaire.innerHTML = formulaire.innerHTML + "<label><input type='checkbox' name='liste_groupes[" + liste_groupes[i][0] + "]' />" + liste_groupes[i][1] + "</label>";
			}
		}
		formulaire.innerHMTL =+ "</div>";
		parent.appendChild(formulaire);

	}
}