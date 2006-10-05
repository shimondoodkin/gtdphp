<?php
include_once('header.php');

$connection = mysql_connect($host, $user, $pass) or die ("Unable to connect!");
mysql_select_db($db) or die ("Unable to select database!");

//GET URL AND FORM DATA
$values['categoryId'] = (int) $_GET['categoryId'];
$values['category']=mysql_real_escape_string($_POST['category']);
$values['description']=mysql_real_escape_string($_POST['description']);
$values['delete']=$_POST['delete']{0};
$values['newCategoryId']=(int) $_POST['newCategoryId'];

if ($values['delete']=="y") {
    query("reassigncategory",$config,$values);
    query("deletecategory",$config,$values);
	}

else query("updatecategory",$config,$values);


echo '<META HTTP-EQUIV="Refresh" CONTENT="0; url=listProjects.php"';
echo "Number of Records Updated: ";
echo mysql_affected_rows();

mysql_close($connection);
include_once('footer.php');
?>
