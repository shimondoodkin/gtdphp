<?php
//INCLUDES
include_once('headerDB.inc.php');

//RETRIEVE URL AND FORM VARIABLES
$values=array();
$values['checklistId'] = (int) $_GET['checklistId'];
$values['newchecklistTitle']=$_POST['newchecklistTitle'];
$values['newcategoryId']=(int) $_POST['newcategoryId'];
$values['newdescription']=$_POST['newdescription'];
$values['delete']=$_POST['delete']{0};

if($values['delete']=="y") {
    query("deletechecklist",$config,$values);
    query("removechecklistitems",$config,$values);
} else
    query("updatechecklist",$config,$values);

nextScreen("listChecklist.php?checklistId={$values['checklistId']}");
include_once('footer.php');
?>
