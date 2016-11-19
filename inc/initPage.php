<?php
/**
 * Page initiator
 *
 * @author Pierre HUBERT
 */

//Starting session
if(!isset($_SESSION))
	session_start();

//Determine relative path to project
$relativePath = str_replace("inc", "", __DIR__);
if(!defined('RELATIVE_PATH'))
	define('RELATIVE_PATH', $relativePath);

//Inclusion of static configuration
include($relativePath.'inc/config/staticConfig.php');

//Path to 3rdParty elements
if(!defined('THIRD_PARTY_FOLDER'))
	define('THIRD_PARTY_FOLDER', $staticConfig["3rdPartyElementsFolder"]);

//Path to user data
if(!defined('USER_DATA_FOLDER'))
	define('USER_DATA_FOLDER', $staticConfig["userDataFolder"]);

//Path to website core
if(!defined('CORE_PATH'))
	define('CORE_PATH', $staticConfig["coreFolder"]);

//Files inclusion
$filesToInclude = array(

	//Inclusion of configuration
	$relativePath.'inc/config/config.php',

	//Inclusion of helpers
	$relativePath.'inc/helpers/callHelpers.php',

	//Connecting to DataBase
	$relativePath.'connexiondb.php',

	//Inclusion of smiley list
	$relativePath.'inc/liste_smile.php',

	//Pages list
	$relativePath.'inc/config/listPages.php',
);

//Adding function files
foreach($staticConfig['listFunctionsFiles'] as $functionFile)
	require_once($relativePath."inc/functions/".$functionFile.".php");

//Get the list of already loaded files
$filesLoaded = get_included_files();

//Include files
foreach($filesToInclude as $file){
	//Check if it has already been included
	if(!in_array($file, $filesLoaded))
		include($file);
}

//Initializate langue
$lang = detecteinstallelangue();