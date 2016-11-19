<!-- Pied de page -->
<div class='bottom'>
	<!--<a href='index.php?lang=fr'>Fran&ccedil;ais</a>
	<a href='index.php?lang=en'>English</a>-->
	<a href='<?php echo $urlsite; ?>about.php'><?php echo $lang[9]; ?></a>
	<a href='<?php echo $urlsite; ?>aide.php'><?php echo $lang[10]; ?></a>
	<a href="<?php echo $urlsite; ?>contact.php">Contact</a>
	<!--<a href='pagepublique.php'><?php echo $lang[11]; ?></a>-->
	<a href='<?php echo $urlsite; ?>recherche.php'>Recherche</a>
	<a href='<?php echo $urlsite; ?>tools' target="_blank">Outils</a>
	<a href='<?php echo $urlsite; ?>index.php?id=<?php echo $ID_official_page; ?>'>Page officielle</a>
	<a href='<?php echo $urlsite; ?>index.php?id=<?php echo $ID_actuality_page; ?>'>Actualit&eacute; monde</a>
	<a href='<?php echo $urlsite; ?>whatiscomunic.php'>Qu'est-ce que Comunic ?</a>
	<a href='<?php echo $urlsite; ?>developer/'>Developer</a>
	<a href="forum.php">Am&eacute;liorations</a>
</div>
<!--<div class="metions_legales">
	Une production de Pierre HUBERT <br />
	&copy; Comunic, 2013, 2016
</div>-->
<!-- Fin de: pied page -->
<?php
//Si l'utilisateur est connecté, on inclus le chat publique ou chat privé
if(isset($_SESSION['ID']))
{
	//Vérification de l'activation du chat publique
	if($activer_publique_chat == "oui")
	{
		//Inclusion du chat
		include(websiteRelativePath().'chat.php');
	}
	
	//Inclusion du fichier du chat privé
	include(websiteRelativePath().'inc/private_chat.php');
}

//On vérifie si l'utilisateur est au courant que des cookies sont enregitrés lors de sa visite (inutile pour le moment)
if(!isset($_COOKIE['ok_message_cookie']) AND false)
{
	?><div class="metro"><?php
	?><p class="bg-lighterBlue padding20 fg-white info_cookies" id="info_cookies"><?php
	?>Lors de votre navigation sur ce site quelques cookies sont enregistr&eacute;s. <a href="#">Cliquez ici</a> pour obtenir plus d'informations. <input type="button" value="OK" onClick="ajax_rapide('action.php?actionid=30'); show_hide_id('info_cookies', 'hidden');" /><?php
	?></p><?php
	?></div><?php
}
?>