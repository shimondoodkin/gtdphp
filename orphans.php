<?php
//INCLUDES
include_once('header.php');

//RETRIEVE URL VARIABLES
$values=array();
$result = query("getorphaneditems",$config,$values,$options,$sort);
$dispArray=array();
$maintable=array();
$thisrow=0;
foreach ($result as $row) {
    $maintable[$thisrow]=array();
    $maintable[$thisrow]['id']=$row['itemId'];
    $maintable[$thisrow]['type']=getTypes($row['type']);
    $maintable[$thisrow]['title']=makeclean($row['title']);
    $maintable[$thisrow]['description']=trimTaggedString($row['description'],$config['trimLength']);
    $thisrow++;
}
$dispArray=array(
    'type'=>'Type'
    ,'title'=>'Name'
    ,'description'=>'Description'
    );
$show=array();
foreach ($dispArray as $key=>$val)
    $show[$key]=true;
if ($config['debug'] & _GTD_DEBUG) echo '<pre>Orphans:',print_r($maintable,true),'</pre>';
?>
<h2>Orphaned Items</h2>
<?php if (count($maintable)) { ?>
    <table class="datatable sortable" id="typetable" summary='table of orphans'>
        <?php require('displayItems.inc.php'); ?>
    </table>
<?php } else {
    $message="Nothing was found.";
    nothingFound($message);
}

include_once('footer.php');
?>
