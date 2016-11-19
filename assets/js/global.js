/**
 *	Main library for the Comunic Project
 *
 * 	@author Pierre HUBERT
 */

//Inclusion de la page
window.onkeydown = receive_key;

//Fonction d'analyse et de détermination des raccourcis claviers envoyés dans Comunic
function receive_key(e){
	//Pour IE
	if (!e){
		e = window.event;
	}    
	
	//Enregistre du code de la touche
	code = e.keyCode;
	
	//Analyse du code de la touche saisie
	//Pour le blocage des flux de streaming réseaux (Code 117 - Touche F6)
	if(code == 117)
	{
		//Blocage des flux réseaux
		if(autoriser_flux_streaming_live == false)
		{
			autoriser_flux_streaming_live = true;
			affiche_notification_succes('Le flux r&eacute;seau a &eacute;t&eacute; r&eacute;activ&eacute;. Appuyez sur F6 pour le d&eacute;sactiver &agrave; nouveau.');
		}
		else
		{
			//Demande confirmation
			if(confirm('Vous avez appuye sur la touche F6 de votre clavier. Dans Comunic, cette touche permet de bloquer l\'actualisation rapide des nouvelles notifications. Confirmez-vous le bloquage?'))
			{
				autoriser_flux_streaming_live = false;
				affiche_notification('Le flux r&eacute;seaux sera normalement maintenant limit&eacute; aux requ&ecirc;tes que vous envoyez, jusqu\'&agrave; la prochaine ouverture de page. Appuyez sur F6 pour r&eacute;activer le flux reseau.');
			}
		}
	}
}	
	
//Fonction Ajax rapide sans réponse attendue
function ajax_rapide(adresse){
	request = new XMLHttpRequest();
	request.open("GET", adresse);
	request.send(null);
} 

//Fonction de recherche et d'affichage si nécessaire des notifications
//Nécessite: prepare_joue_son
//Nécessite: joue_son
function getpopupnotification(file) {
	
	 var xhr_popup_notifications = new XMLHttpRequest();

	// Récupération du contenu du fichier
	xhr_popup_notifications.open('GET', file);

	xhr_popup_notifications.onreadystatechange = function() {

		if (xhr_popup_notifications.readyState == 4 && xhr_popup_notifications.status == 200) {

			if(xhr_popup_notifications.responseText != "")
			{
				affiche_notification(xhr_popup_notifications.responseText, "Notification", 5);
				joue_son("son_notification");
			}
			else
			{
				//Do nothing
			}

		} else if (xhr_popup_notifications.readyState == 4 && xhr_popup_notifications.status != 200) { 
			//Do nothing
		}

	}

	xhr_popup_notifications.send(null);

}

//Fonction de redirection
function redirige(adresse)
{
	//On redirige la page
	document.location.href=adresse;
}

//Préparation d'un contrôleur de son
function prepare_joue_son(adresse_ogg, adresse_mp3, id)
{
	//On ajoute le code HTML
	document.getElementById(id + "_area").innerHTML = "<audio id='" + id + "'><source src='" + adresse_ogg + "'></source><source src='" + adresse_mp3 +"'></source></audio>";
}

function joue_son(id)
{
	//On récupère les informations du son
	var player = document.querySelector('#' + id);
	player.play();

}

//Fonction d'affichage de notification
function affiche_notification(message, titre, temps)
{
	//On définit les variables complémentaire si cela n'a pas été fait
	if(!titre)
	{
		var titre = "Information";
	}
	if(!temps)
	{
		var temps = 3;
	}
	
	//On calcule de temps
	temps = temps*1000;
	
	$(function(){
			$.Notify({
                shadow: true,
                position: 'bottom-right',
                content: message,
				caption: titre,
				timeout: temps
            });
	});
}

//Fonction d'affichage de notification (erreur)
function affiche_notification_erreur(message, titre, temps)
{
	//On définit les variables complémentaire si cela n'a pas été fait
	if(!titre)
	{
		var titre = "Erreur";
	}
	if(!temps)
	{
		var temps = 3;
	}

	//On calcule de temps
	temps = temps*1000;
	
	$(function(){
			$.Notify({
                shadow: true,
                position: 'bottom-right',
                content: message,
				caption: titre,
				timeout: temps,
				style: {background: 'red', color: 'white',},
            });
	});
}

//Fonction d'affichage de notification (succès)
function affiche_notification_succes(message, titre, temps)
{
	//On définit les variables complémentaire si cela n'a pas été fait
	if(!titre)
	{
		var titre = "Succ&egrave;s";
	}
	if(!temps)
	{
		var temps = 3;
	}
	
	//On calcule de temps
	temps = temps*1000;
	
	$(function(){
			$.Notify({
                shadow: true,
                position: 'bottom-right',
                content: message,
				caption: titre,
				timeout: temps,
				style: {background: 'green', color: 'white'}
            });
	});
}

//Fonction de validation d'adresse mail
function validermail(mailarea)
{
	var email = document.getElementById(mailarea).value;
	
	if (email.match(/^[a-zA-Z0-9_.]+@[a-zA-Z0-9-]{1,}[.][a-zA-Z]{2,3}$/))
	{
		return true;
	}
	else
	{
		alert('Votre adresse mail est incorrecte!');
		return false;
	}
}

//Fonction pour afficher ou cacher des éléments
function MM_showHideLayers() { //v9.0
	var i,p,v,obj,args=MM_showHideLayers.arguments;
	for (i=0; i<(args.length-2); i+=3) 
	with (document) if (getElementById && ((obj=getElementById(args[i]))!=null)) { v=args[i+2];
	if (obj.style) { obj=obj.style; v=(v=='show')?'visible':(v=='hide')?'hidden':v; }
	obj.visibility=v; }
}

//Script de confirmation pour effectuer une opération
function confirmaction(destination, message) {
	if (confirm(message))
	{
		document.location.href=destination;
	}
	else
	{
		affiche_notification("Op&eacute;ration annul&eacute;e.", "Information", 7);
	}
}

//Affiche une fenêtre (code source dans l'appel de la fonction)
function affiche_fenetre(titre, code_source, icone, lock_screen, window_width, window_height)
{
	//On définit les variables complémentaire si cela n'a pas été fait
	if(!icone)
	{
		var icone = "icon-file";
	}
	if(!lock_screen)
	{
		var lock_screen = false;
	}
	if(!window_width)
	{
		var window_width = 500;
	}
	if(!window_height)
	{
		var window_height = 200;
	}
	
	$(function(){
		$.Dialog({
			shadow: true,
			overlay: lock_screen,
			draggable: true,
			flat: true,
			icon: '<span class="' + icone + '"></span>',
			title: titre,
			width: window_width,
			padding: 10,
			height : window_height,
			content: code_source
		});
	});
}

//Affiche la liste des smiley
function affiche_liste_smile(destination, liste)
{
	//On définit les variables complémentaire si cela n'a pas été fait
	if(!liste)
	{
		var liste = liste_smile;
	}
	
	var source = "<table class='liste_smile'>";
	var count = 0;
	
	//On parcours la liste
	for (var i = 0; i < liste.length; i++) {
	
		if(count == 0)
		{
			source = source + "<tr>";
		}
		
		source = source + "<td><img class='un_smile_de_liste' onClick='" + 'ajout_source("'+liste[i][0]+'", "'+ destination +'"); ' + "' src='" + liste[i][1] + "' title='" + liste[i][2] + "' /></td>";  //Code retiré : $.Dialog.close();
		
		count = count + 1;
		
		if(count == 7)
		{
			source = source + "</tr>";
			count = 0;
		}
	}
	
	source = source + "</table>";
	
	//Affichage de la fenêtre
	affiche_fenetre("", source, "icon-smiley", true, 130, 100);
}

//Ajout de la source à un champs de texte (ex:Smiley)
function ajout_source(source_ajout, destination)
{
	//On récupère l'ancienne source
	var source_original = document.getElementById(destination).value;
	
	//On ajoute la nouvelle
	document.getElementById(destination).value = source_original + source_ajout;
}

//Affichage des notifications V.2
function affiche_notifications(show_pannel)
{
	if(!show_pannel)
	{
		show_pannel = 1;
	}
	
	//On vérifie si il faut afficher le panneau des notifications
	if(show_pannel == 1)
	{
		//Affichage du panneau des notifications
		//document.getElementById('new_notification').style.visibility = "visible";
		MM_showHideLayers("new_notification", "", "show");
	}
	else
	{
		//On masque le panneau des notifications
		//document.getElementById('new_notification').style.visibility = "hidden";
		MM_showHideLayers("new_notification", "", "hide");

	}
	
	//Ouverture de la requête
	xhr_notifications = new XMLHttpRequest();
	xhr_notifications.open("GET", "notification.php");
	
	xhr_notifications.onreadystatechange = function() {
		if (xhr_notifications.readyState == 4 && (xhr_notifications.status == 200 || xhr_notifications.status == 0)) {
			document.getElementById('new_iframenotification').innerHTML = xhr_notifications.responseText;
		}
	};
	
	//Envoi de la requête
	xhr_notifications.send(null);
}

//Affichage d'une fenêtre de chat privé (ancienne version)
function affiche_chat_prive_old(id)
{
	if(!id)
	{
		id = "";
	}
	else
	{
		id = "?screen=chat&id=" + id;
	}
	
	affiche_fenetre("Chat priv&eacute;", "<iframe src='privatechat.php" + id + "' class='iframenotification'>Votre navigateur n'est pas compatible avec le logiciel de chat priv&eacute;. Veuillez mettre votre navigateur &agrave; jour ou utiliser Mozilla Firefox.</iframe>", "icon-user", true, 415, 170);
}

//Fermer le panneau des amis
function ferme_panneau_amis()
{
	//On masque le panneau
	document.getElementById('listeamis').style.display = "none";
	
	//On enregistre le choix de l'utilisateur par une requête Ajax
	ajax_rapide("index.php?miseajourpanneau=0");
	
	//On affiche un message à l'utilisateur
	affiche_notification("Masquage du panneau des amis termin&eacute;.", "Information", 2);
}

//Fermer le panneau des amis
function affiche_panneau_amis()
{
	//On masque le panneau
	document.getElementById('listeamis').style.display = "block";
	
	//On enregistre le choix de l'utilisateur par une requête Ajax
	ajax_rapide("index.php?miseajourpanneau=1");
	
	//On affiche un message à l'utilisateur
	affiche_notification("Affichage du panneau des amis termin&eacute;.", "Information", 2);
}

//Fonction de post de commentaires
function submitCommentaire(destination, idcommentaireform, iddestination, idtexte, page, iduser)
{
	var commentaire = encodeURIComponent(document.getElementById(idcommentaireform).value);

	//var touscommentaires = document.getElementById(iddestination);
	
	if(page == 0)
	{
		//Préparation de la recherche des nouveaux commentaires
		filegetcommentaire = "commentaire.php?idtexte=" + idtexte + "&id=" + iduser;
	}
	else
	{
		//Préparationn de la recherche des nouveaux ccommentaires
		filegetcommentaire = "commentaire.php?idtexte="+idtexte+"&page="+page+ "&id=" + iduser;
	}
	
	//On vérifie si une image est jointe
	if(document.getElementById('image_' + idtexte).value != "")
	{
		//On effectue le nécessaire
		var vFD = new FormData(document.getElementById("addcommentaire_" + idtexte)); 

		// create XMLHttpRequest object, adding few event listeners, and POSTing our data
		var oXHR = new XMLHttpRequest();        
		
		oXHR.onreadystatechange = function() {
			if (oXHR.readyState == 4 && (oXHR.status == 200 || oXHR.status == 0)) {
				affiche_notification_succes("Commentaire enregistr&eacute;.", "");
				requeteajaxcommentairesmobiles(filegetcommentaire, iddestination); //Affiche tous les commentaires
			}
		};
		
		oXHR.open('POST', destination);
		oXHR.send(vFD);

	}
	else
	{
		document.getElementById(idcommentaireform).value = ""; //Commentaire (forumlaire) vidé
		
		if(commentaire == "")
		{
			affiche_notification_erreur("L'ajout de commentaires vides est interdit !", "Erreur", 5);
		}
		else
		{
			//Nouvel affichage des commentaires
			//touscommentaires = touscommentaires + "<tr><td>Vous</td><td>" + commentaire + "</td></tr>";
			document.getElementById("addcommentaire_" + idtexte).innerHTML = "<tr><td>Veuillez patienter, chargement en cours...</td></tr>";;
			
			//Requête AJAX
			var xhr = new XMLHttpRequest();
			xhr.open("POST", destination, true);
			xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			xhr.send("commentaire="+commentaire+"&idtexte="+idtexte);
			
			xhr.onreadystatechange = function() {
					if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) {
						affiche_notification_succes("Commentaire enregistr&eacute;.", "");
						requeteajaxcommentairesmobiles(filegetcommentaire, iddestination); //Affiche tous les commentaires
					}
			};
		}
	}
}

//Fonction de recherche de commentaires (nécessaire pour le post de commentaire)
function requeteajaxcommentairesmobiles(file, balisescommentaires) {
				
var xhr = new XMLHttpRequest();

// Récupération du contenu du fichier
 xhr.open('GET', file);

 xhr.onreadystatechange = function() {

 if (xhr.readyState == 4 && xhr.status == 200) {

 document.getElementById(balisescommentaires).innerHTML = xhr.responseText;

 } else if (xhr.readyState == 4 && xhr.status != 200) { 
					
document.getElementById(balisescommentaires).innerHTML =  'Une erreur est survenue. Merci de r&eacute;essayer ult&eacute;rieurment. ';

 }

 }

xhr.send(null);

}

//Fonction de détection de la résolution d'écran
function get_screen_width()
{
	 //Minimum for desktop : 620
	 var viewportwidth;
	 var viewportheight;
	  
	 // the more standards compliant browsers (mozilla/netscape/opera/IE7) use window.innerWidth and window.innerHeight
	  
	 if (typeof window.innerWidth != 'undefined')
	 {
		  viewportwidth = window.innerWidth,
		  viewportheight = window.innerHeight
	 }
		  
	// IE6 in standards compliant mode (i.e. with a valid doctype as the first line in the document)
		 
	 else if (typeof document.documentElement != 'undefined' && typeof document.documentElement.clientWidth != 'undefined' && document.documentElement.clientWidth != 0)
	 {
		   viewportwidth = document.documentElement.clientWidth,
		   viewportheight = document.documentElement.clientHeight
		 }
	  
		 // older versions of IE
	  
	 else
	 {
		   viewportwidth = document.getElementsByTagName('body')[0].clientWidth,
		   viewportheight = document.getElementsByTagName('body')[0].clientHeight
	 }
	 
	//Renvoi du résultat
	return viewportwidth;
}

//Fonction d'envoi au Serveur la résolution (width) du client
//Nécessite:
//			get_screen_width
//			ajax_rapide
function send_serveur_width()
{
	var width_screen = get_screen_width();
	ajax_rapide("index.php?screen_width=" + width_screen);
}

//Fonction permettant la mise en évidence de la présence de nouveaux messages pour l'utilisateur
function verifie_messages_non_lus(type)
{
	//On définit les variables complémentaire si cela n'a pas été fait
	if(!type)
	{
		var type = 1;
	}
	
	//Détermination du fichier
	if(type == 1)
	{
		//Pour les nouveaux messages
		fichier = 'action.php?actionid=1';
	}
	else
	{
		//Pour les demandes d'amis
		fichier = 'action.php?actionid=7';
	}
	
	// Récupération du contenu du fichier
	var xhr = new XMLHttpRequest();
	xhr.open('GET', fichier);

	xhr.onreadystatechange = function() {

		if (xhr.readyState == 4 && xhr.status == 200) {
			
			if(xhr.responseText == "1")
			{
				if(type == 1) {
					document.getElementById('new_message').innerHTML = '<a class="element brand" href="messagerie.php" title="Vous avez de nouveaux messages non lus"><span class="icon-mail"></span></a>'; //Pour les nouveaux messages
				}
				else {
					document.getElementById('new_friend').innerHTML = '<a class="element brand" href="amis.php" title="Nouvelles demandes d\'amis"><span class="icon-stats-up"></span></a>'; //Pour les demandes d'amis
				}
			}
			else
			{
				//Do nothing
			}

		}

	}
	
	xhr.send(null);
}
	
	
//Fonction permettant la mise en évidence de la présence de nouvelles notifications pour l'utilisateur
function verifie_notifications_non_vues(debut_URL)
{
	//On vérifie si le flux est bien autorisé
	if(autoriser_flux_streaming_live == false)
	{
		console.log("La requete de recherche de nouvelles notifications a ete bloquee en raison de la configuration de la page l'ayant demande.");
		return false;
	}
	
	
	var xhr = new XMLHttpRequest();

	// Récupération du contenu du fichier
	xhr.open('GET', debut_URL + 'action.php?actionid=3');

	xhr.onreadystatechange = function() {

		if (xhr.readyState == 4 && xhr.status == 200) {
		
			if(xhr.responseText != "0")
			{
				//source_notification = document.getElementById('nb_notification_area').innerHTML;
				source_notification =  "<b><font class='nb_unread_notification'>" + xhr.responseText + "</font></b>";
				
				//Application des modifications si nécessaire
				if(document.getElementById('nb_notification_area').innerHTML != source_notification)
				{
					//Modification du nombre
					document.getElementById('nb_notification_area').innerHTML = source_notification;
					
					//Actualisation de l'iframe
					//document.getElementById('new_iframenotification').src = 'notification.php';
				}
				
				//Modification (si nécessaire) du titre de la page
				if (~document.title.indexOf('(' + xhr.responseText + ')')) 
				{
					//Ne rien faire
				}
				else
				{
					//On vérifie alors si un autre numéro de notifications a été affiché
					if(/(\d)/.test(document.title))
					{
						//Si un autre nombre de notifications a déjà été affiché, on le modifie
						document.title = document.title.replace(/(\d)/,  xhr.responseText);
					}
					else
					{
						//Modification du titre
						document.title = "(" + xhr.responseText + ") " + document.title;
					}
				}
			}
			else
			{
				document.getElementById('nb_notification_area').innerHTML = "";
				 
				//On s'assure que le nombre de notifications est à 0
				document.title = document.title.replace(/(\d)/,  "");
				document.title = document.title.replace("()",  "");
			}

		} else if (xhr.readyState == 4 && xhr.status != 200) { 
			//Do nothing
		}

	}
	
	xhr.send(null);
}

//Fonction permettant de vider des éléments
function vide_id(id)
{
	document.getElementById(id).innerHTML = "";
}

//Fonction d'édition des commentaires
function editcommentaire (idcommentaire, idtexte, iduser, page)
{
	var new_comment = "";
	new_comment = prompt('Saisissez le nouveau commentaire qui va remplacer l\'ancien :', " ");
	
	if(confirm('Confirmez-vous ce nouveau commentaire ? : ' + new_comment))
	{
		if(new_comment != "" && new_comment != " ")
		{
			//Détermination de la zone d'arrivée des commentaires
			iddestination = "tablecommentaire" + idtexte;
			
			//On vide la zone de commentaires
			document.getElementById(iddestination).innerHTML = "<tr><td>Veuillez patienter, chargement en cours...</td></tr>";
			
			//-------------------------------------------------------------------------------------------//
			// -------------------------------- Modification du commentaire -----------------------------//
			//-------------------------------------------------------------------------------------------//
			//Requête AJAX
			var xhr = new XMLHttpRequest();
			xhr.open("POST", "action.php?actionid=8", true);
			xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			xhr.send("commentaire="+new_comment+"&idtexte="+idtexte+"&idcommentaire="+idcommentaire);
			
			xhr.onreadystatechange = function() {
					if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) {
						
						//Pour débogage uniquement
						//alert(xhr.responseText);
					
						//Message de succès
						affiche_notification_succes("Modification du commentaire termin&eacute;e.", "");
						
						//-------------------------------------------------------------------------------------------//
						// -------------------------------- Téléchargement des nouveaux commentaires ----------------//
						//-------------------------------------------------------------------------------------------//
						if(page == 0)
						{
							//Préparationn de la recherche des nouveaux commentaires
							filegetcommentaire = "commentaire.php?idtexte=" + idtexte + "&id=" + iduser;
						}
						else
						{
							//Préparationn de la recherche des nouveaux ccommentaires
							filegetcommentaire = "commentaire.php?idtexte="+idtexte+"&page="+page+ "&id=" + iduser;
						}
						
						requeteajaxcommentairesmobiles(filegetcommentaire, iddestination);
						//-------------------------------------------------------------------------------------------//
					}
			};
			xhr.send(null);
			//-------------------------------------------------------------------------------------------//	
		}
		else
		{
			affiche_notification_erreur("Le commentaire est vide !", "Erreur", 3);
		}
	}
	else
	{
		affiche_notification_erreur("Modification du commentaire annul&eacute;e.", "Erreur", 3);
	}
}

//Fonction permettant d'ouvrir une fenêtre d'upload de vidéo
function ouvre_fenetre_upload_video()
{
	window.open("action.php?actionid=10","upload_video","width=730px,height=400")
}

//Fonction permettant de changer le nom d'une vidéo
function change_nom_video(id, adresse_rafraichissement)
{
	var new_video_name = "";
	new_video_name = prompt('Saisissez le nouveau nom pour la video qui va remplacer l\'ancien :', " ");
	
	if(confirm('Confirmez-vous ce nouveau nom ? (OK : oui, Annuler : non) : ' + new_video_name))
	{
		if(new_video_name != "" && new_video_name != " ")
		{
			
			//-------------------------------------------------------------------------------------------//
			// ---------------------------- Modification du nom de la vidéo -----------------------------//
			//-------------------------------------------------------------------------------------------//
			//Requête AJAX
			var xhr = new XMLHttpRequest();
			xhr.open("POST", "action.php?actionid=12", true);
			xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			xhr.send("nouveau_nom_video="+new_video_name+"&idvideo="+id);
			
			xhr.onreadystatechange = function() {
					if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) {
						
						//Pour débogage uniquement
						//alert(xhr.responseText);
					
						//Message de succès
						affiche_notification_succes("Modification du nom de la vid&eacute;o termin&eacute;e.", "");
						
						//-------------------------------------------------------------------------------------------//
						// -------------------------------- Rafraîchissement de la page web -------------------------//
						//-------------------------------------------------------------------------------------------//
						window.location = adresse_rafraichissement;
						//-------------------------------------------------------------------------------------------//
					}
			};
			xhr.send(null);
			//-------------------------------------------------------------------------------------------//	
		}
		else
		{
			affiche_notification_erreur("Le nom saisi est vide !", "Erreur", 3);
		}
	}
	else
	{
		affiche_notification_erreur("Modification du nom de la vid&eacute;o annul&eacute;e.", "Erreur", 3);
	}
}

//Fonction permettant de demander la confirmation de la suppression d'une vidéo
function confirm_delete_video(URL_if_yes)
{
	if(confirm('Voulez-vous vraiment supprimer cette video ? (OK : oui, Annuler : non) : '))
	{
		//Redirection pour la suppression de la vidéo
		window.location = URL_if_yes;
	}
	else
	{
		affiche_notification("Suppression de la vid&eacute;o annul&eacute;e.");
	}
}

// Fonction permettant d'afficher ou de masquer des éléments
// Service Pierre 2015, tous droits de réutilisation ou de 
// Reproduction réservés.
function show_hide_id(id, visibilite)
{
	//On définit les variables complémentaire si cela n'a pas été fait
	if(!visibilite)
	{
		var visibilite = "visible";
	}

	document.getElementById(id).style.visibility = visibilite;
}

//Fonction permettant de changer le niveau de visibilité d'un post
function change_niveau_visibilite_post(id_post, niveau)
{
	//Requete de modification
	ajax_rapide("action.php?actionid=14&nouveau_niveau_visibilite=" + niveau + "&idtexte=" + id_post);
	
	//Détermination de l'ID de l'image
	id_image = "change_niveau_visibilite_" + id_post + "_img";
	
	//Modification de l'image
	if(niveau == 1)
	{
		document.getElementById(id_image).src = config['pathAssets'] + "img/users_5.png";
	}
	else
	{
		if(niveau == 2)
		{
			document.getElementById(id_image).src = config['pathAssets'] + "img/users_3.png";
		}
		else
		{
			document.getElementById(id_image).src = config['pathAssets'] + "img/user.png";
		}
	}
	
	//Message de succès
	affiche_notification_succes("Modification du niveau de visibilit&eacute; du post termin&eacute;e.", "");
	
	//Masquage du menu de changement du niveau de visibilté
	show_hide_id("change_niveau_visibilite_" + id_post, 'hidden');
}

//Fonction de suppression de commentaire
function delete_comment(idcommentaire, idtexte, iduser, page)
{

	if(confirm('Voulez-vous vraiment supprimer ce commentaire ? : '))
	{
		//Détermination de la zone d'arrivée des commentaires
		iddestination = "tablecommentaire" + idtexte;
		
		//On vide la zone de commentaires
		document.getElementById(iddestination).innerHTML = "<tr><td>Veuillez patienter, chargement en cours...</td></tr>";
		
		//-------------------------------------------------------------------------------------------//
		// -------------------------------- Suppression du commentaire -----------------------------//
		//-------------------------------------------------------------------------------------------//
	
		//Requête AJAX
		ajax_rapide("action.php?idtexte=" + idtexte + "&idcommentaire=" + idcommentaire + "&actionid=15");
		
		
		//Pour débogage uniquement
		//alert(xhr.responseText);
		
		//Message de succès
		affiche_notification_succes("Suppression du commentaire termin&eacute;e.", "");
						
		//-------------------------------------------------------------------------------------------//
		// -------------------------------- Téléchargement des nouveaux commentaires ----------------//
		//-------------------------------------------------------------------------------------------//
		if(page == 0)
		{
			//Préparationn de la recherche des nouveaux commentaires
			filegetcommentaire = "commentaire.php?idtexte=" + idtexte + "&id=" + iduser;
		}
		else
		{
			//Préparationn de la recherche des nouveaux ccommentaires
			filegetcommentaire = "commentaire.php?idtexte="+idtexte+"&page="+page+ "&id=" + iduser;
		}
		
		//Exécution de la requête
		requeteajaxcommentairesmobiles(filegetcommentaire, iddestination);
		//-------------------------------------------------------------------------------------------//
	}
	else
	{
		affiche_notification_erreur("Suppression du commentaire annul&eacute;e.", "Erreur", 3);
	}
}

//Fonction permettant d'aimer un commentaire
function like_comment(idcommentaire, idtexte, aime_deja)
{
	//On détermine si la personne doit aimer ou pas
	var aime = 0;
	
	if(/aime_vide/i.test(document.getElementById("like_comment_" + idcommentaire).src))
		aime = 1;
	
	//Envoi d'une requête pour aimer
	ajax_rapide("action.php?actionid=16&idcommentaire=" + idcommentaire + "&idtexte=" + idtexte + "&aime=" + aime);
	
	//Modification de l'image
	//Définition de la variable
	var complement_nom_image = "";
	
	//On modifie le complément du nom de l'image si il ne s'agit de ne plus aimer
	if(aime == 0)
		complement_nom_image = "_vide";
	
	//Modification de l'image
	document.getElementById("like_comment_" + idcommentaire).src = config['pathAssets'] + "img/aime" + complement_nom_image + ".png";
	
	//Message de succès
	if(aime == 1)
		affiche_notification_succes("Vous aimez d&eacute;sormais ce commentaire.", "");
	else
		affiche_notification_succes("Vous n'aimez d&eacute;sormais plus ce commentaire.", "");
}

//Fonction permettant d'aimer un texte ou une page
function like_text_page(id, type, aime_deja)
{
	//On détermine si il faut aimer ou pas
	if(aime_deja == 1)
		like = 0;
	else
		like = 1;
		
	//On détermine l'ID du conteneur
	if(type == "page")
		id_conteneur = "aime_page";
	else
		id_conteneur = "aime_texte_" + id;
	
	//On envoie une requête AJAX pour récupérer le nouveau code source du conteneur
	var xhr = new XMLHttpRequest();

	// Récupération du contenu du fichier
	xhr.open('GET', "action.php?actionid=21&type=" + type + "&id=" + id + "&aime=" + like);

	xhr.onreadystatechange = function() {

		if (xhr.readyState == 4 && xhr.status == 200) {

			document.getElementById(id_conteneur).innerHTML =  xhr.responseText;

		} else if (xhr.readyState == 4 && xhr.status != 200) { 
			
			//Message d'erreur
			console.log("Il semblerait qu'il y a un problème de connexion Internet. Vérifiez que tous les cables réseau sont branchés ou que le Wi-Fi est activé. Conséquence: Impossible d'aimer ce text ou cette page.");
		}

	}

	xhr.send(null);

	
	//On affiche un message de succès, suivant le type et si l'on aime ou l'on aime plus.
	if(type == "page")
	{
		if(like == 1)
			affiche_notification_succes("Vous aimez d&eacute;sormais cette page.", "");
		else
			affiche_notification_succes("Vous n'aimez d&eacute;sormais plus cette page.", "");
	}
	else
	{
		if(like == 1)
			affiche_notification_succes("Vous aimez d&eacute;sormais ce post.", "");
		else
			affiche_notification_succes("Vous n'aimez d&eacute;sormais plus ce post.", "");
	}
}

//Fonction permettant de faire une recherche AJAX lors de la saisie d'un nom d'utilisateur dans la barre de recherche
function search_user_ajax()
{
	//Récupération du contenu du champs de texte
	var contenu = document.getElementById("search_user_input").value;
	
	//On vide le tableau des précédents affichages
	document.getElementById("result_search").innerHTML = "";
	document.getElementById("list_results").innerHTML = "";
	
	//On vérifie que le champ n'est pas vide
	if(contenu != "")
	{
		//Requête de recherche
		var xhr = new XMLHttpRequest();

		// Récupération du contenu du fichier
		xhr.open('GET', "action.php?actionid=22&search=" + contenu);

		xhr.onreadystatechange = function() {

			if (xhr.readyState == 4 && xhr.status == 200) {
				//Décodage du contenu
				var reponse =  xhr.responseText;
				reponse = reponse.split('<|>'); 
				var longueur_reponse = reponse.length;
				
				//On masque la zone de résultat si il n'y en a pas
				if(xhr.responseText == "")
					document.getElementById("result_search").style.visibility = "hidden";
				
				//Préparation du parcours du tablau
				results = document.getElementById("result_search");
				liste_results = document.getElementById("list_results");
				
				//Parcours du tableau
				for (var i = 0, div ; i < longueur_reponse ; i++) {
					//Décodage du contenu
					analyse = reponse[i].split('*!*');
					
					//On vérifie que le résultat est correct
					if(analyse.length == 3)
					{
						//Afficahge de la zone de résultat
						document.getElementById("result_search").style.visibility = "visible";
						
						//On vérifie que le résultat n'est pas déjà affiché
						contenu_liste = liste_results.innerHTML;
						if((contenu_liste.lastIndexOf("|" + analyse[0] + "|")) == -1)
						{
							//On peut afficher le résultat
							div = results.appendChild(document.createElement('div'));
							
							//On vérifie si on est sur la page d'un utilisateur ou pas
							var vide_form = "document.getElementById(\"search_user_input\").value = \"\"; document.getElementById(\"result_search\").style.visibility = \"hidden\";";
							if(!check_if_we_are_on_user_page())
							{
								//Un rechargement est nécessaire en cas de clic
								var debut_source = "<a href='index.php?id=" + analyse[0] + "' ";
							}
							else
							{
								//Un rechargement rapide peut être effectué
								var debut_source = "<a style='cursor: pointer;' onClick='"+ vide_form +" change_page_personne(" + analyse[0] + ")' "
							}
							
							div.innerHTML = debut_source + ">" + analyse[2] + analyse[1] + "</a>" + '  <a onclick="affiche_chat_prive(' + analyse[0] + ');"><img src="' + config['pathAssets'] + 'img/prive.png" border="0" height="16" width="16"></a>';
							
							//On enregistre l'affichage du résultat
							liste_results.innerHTML = liste_results.innerHTML + "|" + analyse[0] + "|";
						}
					}
				}
			} else if (xhr.readyState == 4 && xhr.status != 200) { 
				
				//Message d'erreur
				console.log("Il semblerait qu'il y a un problème de connexion Internet. Vérifiez que tous les cables réseau sont branchés ou que le Wi-Fi est activé. Conséquence: Impossible d'aimer ce text ou cette page.");
			}

		}

		xhr.send(null);
	}
	else
	{
		//Sinon on masque la zone de résultat
		document.getElementById("result_search").style.visibility = "hidden";
	}
}

//Fonction permettant de supprimer toute les notifications
function supprime_toute_les_notifications()
{
	if(confirm("Voulez-vous vraiment supprimer toute les notifications ?"))
	{
		//On supprime les notifications
		ajax_rapide('notification.php?all_vu');
		
		//On affiche un message de succès
		affiche_notification_succes('Les notifications ont &eacute;t&eacute; supprim&eacute;es.', '', 3);
		
		//On actualise le panneau des notifications
		affiche_notifications();
	}
}

//Fonction permettant de supprimer les anciennes notififications
function delete_old_notifications()
{
	$.Dialog({
        flat: false,
        shadow: true,
		icon: "<span class='icon-history'></span>",
		overlay: true,
		draggable: true,
        title: 'Supprimer les anciennes notifications',
        content: '<iframe src="action.php?actionid=24" id="delete_old_notifications"></iframe>',
        height: 200,
		width: 350
    });
}

//Fonction permettant de voir, d'ajouter et de supprimer un abonnement
function get_abonnement(id, change)
{
	//On vérifie si il faut changer l'abonnement
	if(change != 0)
		var change = "&change";
	else
		var change = "";
	
	//Définition de l'ID de destination
	var id_destination = 'abonnement_' + id;
	
	//On fait une requête ajax de recherche
	xhr_abonnement = new XMLHttpRequest();
	xhr_abonnement.open("GET", "action.php?actionid=25&id=" + id + change);
	
	xhr_abonnement.onreadystatechange = function() {

		if (xhr_abonnement.readyState == 4 && xhr_abonnement.status == 200) {

			document.getElementById(id_destination).innerHTML =  xhr_abonnement.responseText;

		} else if (xhr_abonnement.readyState == 4 && xhr_abonnement.status != 200) { 
			
			//Message d'erreur
			document.getElementById(id_destination).innerHTML = "Erreur";
			
			//Message d'erreur (console)
			console.log("Il semblerait qu'il y a un problème de connexion Internet. Vérifiez que tous les cables réseau sont branchés ou que le Wi-Fi est activé.");
		}

	}
	
	xhr_abonnement.send(null);
}

//Fonction permettant de transférer le contenu du canvas vers un champ de texte (de préférence caché) et d'envoyer le contenu
function send_snapshot_webcam_for_avatar()
{
	//Récupération de la source du canvas
	var source = document.getElementById("target_image_webcam_nouvel_avatar").toDataURL();
	
	//Renvoi de la source du canvas vers le champs de texte
	document.getElementById("data").value = source;
	
	//Envoi du formulaire
	document.getElementById("post_new_image_from_webcam").submit();
}

//Fonction de récupération et d'affichage de textes
function get_show_textes(id, id_destination, page, post)
{
	//On vérifie si il faut recharge tous les textes ou non
	var complement = "";
	
	//On vérifie si il faut chercher un post précis ou non
	if(post != 0)
	{
		complement += "&post=" + post;
	}
	
	//On fait une requête ajax de recherche
	xhr_show_textes = new XMLHttpRequest();
	xhr_show_textes.open("GET", "action.php?actionid=29&id=" + id + "&page=" + page + complement);
	
	xhr_show_textes.onreadystatechange = function() {

		if (xhr_show_textes.readyState == 4 && xhr_show_textes.status == 200) {
			
			//Décodage de la réponse
			var response = xhr_show_textes.responseText;
			/<tr id="view_more">(.+)tr>/.exec(response);
			var valeurs = (RegExp.$1);
			valeurs = valeurs.replace("<", "");
			valeurs = valeurs.split('|');
			
			//On vérifie que les données sont correctes
			complement = "";
			if(valeurs.length == 2)
			{
				if(post == 0)
					complement = "<tr class='metro'><td colspan='2'><input type='button' value='Afficher plus de textes' onClick=" + '"' + "get_show_textes(" + valeurs[0] + ", 'corpstexte', " + valeurs[1] + ", 0); this.parentNode.parentNode.style.display = 'none'" + '"' + " /></td></tr>";
				
				//On supprime les valeurs du fichier réponse
				response = response.replace(valeurs[0] + "|" + valeurs[1], "");
			}
			
			var new_textes = document.createElement('tbody');
			new_textes.innerHTML = response + complement;
			document.getElementById(id_destination).appendChild(new_textes);
			
			
			//Affichage des comptes à rebours
			var timers = document.getElementsByTagName('timers'),    
			timersLen = timers.length;
			
			for (var i = 0 ; i < timersLen ; i++) {
				infos = timers[i].innerHTML.split('|');
				/*
				$("#" + infos[0]).countdown({
					blink: true, // blink divider
					stoptimer: infos[1], // the string value of datetime, example '2013-07-05 12:00'
				});*/
				
				//var ts = countdown(infos[1], null, -2018, 11, 0);
				//	document.getElementById(infos[0]).innerHTML = infos[1]+" - " + ts;
				
				launch_countdown(infos[1], infos[0]);
			}
			
			//Affichage des camemberts des sondages
			var liste_sondages = document.getElementsByTagName('sondage_result'),    
			 SondageLen = liste_sondages.length;
			
			for (var i = 0 ; i < SondageLen ; i++) {
				infos = liste_sondages[i].innerHTML.split('|');
				//alert('draw_camembert(' + infos[0] + ', ' + infos[1] + ');'); //Debug only
				draw_camembert(infos[0], infos[1]);
			}
			
			//Affichage des vidéos
			var videos = document.getElementsByTagName('video'),   
			VideosLen = videos.length;
			
			for (var i = 0 ; i < VideosLen ; i++) {
				id = videos[i].id;
				
				videojs(id, {}, function(){
				  // Player (this) is initialized and ready.
				});
			}

		} else if (xhr_show_textes.readyState == 4 && xhr_show_textes.status != 200) { 
			
			//Message d'erreur
			document.getElementById(id_destination).innerHTML += "Erreur, merci de r&eacute;essayer ult&eacute;rieurement.";
			
			//Message d'erreur (console)
			console.log("Il semblerait qu'il y a un problème de connexion Internet. Vérifiez que tous les cables réseau sont branchés ou que le Wi-Fi est activé.");
		}

	}
	
	xhr_show_textes.send(null);
}

//Fonction de récupération et d'affichage de textes
function get_show_header(id, demander_ami)
{
	//Définition des variables
	id_destination_header = 'header_contenu';

	//On détermine si il faut ajouter un complément ou pas
	if(demander_ami == 0)
		complement = "";
	else
		complement = "&demanderamis";
	
	//On fait une requête ajax de recherche
	xhr_show_header = new XMLHttpRequest();
	xhr_show_header.open("GET", "action.php?actionid=31&id=" + id + complement);
	
	xhr_show_header.onreadystatechange = function() {

		if (xhr_show_header.readyState == 4 && xhr_show_header.status == 200) {

			document.getElementById(id_destination_header).innerHTML = xhr_show_header.responseText;
			
			//Chargement de l'abonnement de la personne
			get_abonnement(id, 0);
			
			//Adaptation du titre de la page
			document.title = document.getElementById('nom_personne').innerHTML + " - Comunic";
			

		} else if (xhr_show_header.readyState == 4 && xhr_show_header.status != 200) { 
			
			//Message d'erreur
			document.getElementById(id_destination_header).innerHTML = "Erreur, merci de r&eacute;essayer ult&eacute;rieurement en appuyant sur F5 ou en rechargeant la page.";
			
			//Message d'erreur (console)
			console.log("Il semblerait qu'il y a un problème de connexion Internet. Vérifiez que tous les cables réseau sont branchés ou que le Wi-Fi est activé.");
		}

	}
	
	xhr_show_header.send(null);
}

//Fonction de récupération et d'affichage du formulaire d'envoi de texte
function get_show_form_send_text(id)
{
	//Définition des variables
	id_destination_add_form_texte = 'add_form_texte';
	
	//On fait une requête ajax de recherche
	xhr_show_add_form = new XMLHttpRequest();
	xhr_show_add_form.open("GET", "action.php?actionid=32&id=" + id);
	
	xhr_show_add_form.onreadystatechange = function() {

		if (xhr_show_add_form.readyState == 4 && xhr_show_add_form.status == 200) {
			
			//Affichage du formulaire
			document.getElementById(id_destination_add_form_texte).innerHTML =  xhr_show_add_form.responseText;
			
			//Chargement de l'éditeur TinyMce pour l'ajout de textes
			var scriptElement = document.createElement('script');
			scriptElement.src = config['pathAssets'] + 'js/ajoutsimple.js';
			document.body.appendChild(scriptElement); 

		} else if (xhr_show_add_form.readyState == 4 && xhr_show_add_form.status != 200) { 
			
			//Message d'erreur
			document.getElementById(id_destination_add_form_texte).innerHTML = "Erreur, merci de r&eacute;essayer ult&eacute;rieurement en appuyant sur F5 ou en rechargeant la page.";
			
			//Message d'erreur (console)
			console.log("Il semblerait qu'il y a un problème de connexion Internet. Vérifiez que tous les cables réseau sont branchés ou que le Wi-Fi est activé.");
		}

	}
	
	xhr_show_add_form.send(null);
}

//Fonction de changement de personne
function change_page_personne(id, post)
{
	//Définition d'une nouvelle url
	
	//On vérifie si il faut afficher un texte précis
	var complement_url = "";
	var idpost = 0;
	if(post > 0)
	{
		complement_url = "&post=" + post;
		idpost = post;
	}
	
	//On vérifie si on est sur la page de l'utilisateur
	if(/\?/.test(document.URL))
	{
		//Récupération des paramètres de l'URL
		/\?(.+)/.exec(document.URL);
		var parametres_url = RegExp.$1;
		
		var nouvelle_url = document.URL;
		nouvelle_url = nouvelle_url.replace(parametres_url, "id=" + id) + complement_url;
	}
	else
	{
		//Sinon on rajoute directement les bons paramètres
		var nouvelle_url = document.URL + "?id=" + id + complement_url;
	}
	
	//Changement de l'URL
	window.history.pushState(document.title,document.title, nouvelle_url);
	
	//Changement de l'header
	get_show_header(id, 0);
	
	//Définition du champs de destination des textes
	var id_destination_champs_texte = "corpstexte";
	
	//On vide le champs de textes
	document.getElementById(id_destination_champs_texte).innerHTML = "";
	document.getElementById("add_form_texte").innerHTML = "";
	//document.getElementById('backup_content').value = "";
	
	if(idpost == 0)
	{
		//Changement du formulaire d'envoi de texte
		get_show_form_send_text(id);
	}
	
	//On recharge le champs de textes
	get_show_textes(id, id_destination_champs_texte, 0, idpost);
}

//Fonction permettant de vérifier si on est sur la page d'un utilisateur ou pas
function check_if_we_are_on_user_page()
{
	//On vérifie si l'adresse contient un ".php"
	if(/.php/.test(document.URL))
	{
		//On vérifie alors si on est sur la page index.php
		if(/index.php/.test(document.URL))
		{
			//On est sur la page d'un utilisateur
			return true;
		}
		else
		{
			//On n'est pas sur la page d'un utilisateur
			return false;
		}
	}
	else
	{
		//On est sur la page d'acceuil ou sur la page d'un utilisateur
		return true;
	}
}

//Fonction permettant de changer la page de l'utilisateur avec auto adaptation ( en fonction de si on est déjà sur la page d'un utilisateur ou pas )
function open_page_ameliore(id)
{
	//On vérifie si on est sur la page d'un utilisateur
	if(check_if_we_are_on_user_page())
		change_page_personne(id); //Chargement rapide de la page de l'utilisateur
	else
	location.href = "index.php?id=" + id; //Rechargement complet de la page
}

//Fonction permettant d'informer que l'on nécessite un rechargement de tous les posts
function require_reload_all_posts()
{
	//document.getElementById("backup_content").value = "require reload";
}

//Fonction de changement de la page d'une personne pour un post précis
function change_page_personne_with_post(id, idpost)
{
	//On vérifie si on est sur la page d'un utilisateur
	if(check_if_we_are_on_user_page())
		change_page_personne(id, idpost); //Chargement rapide de la page de l'utilisateur
	else
	location.href = "index.php?id=" + id + "&post=" + idpost; //Rechargement complet de la page
}

//Fonction d'ouverture d'une page web
function change_webpage(url)
{
	var request = new XMLHttpRequest();
	request.open("GET", url, false);
	request.send(null);

	if (request.status == 200){
		window.history.pushState(document.title,document.title, url);
		document.title = "Comunic";
		
		document.body.parentNode.innerHTML = "";
		
		var page = document.createElement("html");
		page.innerHTML = request.responseText;
		document.body.parentNode.appendChild(page);
		
	} else {
    //quelque chose s’est mal passé
    alert("Erreur: " + request.status + ": " + request.statusText);
  } // fin de if
} // fin de la fonction

//Fonction de réponse à un sonage
function voteSondage(id_sondage, id_personne)
{
	//Récupération du choix de vote
	var id_select_choix_vote = "reponse_sondage_" + id_sondage;
	var list = document.getElementById(id_select_choix_vote);
	var choix_vote = list.options[list.selectedIndex].value;
	
	// Debug only
	// alert(id_sondage + " " + id_personne + " " + choix_vote);
	
	//Envoi de la réponse de l'utilisateur
	var url_reponse = "action.php?actionid=37&id_user=" + id_personne + "&id_sondage=" + id_sondage + "&id_reponse=" + choix_vote+"&type=vote";
	ajax_rapide(url_reponse);
	
	//On enlève le formulaire de vote
	var id_contener_form_choix_reponse = "reponse_sondage_" + id_sondage + "_contener";
	document.getElementById(id_contener_form_choix_reponse).innerHTML = "Votre r&eacute;ponse a &eacute;t&eacute; envoy&eacute;e au serveur. Merci de votre participation &agrave; ce sondage.";
}

/* Graphiques - Camembert */

function draw_camembert(id_array, id_canvas)
{

	///// STEP 0 - setup
    // source data table and canvas tag
    var data_table = document.getElementById(id_array);
    var canvas = document.getElementById(id_canvas);
    var td_index = 1; // which TD contains the data

    ///// STEP 1 - Get the data
    // get the data[] from the table
    var tds, data = [], color, colors = [], value = 0, total = 0;
    var trs = data_table.getElementsByTagName('tr'); // all TRs
    for (var i = 0; i < trs.length; i++) {
        tds = trs[i].getElementsByTagName('td'); // all TDs

        if (tds.length === 0) continue; //  no TDs here, move on

        // get the value, update total
        value  = parseFloat(tds[td_index].innerHTML);
        data[data.length] = value;
        total += value;

        // random color
        color = getColor_random();
        colors[colors.length] = color; // save for later
        trs[i].style.backgroundColor = color; // color this TR
    }


    ///// STEP 2 - Draw pie on canvas
    // exit if canvas is not supported
    if (typeof canvas.getContext === 'undefined') {
        return;
    }

    // get canvas context, determine radius and center
    var ctx = canvas.getContext('2d');
    var canvas_size = [canvas.width, canvas.height];
    var radius = Math.min(canvas_size[0], canvas_size[1]) / 2;
    var center = [canvas_size[0]/2, canvas_size[1]/2];

    var sofar = 0; // keep track of progress
    // loop the data[]
    for (var piece in data) {

        var thisvalue = data[piece] / total;
		
        ctx.beginPath();
        ctx.moveTo(center[0], center[1]); // center of the pie
        ctx.arc(  // draw next arc
            center[0],
            center[1],
            radius,
            Math.PI * (- 0.5 + 2 * sofar), // -0.5 sets set the start to be top
            Math.PI * (- 0.5 + 2 * (sofar + thisvalue)),
            false
        );

        ctx.lineTo(center[0], center[1]); // line back to the center
        ctx.closePath();
        ctx.fillStyle = colors[piece];    // color
        ctx.fill();

        sofar += thisvalue; // increment progress tracker
    }

}

// utility - generates random color
function getColor_random() {
	var rgb = [];
	for (var i = 0; i < 3; i++) {
		rgb[i] = Math.round(100 * Math.random() + 100) ; // [155-255] = lighter colors
	}
	
	//Renvoi du résultat
	return 'rgb(' + rgb.join(',') + ')';
}