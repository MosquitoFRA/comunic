<?php
/**
 * Virtual pages list
 *
 * @author Pierre HUBERT
 */

isset($_SESSION) OR exit("Invalid call - ".$_SERVER['PHP_SELF']);

$pagesList = array();

/**
 * Edit a post
 */
$pagesList['editpost.php'] = array(
	"file" => "homeUser/editPost/editPost.inc.php",
);

/**
 * Search a user
 */
$pagesList['recherche.php'] = array(
	"file" => "searchUser/searchUser.inc.php",
);

/**
 * List opened pages
 */
$pagesList['pagepublique.php'] = array(
	"file" => "openUsers/openUsers.inc.php",
);

/**
 * About the project
 */
$pagesList['about.php'] = array(
	"file" => "about/about.php",
);

/**
 * About Comunic
 */
$pagesList['whatiscomunic.php'] = array(
	"file" => "whatIsComunic/whatiscomunic.inc.php",
);

/**
 * Contact administration
 */
$pagesList['contact.php'] = array(
	"file" => "contact/contact.inc.php",
);

/**
 * Improvement forum
 */
$pagesList['forum.php'] = array(
	"file" => "improveForum/forum.inc.php",
);


/**
 * Share a link
 */
$pagesList['share.php'] = array(
	"file" => "share/share.inc.php",
);

/**
 * Webmail
 */
$pagesList['webmail.php'] = array(
	"file" => "webmail/webmail.inc.php",
);