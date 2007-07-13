<?php
//INCLUDES
include_once('headerDB.inc.php');

//RETRIEVE URL AND FORM VARIABLES
$values=array();
$values['newitem']=$_POST['newitem'];
$values['newnotes']=$_POST['newnotes'];
$values['checklistId'] = (int) $_POST['checklistId'];
$values['newchecked'] = $_POST['completed']{0};
if($values['newchecked']!="y") $values['newchecked']='n';
$values['checklistItemId'] = (int) $_GET['checklistItemId'];
$values['delete']=$_POST['delete']{0};

//SQL CODE AREA
if($values['delete']=="y")
    query("deletechecklistitem",$config,$values);
else
    query("updatechecklistitem",$config,$values);

nextScreen("checklistReport.php?checklistId={$values['checklistId']}");
include_once('footer.php');
?>
