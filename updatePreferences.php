<?php
require_once('ses.php');
require_once('gtdfuncs.php');
if (isset($_POST['theme'])) $_SESSION['theme']=$_POST['theme'];
nextScreen('index.php');
// php closing tag has been omitted deliberately, to avoid unwanted blank lines being sent to the browser
