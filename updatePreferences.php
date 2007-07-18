<?php
require_once('ses.php');
require_once('gtdfuncs.php');
unset($_POST['submit']);
foreach ($_POST as $key=>$val) {
    $_SESSION[$key]=$val;
    setcookie($key,$val,time()+2592000);  // 2,592,000 seconds = 60*60*24*30 = 30 days to cookie expiry
}
nextScreen('index.php');
// php closing tag has been omitted deliberately, to avoid unwanted blank lines being sent to the browser
