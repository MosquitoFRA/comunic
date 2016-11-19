<?php
/**
 * API Server
 *
 * @author Pierre HUBERT
 */

//Security
isset($_SESSION) OR exit("Invalid call ! - ".$_SERVER['PHP_SELF']);

$serviceName = "testService";
$token = "G5AoxG/URyYBDVyLYnsnVpQL0p+JTL7QgOopwQIDAQABAoIBAC7kB2BUVDEm3Dy4";

exit();
//Server framework inclusion
require_once(relativePath_3rdparty("RestServer/RestServer.php"));

//Creating server
$serverState = ($mode_site == "offline" ? "debug" : "production");
$server = new \Jacwright\RestServer\RestServer($serverState);
$server->handle();