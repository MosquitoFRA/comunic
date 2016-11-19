<?php
//On vérifie que les informations requises sont présentes
if(!isset($_GET['id']) OR !isset($_GET['key']))
	die('Missing informations !');
	
//Init page
include('../inc/initPage.php');

//On recherche la personne dans la bdd
$sql = "SELECT * FROM utilisateurs WHERE ID = ? AND flux_rss = ?";
$requete = $bdd->prepare($sql);
if(!$requete->execute(array($_GET['id']*1, $_GET['key'])))
	die('An error occured, try again later.<br />');

if(!$infos_user = $requete->fetch())
	die('Les informations donn&eacute;es sont incorrectes ou la cl&eacute; de flux RSS est p&eacute;rim&eacute;. Cr&eacute;ez une nouvelle cl&eacute; dans les param&egrave;tres de votre compte.');
	
//La clé est correcte

//Fermeture du curseur
$requete->closeCursor();

//Searching the last notifications
$notifications = searchnotification($infos_user['ID'], $bdd, 10000, 0, '0');

//Envoi des en-têtes
header("Content-type: application/xml");

$prenom_nom_user = decorrige_caracteres_speciaux($infos_user['prenom']." ".$infos_user['nom']);

//Début de l'envoi du fichier
echo "<"; ?>?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0">
	<channel>
		<title>Notifications de <?php echo $prenom_nom_user; ?></title>
		<link><?php echo $urlsite; ?></link>
		<language>fr</language>
		<copyright>Comunic</copyright>
		<description><?php echo $prenom_nom_user; ?> recevra les notifications non vues de son compte dans ce flux RSS.</description>
		
<?php
			foreach($notifications as $afficher_notification)
			{
				//We search the informations about the personn who created notification
				$info_creator = cherchenomprenom($afficher_notification['ID_createur'], $bdd);
				
				echo "\t\t <item> \n";
				echo " \t\t\t <title>".decorrige_caracteres_speciaux(corrige_caracteres_speciaux($info_creator['prenom']." ".$info_creator['nom']." ".$afficher_notification['message']))."</title> \n";
				
				if(!preg_match('<private_chat:>', $afficher_notification['adresse']) AND $afficher_notification['adresse'] != "")
					echo "\t\t\t <link>".$urlsite.str_replace('page:', '?id=', str_replace('post:', '&post=', $afficher_notification['adresse']))."</link> \n";
				
				echo "\t\t\t <guid>".$afficher_notification['ID']."</guid> \n";
				echo "\t\t\t <description>".decorrige_caracteres_speciaux($info_creator['prenom']." ".$info_creator['nom'])." s'est manifesté(e) dans Comunic. </description> \n";
				echo "\t\t\t <pubDate>".date("r", strtotime($afficher_notification['date_envoi']))."</pubDate> \n";
				
				echo "\t\t </item> \n";
			}
		?>
	</channel>
</rss>