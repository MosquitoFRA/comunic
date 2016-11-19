<?php
	//Sécurité
	if(!isset($_SESSION['ID']))
		die("Login required.");
	if(!isset($code_session_private_chat))
		die("404 File not found.");
	
	//On récuère la la liste si elle est disponible
	if(isset($_SESSION['private_chat']))
	{
		if(isset($_SESSION['private_chat'][$_SESSION['ID']]))
		{
			//On affiche toute les conversations de manière méthodique
			foreach($_SESSION['private_chat'][$_SESSION['ID']] as $id=>$nothing)
			{
				//Récupération des informations de la personne
					$info_personne = cherchenomprenom($id, $bdd);
					
					echo "<div class='une_conversation_private_chat window'>";
						echo "<div class='header_une_conversation_private_chat caption'>";
							echo  '<span class="icon icon-comments-2"></span>';
							echo "<div class='title'>".corrige_caracteres_speciaux($info_personne['prenom']." ".$info_personne['nom'])."</div>";
							echo " <button class='btn-close' onClick='close_conversation(".$id.");'></button>";
						echo "</div>";
						echo "<div class='content'>";
						echo "<iframe src='".$urlsite."privatechat.php?notitle=1&screen=chat&id=".$id."' class='iframe_une_conversation_private_chat'></iframe>";
						echo "</div>";
					echo "</div>";
			}
		}
	}
	