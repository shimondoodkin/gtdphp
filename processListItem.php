<?php
//INCLUDES
include_once('headerDB.inc.php');

//RETRIEVE URL AND FORM VARIABLES
$values=array();
$values['listId']=(int) $_POST['listId'];
$values['item']=$_POST['item'];
$values['notes']=$_POST['notes'];

$result = query("newlistitem",$config,$values);
nextScreen("listReport.php?listId={$values['listId']}");

include_once('footer.php');

?>
