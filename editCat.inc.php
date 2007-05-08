<?php
$values = array();
if (isset($_GET['categoryId'])) {
    $thisone=(int) $_GET['categoryId'];
    $query='categoryselectbox';
    $field='category';
} else if (isset($_GET['contextId'])) {
    $thisone=(int) $_GET['contextId'];
    $query='spacecontextselectbox';
    $field='context';
} else if (isset($_GET['timecontextId'])) {
    $thisone=(int) $_GET['timecontextId'];
    $query='timecontextselectbox';
    $field='time-context';
} else {
    $thisone='';
    $query='';
}
$result = query($query,$config,$values,$options,$sort);
$catlist=array();
$count=0;
$keys=array('id','name','description');
foreach ($result as $checkcat) {
	$i=0;
	$newcat=array();
    foreach ($checkcat as $item)
    	$newcat[$keys[$i++]]=$item;
    if ($newcat['id']==$thisone)
        $thiscat=$newcat;
    else $catlist[]=$newcat;
    $count++;
}
if ($config['debug'] & _GTD_DEBUG) echo "<pre>catlist:",print_r($catlist,true),'</pre>';
?>
