// Script de gestion du panneau du chat privé
// (c) Service Pierre 					 2015

//Fonction permettant d'afficher ou de masquer le chat privé
function show_hide_panneau_private_chat(show)
{
	//On masque le panneau
	if(show == 0)
	{
		show_hide_id("private_chat_contener", "hidden");
		show_hide_id("private_chat_open_button", "visible");
		
		//On vide son contenu
		document.getElementById('conversations_private_chat').innerHTML = "";
	}
	//On affiche le panneau
	else
	{
		show_hide_id("private_chat_contener", "visible");
		show_hide_id("private_chat_open_button", "hidden");
	}
		
	//Requête ajax pour enregistrer le choix
	ajax_rapide("action.php?actionid=17&show=" + show);
}

//Fonction permettant de changer la taille des fenêtres de chat
function up_down_panneau_private_chat(type) 
{
	//On récupère la valeur actuelle de la hauteur
	var hauteur = parseInt(document.getElementById("private_chat_contener").style.height);
	
	//On la modifie
	if(type == "up")
		hauteur = hauteur + 50;
	else
		hauteur = hauteur - 50;
	
	//Adaptation de la hauteur
	if(hauteur < 20)
	{
		hauteur = 20
	}
	
	
	//Adaptation de la hauteur
	if(hauteur > 220)
	{
		hauteur = 220
	}
	
	//Vérification de la hauteur
	if(hauteur > 20 && hauteur < 220)
	{
		//Message en cas de hauteur de 100px
		if(hauteur == 20)
		{
			//Message
			affiche_notification("Vous avez atteint la hauteur minimale.", "Note");
		}
		if(hauteur == 220)
		{
			//Message
			affiche_notification("Vous avez atteint la hauteur maximale.", "Note");
		}
		
		//Application de la nouvelle hauteur
		document.getElementById("private_chat_contener").style.height = hauteur + "px";
		document.getElementById("conversations_private_chat").style.height = hauteur + "px";
		
		//Envoi d'une requête ajax pour enregistrer la nouvelle hauteur
		ajax_rapide("action.php?actionid=18&size=" + hauteur);
	}
	else
	{
		//Message d'erreur
		affiche_notification_erreur("La hauteur choisie est trop faible ou trop &eacute;lev&eacute;e.", "Erreur");
	}
}

//Fonction permettan d'ouvrir une conversation dans le chat privé
function affiche_chat_prive(id)
{
	//Ouverture de la conversation par ajax
	ajax_rapide("action.php?actionid=19&id=" + id);
	
	//Affichage du panneau de chat
	show_hide_panneau_private_chat(1);
	
	//Fenêtre de succès
	affiche_notification_succes("La conversation a &eacute;t&eacute; ajout&eacute;e.", "", 2);
	
	//Rechargement des fenêtres du chat privé
	refresh_fenetres_chat_decale();
}

//Fonction d'actualisation des fenêtres du chat
function refresh_fenetres_chat()
{
	var xhr = new XMLHttpRequest();
	xhr.open('GET', "action.php?actionid=20");
    xhr.onreadystatechange = function() { // On gère ici une requête asynchrone
        if (xhr.readyState == 4 && xhr.status == 200) { // Si le fichier est chargé sans erreur
            document.getElementById('conversations_private_chat').innerHTML = xhr.responseText;
        }
    };
    xhr.send(null);
}

//Fonction de décalage de la fonction d'actualisation des fenêtres du chat privé
function refresh_fenetres_chat_decale()
{
	//Actualisation des fenêtres du chat (en décalé)
	refresh_fenetres_chat();
}

//Fonction permettant de fermer une conversation
function close_conversation(id)
{
	//Fermeture de la conversation par ajax
	ajax_rapide("action.php?actionid=19&remove=1&id=" + id);
	
	//Actualisation des fenêtres du chat
	refresh_fenetres_chat_decale();
	
	//Fenêtre de succès
	affiche_notification_succes("La conversation a &eacute;t&eacute; ferm&eacute;e.", "", 2);
}
