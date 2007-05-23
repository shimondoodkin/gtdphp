<?php
$values = array();
$thiscat=array();
$field=$_GET['field'];

if (isset($_GET['id'])) {
    $id=(int) $_GET['id'];
    if ($id===0) {
        $thiscat['id']=0;
        $title="Create $field";
        $prefix='Create this item and then ';
        $canDelete=false;
    } else {
        $title="Edit $field";
        $prefix='Save changes and then ';
        $canDelete=true;
    }
} else {
    $id=0;
    $thiscat['id']=false;
    $title="$field List";
    $prefix='';
    $canDelete=false;
}

$keys=array('id','name','description');
switch ($field) {
    case 'category':
        $query='categoryselectbox';
        $showTypes=false;
        break;
    case 'context':
        $query='spacecontextselectbox';
        $showTypes=false;
        break;
    case 'time-context':
        $query='timecontextselectbox' ;
        $values['timefilterquery'] = '';
        if ($config['useTypesForTimeContexts']) {
            $showTypes=true;
            $keys[]='type';
        } else {
            $showTypes=false;
        }
        break;
    default:
        $query='';
        $showTypes=false;
        break;
}
$result = query($query,$config,$values,$options,$sort);
$catlist=array();
$count=0;

foreach ($result as $checkcat) {
	$i=0;
	$newcat=array();
    foreach ($checkcat as $item)
    	$newcat[$keys[$i++]]=$item;
    if ($newcat['id']==$id)
        $thiscat=$newcat;
    else $catlist[]=$newcat;
    $count++;
}
if ($config['debug'] & _GTD_DEBUG) echo "<pre>catlist:",print_r($catlist,true),'</pre>';
// php closing tag has been omitted deliberately, to avoid unwanted blank lines being sent to the browser
