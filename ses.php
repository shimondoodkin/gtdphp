<?php
session_start();  
if(isset($_SESSION['views']))
    $_SESSION['views']++;
else{
    $_SESSION['views'] = 1;
    $_SESSION['categoryId'] = 0;
    $_SESSION['contextId'] = 0;
    $_SESSION['theme'] = 'default';
    $_SESSION['message'] = '';
    $_SESSION['version'] = '';
 }

// php closing tag has been omitted deliberately, to avoid unwanted blank lines being sent to the browser
