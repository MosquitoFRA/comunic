<?php
	//Safety
	include('securite.php');
	
	//Init page
	include('inc/initPage.php');
	
	//International
	$lang = detecteinstallelangue();
	
	//Info:
	 // Table :
			// Name: chatprive
			// Content :
				// ID : Unique ID of the post
				// date_envoi : Date of the post
				// ID_personne : Who has sent the post
				// ID_destination : To who is sent the post
				// contenu : content of the post
			//Is accessible by privatechat.php
			//See Pierre Hubert for more informations.s
	
	//Get ready
	$headerprivatechat = '<link href="'.$urlsite.'css/privatechat.css" rel="stylesheet" type="text/css" />
		<link href="'.$urlsite.'css/metro-bootstrap_2.css" rel="stylesheet">';
	
	if(!isset($_GET['id']))
	{
		echo "<b>Error : OK for chat but with who ?</b>";
		die();
	}
	else
	{
		//Définition de l'ID de l'autre personne
		$id_other = $_GET['id']*1;
	}
	
	if($_SESSION['ID'] == $id_other)
	{
		echo "<b>Error: You can't chat with you !</b>";
		die();
	}
	elseif($id_other < 1)
	{
		echo "<b>Error : File stopped for security reasons.</b>";
		die();
	}
	
	$info_remote = cherchenomprenom($id_other, $bdd);
	
?><!DOCTYPE html>
<html>
	<head>
		<title><?php echo $lang[65]; ?></title>
		<?php echo $headerprivatechat; ?>
		<?php include(pagesRelativePath('common/head.php')); ?>
	</head>
	<body class="metro">
		<?php include(pagesRelativePath('common/pageTop.php')); ?>
		
		<div class="nouveau_corps_page">
			<h2 class="titre">Chat priv&eacute; avec <?php echo $info_remote['prenom']." ".$info_remote['nom']; ?></h2>
			
			<table id="contentpost" width="90%">
				<?php
				//Get the content of the chat
				$content_chat = get_content_private_chat($_SESSION['ID'], $id_other, $bdd, 10000, '*', $urlsite);
			
				//Show content of the private chat
				foreach($content_chat as $show)
				{
					
					//We show the content of the chat
					echo "<tr><td>";
					
					if($show['ID_personne'] == $_SESSION['ID'])
					{
						?><div class="notice marker-on-right bg-lightBlue fg-white">
							<?php echo corrige_accent_javascript(afficher_lien($show['contenu'])); ?>
						</div>
						<?php
					}
					else
					{
						?><div class="notice marker-on-left bg-lightOlive fg-white">
							<?php echo corrige_accent_javascript(afficher_lien($show['contenu'])); ?>
						</div>
						<?php
					}
					
					echo "</tr>"; 
				} ?>
			</table>
		</div>
		
		<hr />
		<?php include(pagesRelativePath('common/pageBottom.php')); ?>
	</body>
</html>