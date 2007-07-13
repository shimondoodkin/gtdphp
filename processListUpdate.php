<?php
include_once('headerDB.inc.php');

//RETRIEVE URL AND FORM VARIABLES
$values=array();
$values['listItemId'] = (int) $_POST['listItemId'];
$values['listId'] = (int) $_GET['listId'];
$completedLis = $_POST['completedLis'];

if(isset($completedLis)){
	$values['date']=date('Y-m-d');
    foreach ($completedLis as $values['completedLi'])
        query("completelistitem",$config,$values);
}

nextScreen("listReport.php?listId={$values['listId']}");
include_once('footer.php');
?>
