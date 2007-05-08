<?php
include_once('header.php');
if ($config['debug'] & _GTD_DEBUG)
    echo '<pre>',print_r($_POST,true),'</pre>';
//GET URL AND FORM DATA
$values=array();
$values['id']=(int) $_POST['id'];
$values['name']=$_POST['name'];
$values['description']=$_POST['description'];
$values['newId']=(int) $_POST['replacewith'];
$next=$_POST['submit'];
switch ($_POST['field']) {
    case 'category'    :$query='category'    ;$getId='category'   ;break;
    case 'context'     :$query='spacecontext';$getId='context'    ;break;
    case 'time-context':$query='timecontext' ;$getId='timecontext';break;
    default:break;
}
if ($_POST['delete']=="y") {
    query("reassign$query",$config,$values);
    query("delete$query",$config,$values);
} else
    query("update$query",$config,$values);
    
$nexturl=($next=='Update')?'reportContext.php':"editCat.php?{$getId}Id=$next";
echo nextScreen($nexturl,$config);
include_once('footer.php');
?>
