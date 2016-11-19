//Countdown for Comunic (c) all rights reserved
//Made in 2015

//Fonction de lancement du compteur
function launch_countdown(timestamp_end, id)
{
	setInterval("count_down(" + timestamp_end + ", '" + id + "');", 1000);
}

//Fonction de correction de l'affichage en cas d'un seul chiffre (ajout d'un 0)
function fix_countd(nb)
{
	if(nb < 10)
		return "0" + nb;
	else
		return nb;
}

//Fonction d'exécution du compteur
function count_down(timestamp_end, id)
{
	var days = 24*60*60,
		hours = 60*60,
		minutes = 60;
			
	//On détermine le temps restant
	//Définition des variables
	var now, left, d, h, m, s;
	
	//Temps actuel
	now = new Date() / 1000;
		
	//Temps restant
	left = Math.floor(timestamp_end - now);
		
	//On passe le temps restant à 0 si nécessaire
	if(left < 0){
		left = left*(-1);
    }
		
	// Number of days left
    d = Math.floor(left / days);
    left -= d*days;

    // Number of hours left
    h = Math.floor(left / hours);
    left -= h*hours;

    // Number of minutes left
    m = Math.floor(left / minutes);
    left -= m*minutes;

    // Number of seconds left
    s = left;
	
	//Correction des nombres
	d = fix_countd(d);
	h = fix_countd(h);
	m = fix_countd(m);
	s = fix_countd(s);
	
	//Détermination de l'affichage du compteur
	var afficher = d + ":" + h + ":" + m + ":" + s;
	
	//Affichage du compteur à rebours
	document.getElementById(id).innerHTML = afficher;
}