<?php
/**
 * Listing of settings pages
 *
 * @author Pierre HUBERT
 */

if(!isset($_SESSION))
	exit("Invalid call !");

//Settings list
$settingsList = array(
	//Left side menu
	"settingsMenu" => array("file" => "settingsMenu.inc.php"),

	//General settings
	"general" => array("file" => "general.inc.php"),

	//RSS personnal settings
	"rss" => array("file" => "rss.inc.php"),

	//People groups settings
	"groupe_personnes" => array("file" => "groupePersonnes.inc.php"),

	//Multi-auth settings
	"multi_login" => array("file" => "multiLogin.inc.php"),

	//Piwik configuration settings
	"piwik" => array("file" => "piwik.inc.php"),

	//Multi-pages settings (not released yet)
	"pages" => array("file" => "pages.inc.php"),

	//Appearance settings
	"apparence" => array("file" => "appearance.inc.php"),

	//Notifications settings
	"notifications" => array("file" => "notifications.inc.php"),

	//Personnal URL settings
	"repertoire" => array("file" => "repertoire.inc.php"),

	//Password settings
	"password" => array("file" => "password.inc.php"),

	//Avatar settings
	"avatar" => array("file" => "avatar.inc.php"),

	//Background image settings
	"imgfond" => array("file" => "backgroundImg.inc.php"),

	//Chat settings
	"chat" => array("file" => "chat.inc.php"),

	//Clean account settings
	"clean_account" => array("file" => "cleanAccount.inc.php"),

	//Change avatar visibility settings
	"visibilite_avatar" => array("file" => "avatarVisibility.inc.php"),

	//Export personnal datas
	"exportinfo" => array("file" => "exportData.inc.php"),

	//No page found whith the current request
	"error" => array("file" => "error.inc.php"),
);