<?php
//INCLUDES
include_once('header.php');

//FORM DATA COLLECTION AND PARSING
$values=array();
$values['title'] = mysql_real_escape_string($_POST['title']);
$values['note'] = mysql_real_escape_string($_POST['note']);
$values['date'] = $_POST['date'];
$values['repeat'] = (int) $_POST['repeat'];
$values['suppressUntil'] = mysql_real_escape_string($_POST['suppressUntil']);
$values['delete'] = $_POST['delete']{0};
$values['noteId'] = (int) $_GET['noteId'];
$referrer = $_POST['referrer']{0};
$type = $_POST['type']{0};

//CRUDE error checking
if ($values['date']=="") die ('<META HTTP-EQUIV="Refresh" CONTENT="3;url=note.php?type='.$type.'&referrer='.$referrer.'"><p>No date choosen. Note NOT added.</p>');
if ($values['title']=="") die ('<META HTTP-EQUIV="Refresh" CONTENT="3;url=note.php?type='.$type.'&referrer='.$referrer.'"><p>No title. Note NOT added.</p>');


//SQL CODE AREA
if($values['delete']=="y") query("deletenote",$config,$values);
else {
        $testrow = query("testitemrepeat",$config,$values);
        //if repeating, copy result row to new row (new note) with updated due date
        if ($testrow[0]['repeat']!=0) {
            $nextdue=strtotime("+".$testrow[0]['repeat']."day");
            $values['nextduedate']=gmdate("Y-m-d", $nextdue);

            //retrieve item details
            $copyresult = query("selectnote",$config,$values,$options,$sort);
            $values['date']=$values['nextduedate'];
            $values['title']=$copyresult[0]['title'];
            $values['note']=$copyresult[0]['note'];
            $values['repeat']=$copyresult[0]['repeat'];
            $values['suppressUntil']=$copyresult[0]['suppressUntil'];

            //copy data to projects tables with new id
            $result=query("newnote",$config,$values);

            //delete original row
            $result = query("deletenote",$config,$values);
            }

        else query("updatenote",$config,$values);
    }

if ($referrer=="s") echo '<META HTTP-EQUIV="Refresh" CONTENT="0; url=summaryAlone.php" />';
else echo '<META HTTP-EQUIV="Refresh" CONTENT="0; url=listItems.php?type='.$type.'" />';

include_once('footer.php');
?>
