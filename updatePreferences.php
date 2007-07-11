<?php
include_once('ses.php');
if (isset($_POST['theme'])) $_SESSION['theme']=$_POST['theme'];
?><html><head><meta HTTP-EQUIV="Refresh" CONTENT="0; url=index.php"></head></html>
