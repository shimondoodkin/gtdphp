<?php
include_once('header.php');

$connection = mysql_connect($host, $user, $pass) or die ("Unable to connect!");
mysql_select_db($db) or die ("Unable to select database!");

//GET URL AND FORM DATA
$values['tcId'] = (int) $_GET['tcId'];
$values['name']=mysql_real_escape_string($_POST['timeframe']);
$values['description']=mysql_real_escape_string($_POST['description']);
$values['delete']=$_POST['delete']{0};
$values['ntcId']=(int) $_POST['ntcId'];

if ($values['delete']=="y") {
        query("reassigntimecontext",$config,$values,$options,$sort);
        query("deletetimecontext",$config,$values,$options,$sort);
        }

else query("updatetimecontext",$config,$values,$options,$sort);

echo '<META HTTP-EQUIV="Refresh" CONTENT="0; url=reportContext.php"';
//echo "Number of Records Updated: ";
//echo mysql_affected_rows();

mysql_close($connection);
include_once('footer.php');
?>
