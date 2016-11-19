<?php
/**
 * Third party Helpers
 *
 *	@author Pierre HUBERT
 */

/**
 * Returns the path to a third party element, from the base of the website
 *
 * @param String $file The file in the 3rd Party
 */
function path_3rdparty($file = ""){
	//Path to an asset
	return THIRD_PARTY_FOLDER.$file;
}

/**
 * Returns the relative path to a third party element, from the base of the website
 *
 * @param String $file The file in the 3rd Party
 */
function relativePath_3rdparty($file = ""){
	//Path to an asset
	return websiteRelativePath().path_3rdparty($file);
}

/**
 * Returns the url of a Third Party element
 *
 * @param 	String $file 	The file in the third party
 * @return 	String 			The URL to the third party
 */
function url_3rdparty($file){
	return siteURL().path_3rdparty($file);
}