function loadAmis(file) {
	
	//On v�rifie si le flux est bien autoris�
	if(autoriser_flux_streaming_live == false)
	{
		console.log("La requete d'actualisation de la liste d'amis a ete bloquee en raison de la configuration de la page l'ayant demande.");
		return false;
	}
	
	var xhr = new XMLHttpRequest();

	// R�cup�ration du contenu du fichier
	xhr.open('GET', file);

	//Lorsque l'on est pret...
	xhr.onreadystatechange = function() {
	
		//Compteur du nombre d'erreur de login
		compteur = document.getElementById('internet_error');

		if (xhr.readyState == 4 && xhr.status == 200) {

			document.getElementById('fileAmis').innerHTML =  xhr.responseText;
			
			if(compteur.innerHTML != 0)
			{
				var not = $.Notify({style: {background: 'green', color: 'white'}, content: "C'est bon. La connexion Internet est de retour ! :)"});
				//not.closeAll();
			}
			
			//On remet le compteur des erreurs � 0
			compteur.innerHTML = 0;

		} else if (xhr.readyState == 4 && xhr.status != 200) { 
							
			//document.getElementById('fileAmis').innerHTML = "<?php echo $lang[60]; ?>";
			
			//Message d'erreur
			console.log("Il semblerait qu'il y a un probl�me de connexion Internet. V�rifiez que tous les cables r�seau sont branch�s ou que le Wi-Fi est activ�.");
			
			if(compteur.innerHTML < 3)
				compteur.innerHTML = compteur.innerHTML + 1;
			else
			{
				var not = $.Notify({
					style: {background: 'red', color: 'white'}, 
					content: "Erreur de connexion Internet. Comunic ne parvient plus &agrave; se connecter &agrave; Internet. Veuillez v&eacute;rifier la connexion Internet de votre ordinateur ou essayer de recharger la page.",
					timeout: 10000 // 10 seconds
				});
			}
		}

	}

	xhr.send(null);

}

if(beaucoup_amis == true)
{
	var complement = "&grandavatar";
}
else
{
	var complement = "";
}

if(debut_URL_liste_amis != "undefined")
{
	var debut_URL = debut_URL_liste_amis;
}
else
{
	var debut_URL = "";
}

//Ex�cution de la requete
loadAmis(debut_URL + 'amis.php?ajax' + complement);

//Timer pour actualiser toute les 10 secondes
var last_activity=setInterval("loadAmis('" + debut_URL + "amis.php?ajax" + complement + "')", 10000); // r�p�te toutes les 10s