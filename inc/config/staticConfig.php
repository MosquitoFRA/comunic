<?php
/**
 * Static configuration of website
 * contains every elements which may not require to be changed
 * when Comunic is installed on a new webserver
 *
 * @author Pierre HUBERT
 */

$staticConfig = array();

/**
 * Path to 3rd party elements
 */
$staticConfig["3rdPartyElementsFolder"] = "3rdparty/";

/**
 * Path to user data
 */
$staticConfig["userDataFolder"] = "user_data/";

/**
 * Path the core elements of website
 */
$staticConfig['coreFolder'] = "inc/core/";

/**
 * List of function files
 */
$staticConfig['listFunctionsFiles'] = array("sql", "main", "url", "api");