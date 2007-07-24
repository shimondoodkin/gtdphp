<?php
require_once("headerDB.inc.php");

if ($_SESSION['version']!==_GTD_VERSION) {
    $testver=query('getgtdphpversion',$config);
    if ($testver==-1 || _GTD_VERSION !== array_pop(array_pop($testver)) ) {
        $_SESSION['message']=array(); // remove warning about version not being found
        nextScreen('install.php?warn=upgradeneeded');
        die;
    } else {
        $_SESSION['version']=_GTD_VERSION;
    }
}

require_once("headerHtml.inc.php");
echo "</head><body><div id='container'>\n";
require_once("headerMenu.inc.php");
echo "<div id='main'>\n";
if ($config['debug'] & _GTD_DEBUG) echo '<br /><pre>Session:',print_r($_SESSION,true),'<hr>Post:',print_r($POST,true),'</pre>';
include_once('showMessage.inc.php');
?>
