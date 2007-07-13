<?php
//INCLUDES
include_once('headerDB.inc.php');

//RETRIEVE FORM URL VARIABLES
$values=array();
$values['listId'] = (int) $_GET['listId'];
$values['newlistTitle']=$_POST['newlistTitle'];
$values['newcategoryId']=(int) $_POST['newcategoryId'];
$values['newdescription']=$_POST['newdescription'];
$values['delete']=$_POST['delete']{0};

//SQL CODE AREA
if($values['delete']=="y") {
    query("deletelist",$config,$values);
    $_SESSION['message'][]=mysql_affected_rows().' lists deleted';
    query("removelistitems",$config,$values);
    $_SESSION['message'][]=mysql_affected_rows().' list items deleted';
    $url='listList.php';
}else {
    query("updatelist",$config,$values);
    $_SESSION['message'][]="List {$values['newlistTitle']} updated";
    $url="listReport.php?listId={$values['listId']}";
}
nextScreen($url);
include_once('footer.php');
?>
