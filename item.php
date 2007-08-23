<?php
//INCLUDES
include_once('header.php');

$values = array();
$values['itemId']= (int) $_REQUEST['itemId'];
$values['parentId']=array();

//SQL CODE
if ($values['itemId']) { // editing an item
    $where='edit';
    $result = query("selectitem",$config,$values,$options,$sort);
    if ($GLOBALS['ecode']==0) {
        $values = $result[0];
        //Test to see if nextaction
        $result = query("testnextaction",$config,$values,$options,$sort);
        $nextaction= ($result!="-1" && $result[0]['nextaction']==$values['itemId']);
    } else {
        echo "<p class='error'>Failed to retrieve item {$values['itemId']}</p>";
        return;
    }
} else { // creating an item
    $where='create';
    $nextaction=false;
    //RETRIEVE URL VARIABLES
    $values['type']=$_REQUEST['type'];
    if ($values['type']==='s') {
        $values['isSomeday']='y';
        $values['type']='p';
    } elseif ($values['type']==='n') {
        $nextaction=true;
        $values['type']='a';
    }
}
$show=getShow($where,$values['type']);
if (!$values['itemId']) {
    if ($_GET['someday']=='true') $values['isSomeday']='y';

    if ($show['suppress'] && ($_REQUEST['suppress']=='true' || $_REQUEST['suppress']==='y')) {
        $values['suppress']='y';
        $values['suppressUntil']=$_REQUEST['suppressUntil'];
    }
    if ($show['NA']       && ($_REQUEST['nextonly']=='true' || $_REQUEST['nextonly']==='y')) $nextaction=true;
    if ($show['deadline'] && !empty($_REQUEST['deadline']))$values['deadline']=$_REQUEST['deadline'];
    if ($show['ptitle']   && !empty($_REQUEST['parentId'])) $values['parentId'] = explode(',',$_REQUEST['parentId']);

    foreach ( array('category','context','timeframe') as $cat)
        if ($show[$cat]) $values[$cat.'Id']= (int) $_REQUEST[$cat.'Id'];
}

//determine item and parent labels
switch ($values['type']) {
    case "m" : $typename="Value"; $parentname=""; $values['ptype']=""; break;
    case "v" : $typename="Vision"; $parentname="Value"; $values['ptype']="m"; break;
    case "o" : $typename="Role"; $parentname="Vision"; $values['ptype']="v"; break;
    case "g" : $typename="Goal"; $parentname="Role"; $values['ptype']="o"; break;
    case "p" : $typename="Project"; $parentname="Goal"; $values['ptype']="g"; break;
    case "a" : $typename="Action"; $parentname="Project"; $values['ptype']="p"; break;
    case "w" : $typename="Waiting On"; $parentname="Project"; $values['ptype']="p"; break;
    case "r" : $typename="Reference"; $parentname="Project"; $values['ptype']="p"; break;
    case "i" : $typename="Inbox Item"; $parentname="Project"; $values['ptype']="p"; break; //default to project as parent
    default  : $typename="Item"; $parentname="Project"; $values['ptype']="p"; //default to project as parent
}

if ($values['isSomeday']==="y") $typename="Someday/Maybe";
if ($nextaction) $typename="Next Action";

$parents = query("lookupparent",$config,$values);

if ($parents!="-1") foreach ($parents as $row) $values['parentId'][]=$row['parentId'];

//create filters for selectboxes
$values['timefilterquery'] = ($config['useTypesForTimeContexts'] && $values['type']!=='i')?" WHERE ".sqlparts("timetype",$config,$values):'';

//create item, timecontext, and spacecontext selectboxes
$pshtml = parentselectbox($config,$values,$options,$sort);
$cashtml = categoryselectbox($config,$values,$options,$sort);
$cshtml = contextselectbox($config,$values,$options,$sort);
$tshtml = timecontextselectbox($config,$values,$options,$sort);

$oldtype=$values['type'];

//PAGE DISPLAY CODE
$title=(($values['itemId']>0)?'Edit ':'New ').$typename;

$hiddenvars=array(
            'referrer'=>$referrer,
            'type'=>$values['type']
            );
            
if ($values['itemId']) {
    $hiddenvars['itemId']=$values['itemId'];
    $hiddenvars['action']='fullUpdate';
} else
    $hiddenvars['action']='create';

?>

<h2><?php echo $title; ?></h2>

<?php if (!empty($_REQUEST['createnote'])) { ?>
    <p class='warning'>Notes have been superseded by tickler actions. These actions get
    suppressed until a specified number of days before their deadlines</p>
<?php } if ($show['type']) {
    ?><p>
        <a href='assignType.php?itemId=<?php echo $values['itemId']; ?>'>Assign Type</a>
        (Warning, changing an item's type will sever all relationships to its parent and child items)
    </p>
<?php } ?>

<form action="processItems.php" method="post" onsubmit="return validate(this);"><div class='form'>
    <div class='formrow'><span class="error" id='errorMessage'></span></div>
        <?php if($show['title']) { ?>
            <div class='formrow'>
                    <label for='title' class='left first'>Title:</label>
                    <input type="text" name="title" id="title" value="<?php echo makeclean($values['title']); ?>" />
            </div>
        <?php } else $hiddenvars['title']=$values['title'];

        if ($show['ptitle']) { ?>
            <div class='formrow'>
                <label for='parent' class='left first'><?php echo $parentname; ?>:</label>
                <select name="parentId[]" id='parent' multiple="multiple" size="6">
                <?php echo $pshtml; ?>
                </select>
            </div>
        <?php } elseif (is_array($values['parentId']))
            foreach ($values['parentId'] as $parent)
                echo hidePostVar('parentId[]',$parent); ?>

        <div class='formrow'>
            <?php if ($show['category']) { ?>
                <label for='category' class='left first'>Category:</label>
                <select name='categoryId' id='category'>
                <?php echo $cashtml; ?>
                </select>
            <?php } else $hiddenvars['categoryId']=$values['categoryId'];
            if ($show['context']) { ?>
                <label for='context' class='left'>Context:</label>
                <select name='contextId' id='context'>
                <?php echo $cshtml; ?>
                </select>
            <?php } else $hiddenvars['contextId']=$values['contextId'];
            if ($show['timeframe']) { ?>
                <label for='timeframe' class='left'>Time:</label>
                <select name='timeframeId' id='timeframe'>
                <?php echo $tshtml; ?>
                </select>
            <?php } else $hiddenvars['timeframeId']=$values['timeframeId']; ?>
        </div>
        <div class='formrow'>
            <?php if ($show['deadline']) { ?>
                <label for='deadline' class='left first'>Deadline:</label>
                <input type='text' size='10' name='deadline' id='deadline' class='hasdate' value='<?php echo $values['deadline']; ?>'/>
                <button type='reset' id='deadline_trigger'>...</button>
                    <script type='text/javascript'>
                        Calendar.setup({
							firstDay    :   <?php echo (int) $config['firstDayOfWeek']; ?>,
                            inputField  :	'deadline',	  // id of the input field
                            ifFormat	:	'%Y-%m-%d',	   // format of the input field
                            showsTime	:	false,			// will display a time selector
                            button		:	'deadline_trigger',   // trigger for the calendar (button ID)
                            singleClick	:	true,		   // single-click mode
                            step		:	1				// show all years in drop-down boxes (instead of every other year as default)
                        });
                    </script>
            <?php } else $hiddenvars['deadline']=$values['deadline'];
            if ($show['dateCompleted']) { ?>
                <label for='dateCompleted' class='left'>Completed:</label><input type='text' size='10' class='hasdate' name='dateCompleted' id='dateCompleted' value='<?php echo $values['dateCompleted'] ?>'/>
                <button type='reset' id='dateCompleted_trigger'>...</button>
                    <script type='text/javascript'>
                        Calendar.setup({
							firstDay    :    <?php echo (int) $config['firstDayOfWeek']; ?>,
                            inputField	:	'dateCompleted',	  // id of the input field
                            ifFormat	:	'%Y-%m-%d',	   // format of the input field
                            showsTime	:	false,			// will display a time selector
                            button		:	'dateCompleted_trigger',   // trigger for the calendar (button ID)
                            singleClick	:	true,		   // single-click mode
                            step		:	1				// show all years in drop-down boxes (instead of every other year as default)
                        });
                    </script>
				<button type='button' id='dateCompleted_today' onclick="javascript:completeToday('dateCompleted');">Today</button>
            <?php } else $hiddenvars['dateCompleted']=$values['dateCompleted']; ?>
        </div>
        <?php if ($show['description']) { ?>
            <div class='formrow'>
                    <label for='description' class='left first'>Description:</label>
                    <textarea rows='12' cols='50' name='description' id='description'><?php echo makeclean($values['description']); ?></textarea>
            </div>
        <?php } else $hiddenvars['description']=$values['description'];
        if ($show['desiredOutcome']) { ?>
            <div class='formrow'>
                    <label for='outcome' class='left first'>Desired Outcome:</label>
                    <textarea rows='4'  cols='50' name='desiredOutcome' id='outcome' class='big'><?php echo makeclean($values['desiredOutcome']); ?></textarea>
            </div>
        <?php
        } else $hiddenvars['desiredOutcome']=$values['desiredOutcome'];
        if ($show['repeat']) { ?>
            <div class='formrow'>
                    <label for='repeat' class='left first'>Repeat every&nbsp;</label><input type='text' name='repeat' id='repeat' size='3' value='<?php echo $values['repeat']; ?>' /><label for='repeat'>&nbsp;days</label>
            </div>
        <?php } else $hiddenvars['repeat']=$values['repeat'];
        if ($show['suppress']) { ?>
            <div class='formrow'>
                    <label for='suppress' class='left first'>Tickler:</label>
                    <input type='checkbox' name='suppress' id='suppress' value='y' title='Temporarily puts this into the tickler file, hiding it from the active view' <?php if ($values['suppress']=="y") echo " checked='checked' "; ?>/>
                    <label for='suppressUntil'>Tickle&nbsp;</label>
                    <input type='text' size='3' name='suppressUntil' id='suppressUntil' value='<?php echo $values['suppressUntil'];?>' /><label for='suppressUntil'>&nbsp;days before deadline</label>
            </div>
        <?php } else {
            $hiddenvars['suppress']=$values['suppress'];
            $hiddenvars['suppressUntil']=$values['suppressUntil'];
        } ?>
        <div class='formrow'>
            <?php if ($show['NA']) { ?>
                <label for='nextAction' class='left first'>Next Action:</label><input type="checkbox" name="nextAction" id="nextAction" value="y" <?php if ($nextaction) echo " checked='checked'"; ?> />
            <?php } else $hiddenvars['nextAction']=($nextaction)?'y':'';
            if ($show['isSomeday']) { ?>
                <label for='isSomeday' class='left first'>Someday:</label><input type='checkbox' name='isSomeday' id='isSomeday' value='y' title='Places item in Someday file'<?php if ($values['isSomeday']==='y') echo " checked='checked'";?> />
            <?php } else $hiddenvars['isSomeday']=$values['isSomeday']; ?>
        </div>
    	<input type='hidden' name='required'
    	value='title:notnull:Title can not be blank.,deadline:date:Deadline must be a valid date.,dateCompleted:date:Completion date is not valid.,suppress:depends:You must set a deadline to base the tickler on:deadline,suppress:depends:You must set a number of days for the tickler to be active from:suppressUntil' />
    	<input type='hidden' name='dateformat' value='ccyy-mm-dd' />
<?php
if (!$values['itemId']) $hiddenvars['lastcreate']=$_SERVER['QUERY_STRING'];
foreach ($hiddenvars as $key=>$val) echo hidePostVar($key,$val);
echo "<input type='hidden' name='referrer' value='{$_REQUEST['referrer']}' />\n";
$key='afterCreate'.$values['type'];
// always use config value when creating
if (isset($config['afterCreate'][$values['type']]) && !isset($_SESSION[$key]))
	$_SESSION[$key]=$config['afterCreate'][$values['type']];
	
if ($values['itemId'])
    $tst=$_SESSION[$key];
else
    $tst=$config['afterCreate'][$values['type']];

echo "<div class='formrow'>\n<label class='left first'>After "
    ,($values['itemId'])?'updating':'creating'
    ,":</label>\n";
    
if ($show['ptitle'])
    echo "<input type='radio' name='afterCreate' id='parentNext' value='parent' class='first'"
	 	,($tst=='parent')?" checked='checked' ":""
		," /><label for='parentNext' class='right'>View parent</label>\n";
		
echo "<input type='radio' name='afterCreate' id='itemNext' value='item' class='notfirst'"
	 	,($tst=='item')?" checked='checked' ":""
		," /><label for='itemNext' class='right'>View item</label>\n"
	,"<input type='radio' name='afterCreate' id='listNext' value='list' class='notfirst'"
	 	,($tst=='list')?" checked='checked' ":""
		," /><label for='listNext' class='right'>List items</label>\n"
	,"<input type='radio' name='afterCreate' id='anotherNext' value='another' class='notfirst'"
	 	,($tst=='another')?" checked='checked' ":""
		," /><label for='anotherNext' class='right'>Create another $typename</label>\n";
		
if ($referrer!='' || $_SESSION[$key]!='') {
    echo "<input type='radio' name='afterCreate' id='referrer' value='referrer' class='notfirst'"
	 	,($tst=='referrer')?" checked='checked' ":''
		," /><label for='referrer' class='right'>Return to previous list</label>\n";
}

echo "</div>\n</div> <!-- form div -->\n<div class='formbuttons'>\n"
    ,"<input type='submit' value='"
    ,($values['itemId'])?"Update $typename":'Create'
    ,"' name='submit' />\n"
    ,"<input type='reset' value='Reset' />\n";
if ($values['itemId']) {
    echo "<input type='checkbox' name='delete' id='delete' value='y' title='Deletes item. Child items are orphaned, NOT deleted.'/>\n"
        ,"<label for='delete'>Delete&nbsp;$typename</label>\n"
        ,"<input type='hidden' name='oldtype' value='$oldtype' />\n";
}
echo "</div>\n</form>\n";

if ($values['itemId']) {
        echo "	<div class='details'>\n";
        echo "		<span class='detail'>Date Added: ".$values['dateCreated']."</span>\n";
        echo "		<span class='detail'>Last Modified: ".$values['lastModified']."</span>\n";
        echo "	</div>\n";
}
include_once('footer.php');
function hidePostVar($name,$val) {
    $val=makeclean($val);
    return "<input type='hidden' name='$name' value='$val' />\n";
}
?>
