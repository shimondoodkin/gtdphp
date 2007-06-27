<?php
$values = array();
$thiscat=array();
$field=$_GET['field'];

if (isset($_GET['id'])) {
    $id=(int) $_GET['id'];
    if ($id===0) {
        $thiscat['id']=0;
        $title="Create $field";
        $canDelete=false;
    } else {
        $title="Edit $field";
        $canDelete=true;
    }
} else {
    $id=0;
    $thiscat['id']=false;
    $title="$field List";
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
$thiscat=array();

if (is_array($result)) {
 	$firstcat=0;
 	$nextcat=-1;
    foreach ($result as $checkcat) {
    	$newcat=array();
    	$i=0;
        foreach ($checkcat as $item)
        	$newcat[$keys[$i++]]=$item;
        if (!$firstcat) $firstcat=$newcat['id'];
        if (!$nextcat) $nextcat=$newcat['id'];
        if ($newcat['id']==$id) {
            $thiscat=$newcat;
            $nextcat=0;
        } else $catlist[]=$newcat;
        $count++;
    }
    if (!$nextcat)
        $nextcat=$firstcat;
    else if ($nextcat==-1)
        $nextcat=0;
}
if ($config['debug'] & _GTD_DEBUG) echo "<pre>catlist:",print_r($catlist,true),'</pre>';

?>
