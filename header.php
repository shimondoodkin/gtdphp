<?php
require_once("headerDB.inc.php");
require_once("headerHtml.inc.php");
echo "</head><body><div id='container'>\n";
require_once("headerMenu.inc.php");
echo "<div id='main'>\n";
if ($config['debug'] & _GTD_DEBUG) echo '<br /><pre>Session:',print_r($_SESSION,true),'<hr>Post:',print_r($POST,true),'</pre>';
include_once('showMessage.inc.php');
?>
