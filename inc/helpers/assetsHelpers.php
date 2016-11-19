<?php
/**
 * Assets Helpers
 *
 *	@author Pierre HUBERT
 */

/**
 * Returns the path to an asset
 *
 * @param String $file The file researched in the assets
 */
function path_assets($file = ""){
	//Path to an asset
	return siteURL()."assets/".$file;
}

/**
 * Returns the relative path to an asset
 *
 * @param String $file The file researched in the assets
 */
function relativePath_assets($file = ""){
	//Path to an asset
	return websiteRelativePath()."assets/".$file;
}


/**
 * Returns the path to an CSS asset
 * 
 * @param String $CSSfile The CSS file
 */
function path_css_asset($CSSfile = ""){
	return path_assets('css/'.$CSSfile);
}

/**
 * Returns the path to an JS asset
 * 
 * @param String $CSSfile The JS file
 */
function path_js_asset($JSfile = ""){
	return path_assets('js/'.$JSfile);
}

/**
 * Returns the path to an Image asset
 * 
 * @param String $IMGfile The IMG file
 */
function path_img_asset($IMGfile = ""){
	return path_assets('img/'.$IMGfile);
}

/**
 * Returns the path to an audio asset
 * 
 * @param String $AUDIOfile The audio file
 */
function path_audio_asset($AUDIOfile = ""){
	return path_assets('audio/'.$AUDIOfile);
}

/**
 * Returns the source code to call a css file
 *
 * @param String $file The css file to call
 */
function code_inc_css($file){
	return '<link rel="stylesheet" href="'.$file.'" />';
}

/**
 * Returns the source code to call a javascript file
 *
 * @param String $file The javascript file to call
 */
function code_inc_js($file){
	return '<script type="text/javascript" src="'.$file.'"></script>';
}

/**
 * Returns the source code to include an image
 *
 * @param String $file     	The image file to call
 * @param String $name     	Optionnal - The name of the image
 * @param String $width    	Optionnal - The width of the image
 * @param String $height   	Optionnal - The height of the image
 * @param String $style    	Optionnal - The style attached to the image
 * @param String $onClick	Optionnal - What to do once image is clicked
 * @param String $class 	Optionnal - The class of the image
 */
function code_inc_img($file, $name = "", $width = "", $height = "", $style = "", $onClick="", $class = ""){
	if($width != "")
		$width = " width='".$width."' ";

	if($height != "")
		$height = " height='".$height."' ";

	if($style != "")
		$style = " style='".$style."' ";

	if($onClick != "")
		$onClick = ' onClick="'.$onClick.'" ';

	if($class != "")
		$class = ' onClick="'.$class.'" ';

	//Returning result
	return '<img src="'.$file.'" name="'.$name.'" alt="'.$name.'" '.$width.$height.$style.$onClick.$class.' />';
}
