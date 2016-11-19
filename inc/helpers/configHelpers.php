<?php
/**
 * Config Helpers
 *
 *	@author Pierre HUBERT
 */

/**
 * Define constants for config
 */
define('URL_SITE', $urlsite);


/**
 * Return the path to config
 *
 * @param  String 	$file 	Optionnal - The file researched in website
 * @return String 			The path to the config
 */
function siteURL($file = ""){
	//Path to an asset
	return URL_SITE.$file;
}

/**
 * Returns the relative path to the website
 *
 * @param 	String 	$file 	Optionnal - The file searched in the website
 * @return 	String 			The relative path to the website
 */
function websiteRelativePath($file=""){
	return RELATIVE_PATH.$file;
}

/**
 * Returns the relative path to pages folder
 *
 * @param 	String 	$file 	Optionnal - The file searched in the website
 * @return 	String 			The relative path to the folder path
 */
function pagesRelativePath($file=""){
	return RELATIVE_PATH."inc/pages/".$file;
}

/**
 * Returns the path to user data
 *
 * @param 	String 	$file 	Optionnal - The file to search in user data
 * @return 	String 			The path to user data (from the URI of website)
 */
function userDataFolder($file=""){
	return USER_DATA_FOLDER.$file;
}


/**
 * Returns relative path to user data
 *
 * @param 	String 	$file 	Optionnal - file to search in user data
 * @return 	String 			Path to user datas
 */
function relativeUserDataFolder($file=""){
	return websiteRelativePath().userDataFolder($file);
}

/**
 * Returns web path to user data
 *
 * @param 	String 	$file 	Optionnal - file to search in user data
 * @return 	String 			Path to user datas
 */
function webUserDataFolder($file=""){
	return siteURL().userDataFolder($file);
}

/**
 * Returns relative path to the core elements of the website
 *
 * @param 	String 	$file 	Optionnal - file researched in the core of website
 * @return 	String 			Relative path to the core of website
 */
function websiteCoreFolder($file = ""){
	return websiteRelativePath().CORE_PATH.$file;
}