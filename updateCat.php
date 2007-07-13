<?php
include_once('headerDB.inc.php');
$html=false;
if ($config['debug'] & _GTD_DEBUG) {
    $html=true;
    include_once('headerHtml.inc.php');
    echo "</head><body><div id='container'><pre>\n",print_r($_POST,true),"</pre>\n";
}

$values=array();
$field=$_POST['field'];

if (isset($_POST['id'])) {
    $values['id']=(int) $_POST['id'];
    $values['name']=$_POST['name'];
    $values['description']=$_POST['description'];
    $values['newId']=(int) $_POST['replacewith'];
    switch ($field) {
        case 'category':
            $query='category';
            $getId='category';
            break;
        case 'context':
            $query='spacecontext';
            $getId='context';
            break;
        case 'time-context':
            $query='timecontext';
            $getId='timecontext';
            if ($config['useTypesForTimeContexts'] && isset($_POST['type']) && $_POST['type']!='')
                $values['type']=$_POST['type'];
            else
                $values['type']='a';
            break;
        default:
            break;
    }
    if ($values['id']==0) {
        // create an item - first need to check for non-blank names
        if ($values['name']!='') {
            $result = query("new$query",$config,$values);
            // just created an item, without selecting another item for editing, so offer to create another one
            $msg='Created';
        }
    } elseif ($_POST['delete']==="y") {
        query("reassign$query",$config,$values);
        query("delete$query",$config,$values);
        $msg='Deleted';
    } else {
        query("update$query",$config,$values);
        $msg='Updated';
    }
} // end of: if (isset($_POST['id']))
if ($GLOBALS['ecode']=="0") $_SESSION['message'][]="$msg $field '{$values['name']}'";

$nexturl="editCat.php?field=$field";
if (isset($_POST['next']))
    $nexturl.='&amp;id='.$_POST['next'];
nextScreen($nexturl);

if ($html)
    include_once('footer.php');
else
    echo '</head></html>';

// php closing tag has been omitted deliberately, to avoid unwanted blank lines being sent to the browser
