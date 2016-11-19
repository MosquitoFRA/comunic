<?php
/**
 * Private chat
 *
 * @author Pierre HUBERT
 */

//Vérification de l'appel du fichier
if(!isset($_SESSION['ID']))
	die();

//Chargement des informations personnelles pour le chat privé
$afficher = cherchenomprenom($_SESSION['ID'], $bdd);
		
// Required files for private chat inclusion
echo code_inc_css(path_css_asset('private_chat_contener.css'));
echo code_inc_js(path_js_asset('panneau_private_chat.js')); 

	if($afficher['view_private_chat'] == "1")
		$show = true;
	
	//Balise d'ouverture
	echo '<div id="private_chat_contener" style="visibility: '.(isset($show) ? "visible" : "hidden").'; height: '.$afficher['height_private_chat'].'px;">';
	
	//Contenu du chat
		//Conteneur des conversations
		echo '<div id="conversations_private_chat" class="metro" style="height: '.$afficher['height_private_chat'].'px;">';
		echo '</div>';
	
		//Panneau de droite
		echo '<div class="panneau_fonctions_private_chat">';
			echo code_inc_img(path_img_asset('small/arrow_up.png'), "Augmenter la hauteur du chat", "", "", "", "up_down_panneau_private_chat('up');");
			echo code_inc_img(path_img_asset('small/comment_add.png'), "Ajouter une conversation", "", "", "", "affiche_chat_prive(0);");
			echo code_inc_img(path_img_asset('small/cross.png'), "Cacher le chat", "", "", "", "show_hide_panneau_private_chat(0);");
			echo code_inc_img(path_img_asset('small/arrow_down.png'), "Baisser la hauteur du chat", "", "", "", "up_down_panneau_private_chat('down');");
		echo "</div>";
	
	//Balise de fermeture
	echo '</div>';
	
	//Bouton d'ouverture du chat
	echo '<div id="private_chat_open_button" style="visibility: '.(!isset($show) ? "visible" : "hidden").'">';
	
	//Contenu du bouton
	?><div class="img-private-chat" onClick="show_hide_panneau_private_chat(1); refresh_fenetres_chat();" title="Ouvrir le chat priv&eacute;" ></div><?php
	
	//Balise de fermeture
	echo '</div>';
	
	//Chargement des conversations (si ouvert)
	if($afficher['view_private_chat'] == "1")
		echo "<script type='text/javascript'>refresh_fenetres_chat();</script>";
	
	//Panneau de découverte du chat privé
	?><div id="discover_private_chat">
		<?php echo code_inc_img(path_img_asset('discover_private_chat.png')); ?>
		<div class="close_discover_private_chat">
			<input type="button" onClick="show_hide_id('discover_private_chat', 'hidden'); show_hide_panneau_private_chat(1); refresh_fenetres_chat();" value="Fermer">
		</div>
	</div><?php
	