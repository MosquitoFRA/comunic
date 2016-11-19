function getXMLHttpRequest() {
	var xhr = null;
	
	if (window.XMLHttpRequest || window.ActiveXObject) {
		if (window.ActiveXObject) {
			try {
				xhr = new ActiveXObject("Msxml2.XMLHTTP");
			} catch(e) {
				xhr = new ActiveXObject("Microsoft.XMLHTTP");
			}
		} else {
			xhr = new XMLHttpRequest(); 
		}
	} else {
		alert("Votre navigateur ne supporte pas l'objet XMLHTTPRequest...");
		return null;
	}
	
	return xhr;
}


function getinIDajax(file, iddestination)
{
	var xhr = getXMLHttpRequest();
	xhr.onreadystatechange = function() {
			if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) {
				if(xhr.responseText != "0")
				{
					//On joue un son si nécessaire
					if(/lightOlive/.test(xhr.responseText) && document.getElementById(iddestination).innerHTML != "")
					{
						var player_new_mp = document.querySelector('#new_mp');
						player_new_mp.play();
					}
					
					document.getElementById(iddestination).innerHTML = xhr.responseText + document.getElementById(iddestination).innerHTML; // Données textuelles récupérées
				}
			}
	};

	xhr.open("GET", file, true);
	xhr.send(null);
}

/* For private Chat Only */

function submitPrivateChat(file, iddestination)
{
	var xhr = getXMLHttpRequest();
	var message = encodeURIComponent(document.getElementById('message').value);
	document.getElementById('message').value = ""; // on vide le message sur la page
	document.getElementById('message').style.height = "24px"; // On le réduit à sa taille initiale...
	xhr.onreadystatechange = function() {
			if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) {
				if(xhr.responseText != "0")
				{
					document.getElementById(iddestination).innerHTML = xhr.responseText + document.getElementById(iddestination).innerHTML; // Données textuelles récupérées
				}
			}
	};

	xhr.open("POST", file, true);
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xhr.send("message="+message);

}