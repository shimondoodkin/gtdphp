<?php
//INCLUDES
include_once('headerDB.inc.php');

$html=false; // indicates if we are outputting html

// get core variables first
$values=array();  // ensures that this is a global variable
$values['itemId'] = (int) $_POST['itemId'];
$values['type'] = $_POST['type'];

$action = $_POST['action'];

$updateGlobals=array();
$updateGlobals['multi']    = (isset($_POST['multi']) && $_POST['multi']==='y');
$updateGlobals['referrer'] = $_POST['referrer'];
$updateGlobals['parents'] = $_POST['parentId'];
if (!is_array($updateGlobals['parents'])) $updateGlobals['parents']=array($updateGlobals['parents']);

if (isset($_POST['wasNAonEntry'])) {  // toggling next action status on several items
	$updateGlobals['wasNAonEntry'] = explode(' ',$_POST['wasNAonEntry']);
	$updateGlobals['isNA']=array();
	if (isset($_POST['isNAs'])) $updateGlobals['isNA']=$_POST['isNAs'];
}

if (isset($_POST['isMarked'])) { // doing a specific action on several items (currently, the only option is to complete them)
	$updateGlobals['isMarked']=array();
	$updateGlobals['isMarked']=array_unique($_POST['isMarked']); // remove duplicates
}

// some debugging - if debug is set to halt, dump all the variables we've got

if ($config['debug'] & _GTD_DEBUG) {
    include 'headerHtml.inc.php';
    echo "</head><body><div id='container'>\n";
    $html=true;
	// debugging text - simply dump the variables, and quit, without processing anything
	literaldump('$_GET');
    literaldump('$_POST');
    literaldump('$_SESSION');
	literaldump('$action');
	literaldump('$config');
	literaldump('$values');
	literaldump('$updateGlobals');
	if (isset($updateGlobals['isNA'])) {
		echo '<hr /><pre>array_diff(wasNAonEntry,isNA)';
		print_r(array_diff($updateGlobals['wasNAonEntry'],$updateGlobals['isNA']));
		echo '<br /><hr />array_diff(isNA,wasNAonEntry)';
		print_r(array_diff($updateGlobals['isNA'],$updateGlobals['wasNAonEntry']));
		echo '</pre>';
	}
} // END OF debugging text

$title='';

if ($updateGlobals['multi']) {
	// recursively do actions, looping over items
	if (isset($updateGlobals['wasNAonEntry']) && isset($updateGlobals['isNA'])) {  // toggling next action status on several items
		foreach (array_diff($updateGlobals['wasNAonEntry'],$updateGlobals['isNA']) as $values['itemId']) if ($values['itemId']) doAction('removeNA');
		foreach (array_diff($updateGlobals['isNA'],$updateGlobals['wasNAonEntry']) as $values['itemId']) if ($values['itemId']) doAction('makeNA');
	}
	if (isset($updateGlobals['isMarked'])) { // doing a specific action on several items (currently, the only option is to complete them)
		foreach ($updateGlobals['isMarked'] as $nextItem) {
			$values=array('itemId'=>$nextItem); // reset the $values array each time, so that it only contains itemId
			doAction($action);
		}
	}
} else {
	if ($_POST['delete']==='y') $action='delete'; // override item-update if we are simply deleting
	doAction($action);
}

nextPage();
if ($html)
    include_once('footer.php');
else
    echo '</head></html>';
return;

/*========================================================================================
  main program finished - utility functions from here, below
========================================================================================*/

function doAction($localAction) { // do the current action on the current item; returns TRUE if succeeded, else returns FALSE
	global $config,$values,$updateGlobals,$title;
	if ($values['itemId']) {
        $result=query('getitembrief',$config,$values);
    	if ($result!=-1) $title=$result[0]['title'];
    } else
        $title=$_POST['title'];

    if ($title=='') $title='item '.$values['itemId'];

	if ($config['debug'] & _GTD_DEBUG) echo "<p><b>Action here is: $localAction item {$values['itemId']}</b></p>";
	if ($config['debug'] & _GTD_FREEZEDB) return TRUE;
	switch ($localAction) {
		case 'makeNA':
			makeNextAction();
			$msg="'$title' is now a next action";
			break;
			
		case 'removeNA':
			removeNextAction();
            $msg="'$title' is no longer a next action";
			break;
			
		case 'changeType':
			changeType();
			$newtype=getTypes($values['type']);
			$msg="$newtype is now the type for item: '$title'";
			break;

        case 'create':
			retrieveFormVars();
			createItem();
			$msg="Created item: '$title'";
			break;
			
		case 'complete':
			completeItem();
			$msg="Completed '$title'";
			break;
		
		case 'fullUpdate':
			retrieveFormVars();
			updateItem();
			$msg="Updated '$title'";
			break;
			
		case 'delete':
			deleteItem();
			$msg="Deleted '$title'";
			break;
		
		case 'createbasic': // not in use yet. added for future use, when only title and type are set.
			createItemQuickly();
			$msg="Created item: '$title'";
			break;
			
		default: // failed to identify which action we should be taking, so quit
			return FALSE;
	}
	$_SESSION['message'][] = $msg;
	return TRUE; // we have successfully carried out some action
}

/* ===========================================================================================
	primary action functions
   ================================= */

function deleteItem() { // delete all references to a specific item
	global $config,$values;
	query("deleteitemstatus",$config,$values);
	query("deleteitemattributes",$config,$values);
	query("deleteitem",$config,$values);
	query("deletelookup",$config,$values);
	query("deletelookupparents",$config,$values);
	removeNextAction();
	query("deletenextactionparents",$config,$values);
}

function createItem() { // create an item and its parent-child relationships
	global $config,$values,$updateGlobals,$title;
	//Insert new records
	$result = query("newitem",$config,$values);
	$values['newitemId'] = $GLOBALS['lastinsertid'];
	$result = query("newitemattributes",$config,$values);
	$result = query("newitemstatus",$config,$values);
	setParents('new');
	$title=$values['title'];
}

function createItemQuickly() {// create an item when we only know its type and title - not yet in use - TOFIX still to check
	global $config,$values,$updateGlobals,$title;
	//Insert new records
	$result = query("newitem",$config,$values);
	$values['newitemId'] = $GLOBALS['lastinsertid'];
	setParents('new');
	$title=$values['title'];
}

function updateItem() { // update all the values for the current item
	global $config,$values,$updateGlobals,$title;
	query("deletelookup",$config,$values);
	removeNextAction();
    query("updateitemattributes",$config,$values);
    query("updateitem",$config,$values);
    if ($values['type'] === $values['oldtype'])
    	setParents('update');
    else
        // changing item type - sever child links
    	query("deletelookupparents",$config,$values);

	if ($values['dateCompleted']==='NULL')
		query("touchitem",$config,$values);
	else
		completeItem();
	$title=$values['title'];
}

function completeItem() { // mark an item as completed, and recur if required
	global $config,$values;

	if (!isset($values['dateCompleted'])) $values['dateCompleted']="'".date('Y-m-d')."'";
	
	if (!isset($values['repeat'])) {
		$testrow = query("testitemrepeat",$config,$values);
		$values['repeat']=$testrow[0]['repeat'];
	}
	if ($values['repeat']) recurItem(); else makeComplete();
}

function makeNextAction() { // mark the current item as a next action
	global $config,$values;
	$thisquery='updatenextaction';
    $parentresult = query("lookupparent",$config,$values);
    if ($parentresult=="-1") {
        $values['parentId']=0;
		query($thisquery,$config,$values);
    } else foreach ($parentresult as $parent) {
		$values['parentId']=$parent['parentId'];
		query($thisquery,$config,$values);
	}
	query("touchitem",$config,$values);
}

function removeNextAction() { // remove the next action reference for the current item
	global $config,$values;
	query("deletenextaction",$config,$values);
	query("touchitem",$config,$values);
}

function changeType() {
	global $config,$values;
    $values['type'] = $_POST['type'];
    $values['isSomeday']='n';
    query("updateitemtype",$config,$values);
	query("deletelookup",$config,$values);
	query("deletelookupparents",$config,$values);
	removeNextAction();
	query("deletenextactionparents",$config,$values);
}
/* ===========================================================================================
	utility functions for the primary actions
   =========================================== */

function retrieveFormVars() {
	global $config,$values;

	// key variables
	if (isset($_POST['type']))           $values['type']           = $_POST['type'];
	if (isset($_POST['oldtype']) && $_POST['oldtype']!='') $values['oldtype']        = $_POST['oldtype'];
	if (isset($_POST['title']))          $values['title']          = $_POST['title'];
	if (isset($_POST['description']))    $values['description']    = $_POST['description'];
	if (isset($_POST['desiredOutcome'])) $values['desiredOutcome'] = $_POST['desiredOutcome'];
	if (isset($_POST['categoryId']))     $values['categoryId']     = $_POST['categoryId'];
	if (isset($_POST['contextId']))      $values['contextId']      = $_POST['contextId'];

	// binary yes/no
	$values['nextAction'] = ($_POST['nextAction']==="y")?'y':'n';
	$values['isSomeday']  = ($_POST['isSomeday']==='y')?'y':'n';
	$values['suppress']   = ($_POST['suppress']==='y')?'y':'n';
	$values['delete']     = ($_POST['delete']==='y')?'y':NULL;

	// integers
	if (isset($_POST['suppressUntil'])) $values['suppressUntil']  = (int) $_POST['suppressUntil']; // ($values['suppress']==='n')?0:(int) $_POST['suppressUntil'];
	if (isset($_POST['repeat']))        $values['repeat']         = (int) $_POST['repeat'];
	if (isset($_POST['timeframeId']))   $values['timeframeId']    = $_POST['timeframeId'];

	// dates
	if (isset($_POST['dateCompleted'])) $values['dateCompleted'] = ($_POST['dateCompleted'] ==='')?"NULL":"'{$_POST['dateCompleted']}'";
	if (isset($_POST['deadline']))      $values['deadline']      = ($_POST['deadline']      ==='')?"NULL":"'{$_POST['deadline']}'";

	// crude error checking
	if (!isset($values['title'])) die ("No title. Item NOT added."); // TOFIX

	if ($config['debug'] & _GTD_DEBUG) {
		echo '<hr /><pre><b>retrieved form vars</b><br />';
		literaldump('$values');
		echo '</pre>';
	}
}

function getItemCopy() { // retrieve all the values for the current item, and store in the $values array
	global $config,$values,$updateGlobals;
	$copyresult = query("selectitem",$config,$values,$options,$sort);
	foreach ($copyresult[0] as $key=>$thisvalue) $values[$key]=$thisvalue;
	// now get parents
	$result=query("lookupparent",$config,$values,$options,$sort);
	$updateGlobals['parents']=array();
	if (is_array($result))
		foreach ($result as $parent)
			$updateGlobals['parents'][]=$parent['parentId'];
	if ($config['debug'] & _GTD_DEBUG) {
		echo '<pre>Retrieved record for copying: </pre>';
		literaldump('$values');
		echo '<pre>Parents:',print_r($updateGlobals['parents'],true),'</pre>';
	}
}

function setParents($new) {
    global $config,$values,$updateGlobals;
	if($config['debug'] & _GTD_DEBUG) echo '<pre>',print_r($updateGlobals['parents'],true),'</pre>';
    $markedna=false;
    foreach ($updateGlobals['parents'] as $values['parentId']) if ($values['parentId']) {
    	$result = query($new."parent",$config,$values);
    	if($values['nextAction']==='y') {
            $result = query($new."nextaction",$config,$values);
            $markedna=true;
        }
   	}
    if ($values['nextAction']==='y' && !$markedna) {
        $values['parentId']=0;
        $result = query($new."nextaction",$config,$values);
    }
}

function recurItem() { // mark a recurring item completed, and set up the recurrence
	global $config,$values,$updateGlobals;
	// calculate date to recur to, based on: date completed + number of days between recurrences
	$dateArray=explode("-", str_replace("'",'',$values['dateCompleted']));
	$unixdateCompleted=mktime(12,0,0,$dateArray[1],$dateArray[2],$dateArray[0]);
	$nextdue=strtotime("+".$values['repeat']." day",$unixdateCompleted);

	if ($config['storeRecurrences']) {
		makeComplete();
		getItemCopy();
		if (isset($updateGlobals['isNA']) && in_array($values['itemId'],$updateGlobals['isNA']))
			$values['nextAction']='y';
	}

	$values['dateCompleted']="NULL"; 
	$values['deadline']="'".gmdate("Y-m-d", $nextdue)."'";

	if ($config['storeRecurrences'])
		createItem();
	else {
		query("updatedeadline",$config,$values);
		query("completeitem",$config,$values); // reset completed date to null, and touch the last modified date
	}
}

function makeComplete() { // mark an action as completed, and removes next action marker for it
	global $config,$values;
	query("completeitem",$config,$values);
	removeNextAction();
}

/* ===========================================================================================
	general utility functions that don't modify the database
   ========================================================= */

function nextPage() { // set up the forwarding to the next page
	global $config,$values,$updateGlobals;
	$t = (array_key_exists('oldtype',$values))? $values['oldtype']:$values['type'];
	$key='afterCreate'.$t;
    $id=($values['newitemId'])?$values['newitemId']:$values['itemId'];
    $nextURL='';
    if (isset($_POST['afterCreate'])) {
        $tst=$_POST['afterCreate'];
        $_SESSION[$key]=$_POST['afterCreate'];
    }elseif (isset($updateGlobals['referrer']) && ($updateGlobals['referrer'] !== ''))
		$tst=$updateGlobals['referrer'];
    else
        $tst=$_SESSION[$key];
        
    if ($action=='delete' && $tst=='item') $tst='list';

	switch ($tst) {
		case "parent"  : $nextURL=($updateGlobals['parents'][0])?('itemReport.php?itemId='.$updateGlobals['parents'][0]):('orphans.php'); break;
		case "item"    : $nextURL="itemReport.php?itemId=$id"; break;
		case "another" :
            $nextURL="item.php?";
            if (isset($_SESSION['lastcreate']) && $_SESSION['lastcreate']!='')
                $nextURL.=$_SESSION['lastcreate'];
            else
                $nextURL.="type=$t";
            break;
		case "list"	   : $nextURL="listItems.php?type=$t"; break;
		case "referrer": $nextURL=$_SESSION["lastfilter$t"];break;
        default        : $nextURL=$tst;break;
	}
	if ($config['debug'] & _GTD_DEBUG) {
        echo '<pre>$referrer=',print_r($updateGlobals['referrer'],true),'<br />'
            ,"type={$values['type']}<br />"
            ,'session=',print_r($_SESSION,true),'<br />'
            ,'</pre>';
    }
    if ($nextURL=='') $nextURL="listItems.php?type=$t";
    $_SESSION[$key]=$tst;
    $nextURL=html_entity_decode($nextURL);
	nextScreen($nextURL);
}

function literaldump($varname) { // dump a variable name, and its contents
	echo "<pre><b>$varname</b>=";
	$tst="print_r((isset($varname))?($varname):(\$GLOBALS['".substr($varname,1)."']));return 1;";
	if (eval($tst))
		echo '</pre>';
	else
		echo "<br />Failed to display variable value: $tst <br />";
}

// php closing tag has been omitted deliberately, to avoid unwanted blank lines being sent to the browser
