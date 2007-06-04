<?php
require_once("ses.php");
require_once("config.php");
if ($config['debug'])
		error_reporting(E_USER_ERROR);
else/*if (version_compare(PHP_VERSION, "5.0.0", ">="))
		error_reporting( (E_STRICT | E_ALL) ^ E_NOTICE);
else*/
		error_reporting(E_ALL ^ E_NOTICE);
//CONNECT TO DATABASE: this will need modification to connect to other dtabases (use SWITCH)
$connection = mysql_connect($config['host'], $config['user'], $config['pass']) or die ("Unable to connect!");
mysql_select_db($config['db']) or die ("Unable to select database!");

require_once("gtdfuncs.php");
require_once("query.inc.php");
// php closing tag has been omitted deliberately, to avoid unwanted blank lines being sent to the browser
