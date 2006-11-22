<?php
//INCLUDES
include_once('header.php');

//FORM DATA COLLECTION AND PARSING
$values=array();
$values['title'] = mysql_real_escape_string($_POST['title']);
$values['note'] = mysql_real_escape_string($_POST['note']);
$values['date'] = $_POST['date'];
$values['repeat'] = (int) $_POST['repeat'];
$values['delete'] = $_POST['delete']{0};
$values['noteId'] = (int) $_GET['noteId'];
$referrer = $_POST['referrer']{0};
$type = $_POST['type']{0};

//CRUDE error checking
if ($values['date']=="") die ('<META HTTP-EQUIV="Refresh" CONTENT="3;url=note.php?type='.$type.'&referrer='.$referrer.'"><p>No date choosen. Note NOT added.</p>');
if ($values['title']=="") die ('<META HTTP-EQUIV="Refresh" CONTENT="3;url=note.php?type='.$type.'&referrer='.$referrer.'"><p>No title. Note NOT added.</p>');


//SQL CODE AREA
if($values['delete']=="y") query("deletenote",$config,$values);
else query("updatenote",$config,$values);

if ($referrer=="s") echo '<META HTTP-EQUIV="Refresh" CONTENT="0; url=summaryAlone.php" />';
else echo '<META HTTP-EQUIV="Refresh" CONTENT="0; url=listItems.php?type='.$type.'" />';

include_once('footer.php');
?>
