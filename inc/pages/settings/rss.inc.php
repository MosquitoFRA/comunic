<?php
/**
 * Change RSS settings
 *
 * @author Pierre HUBERT
 */

if(!isset($_SESSION))
	exit("Invalid call !");

//On vérifie la demande d'une génération de clé
if(isset($_GET['generate']))
{
	//Génération de la clé
	if(isset($_GET['delete']))
		$cle = "";
	else
		$cle = sha1(time().$_SESSION['ID'].$afficher['last_activity']);
	
	//Enregistrement de la clé
	$sql = "UPDATE utilisateurs SET flux_rss = ? WHERE ID = ?";
	$modif = $bdd->prepare($sql);
	if($modif->execute(array($cle, $_SESSION['ID'])))
	{
		//Message de succès
		?><script type="text/javascript">affiche_notification_succes("La cl&eacute; du flux RSS a &eacute;t&eacute; modifi&eacute;e avec succ&egrave;s.");</script><?php
		
		//Rechargement des informations
		$afficher = cherchenomprenom($_SESSION['ID'], $bdd);
	}
	else
	{
		?><script type="text/javascript">affiche_notification_succes("La modification de la cl&eacute; du flux RSS a &eacute;chou&eacute;. Veuillez r&eacute;essayer.");</script><?php
	}
}

?><h3>Flux RSS</h3>
<p>Vous permet de t&eacute;l&eacute;charger les notifications de Comunic sur votre logiciel de gestion de flux.</p>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>?c=<?php echo $_GET['c']; ?>&generate" method="post">
	<input type="submit" value="<?php echo ($afficher['flux_rss'] == "" ? "G&eacute;n&eacute;rer" : "Reg&eacute;n&eacute;rer"); ?> une cl&eacute; de flux RSS" />
</form>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>?c=<?php echo $_GET['c']; ?>&generate&delete" method="post">
	<input type="submit" class="danger" value="Supprimer la cl&eacute; de flux RSS" />
</form>

<p><u>Note :</u> Lorsque vous reg&eacute;n&eacute;rez une nouvelle cl&eacute;, l'ancienne cl&eacute; est &eacute;cras&eacute; et n'est plus utilisable.</p>

<p>Adresse actuelle du flux :</p>

<?php
if($afficher['flux_rss'] == "")
	//Il n'y a pas de clé
	echo "Aucune cl&eacute; pour le moment.<br />";
else
{
	//Génération de l'adresse
	$adresse_flux = $urlsite."rss.php?id=".$_SESSION['ID']."&key=".$afficher['flux_rss'];
	
	echo "<p>Cette adresse vous permet d'acc&eacute;der au flux : <i><a href='".$adresse_flux."' target='_blank'>".$adresse_flux."</a></p>";
}