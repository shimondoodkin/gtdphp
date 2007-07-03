<?php
//INCLUDES
include_once('header.php');

$values = array();
$values['itemId']= (int) getVarFromGetPost('itemId');
$values['parentId']=array();
$currentrow = array();

//SQL CODE
if ($values['itemId']) { // editing an item
    $result = query("selectitem",$config,$values,$options,$sort);
    if ($GLOBALS['ecode']==0) {
    $currentrow = $result[0];
    $values['itemId']=$currentrow['itemId'];
    $values['type']=$currentrow['type'];
    $values['categoryId']=$currentrow['categoryId'];
    $values['contextId']=$currentrow['contextId'];
    $values['timeframeId']=$currentrow['timeframeId'];
    $values['isSomeday']=$currentrow['isSomeday'];

    //Test to see if nextaction
    $result = query("testnextaction",$config,$values,$options,$sort);
    if ($result!="-1") {
        if ($result[0]['nextaction']==$values['itemId']) $nextaction=true;
        }
    }
    //$_SESSION['lastcreate']=''; // I don't think we need to do this, so have commented it out for now. [Andrew]
} else { // creating an item
    //RETRIEVE URL VARIABLES
    $values['type']=getVarFromGetPost('type');
    if ($values['type']==='s') {
        $values['isSomeday']='y';
        $values['type']='p';
    }
    if ($_GET['someday']=='true') $values['isSomeday']='y';
    if ($_GET['suppress']=='true') $currentrow['suppress']='y';

    $nextaction=false;
    if ($values['type']==='n') {
        $nextaction=true;
        $values['type']='a';
    }
    if ($_GET['nextonly']=='true') $nextaction=true;

    $tmp=getVarFromGetPost('parentId');
    if ($tmp!=='') $values['parentId'][0] = (int) $tmp;

    foreach ( array('categoryId','contextId','timeframeId') as $cat)
        $values[$cat]= (int) getVarFromGetPost($cat);

    $_SESSION['lastcreate']=$_SERVER['QUERY_STRING'];
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
echo '<h2>',($values['itemId']>0)?'Edit ':'New ',$typename,"</h2>\n";
echo '	<form action="processItems.php" method="post" onsubmit="return validate(this);">',"<div class='form'>\n";

echo "<input type='hidden' name='action' value='",
	($values['itemId']>0)?("fullUpdate' />\n<input type=\"hidden\" name=\"itemId\" value=\"{$values['itemId']}\" "):"create'"
	," /> \n";
?>	
        <div class='formrow'><span class="error" id='errorMessage'></span></div>
		<input type='hidden' name='required'
		value='title:notnull:Title can not be blank.,deadline:date:Deadline must be a valid date.,dateCompleted:date:Completion date is not valid.,suppress:depends:You must set a deadline to base the tickler on:deadline,suppress:depends:You must set a number of days for the tickler to be active from:suppressUntil' /> 
 		<input type='hidden' name='dateformat' value='ccyy-mm-dd' />         
                <div class='formrow'>
                        <label for='title' class='left first'>Title:</label>
                        <input type="text" name="title" id="title" value="<?php echo htmlspecialchars(stripslashes($currentrow['title'])); ?>" />
                </div>

                <?php if ($values['ptype']!="") { ?>
                <div class='formrow'>
                        <label for='parent' class='left first'>
                        <?php echo $parentname; ?>:</label>
                        <select name="parentId[]" id='parent' multiple="multiple" size="6">
                        <?php echo $pshtml; ?>
                        </select>
                </div>
                <?php } ?>

                <div class='formrow'>
                        <label for='category' class='left first'>Category:</label>
                        <select name='categoryId' id='category'>
                        <?php echo $cashtml; ?>
                        </select>

                        <label for='context' class='left'>Context:</label>
                        <select name='contextId' id='context'>
                        <?php echo $cshtml; ?>
                        </select>

                        <label for='timeframe' class='left'>Time:</label>
                        <select name='timeframeId' id='timeframe'>
                        <?php echo $tshtml; ?>
                        </select>
                </div>

                <div class='formrow'>
                        <label for='deadline' class='left first'>Deadline:</label>
                        <input type='text' size='10' name='deadline' id='deadline' value='<?php echo $currentrow['deadline']; ?>'/>
                        <button type='reset' id='deadline_trigger'>...</button>
                                <script type='text/javascript'>
                                        Calendar.setup({
    											firstDay       :    <?php echo (int) $config['firstDayOfWeek']; ?>,
                                                inputField	 :	'deadline',	  // id of the input field
                                                ifFormat	   :	'%Y-%m-%d',	   // format of the input field
                                                showsTime	  :	false,			// will display a time selector
                                                button		 :	'deadline_trigger',   // trigger for the calendar (button ID)
                                                singleClick	:	true,		   // single-click mode
                                                step		   :	1				// show all years in drop-down boxes (instead of every other year as default)
                                        });
                                </script>
                        <label for='dateCompleted' class='left'>Completed:</label><input type='text' size='10' name='dateCompleted' id='dateCompleted' value='<?php echo $currentrow['dateCompleted'] ?>'/>
                        <button type='reset' id='dateCompleted_trigger'>...</button>
                                <script type='text/javascript'>
                                        Calendar.setup({
												firstDay       :    <?php echo (int) $config['firstDayOfWeek']; ?>,			
                                                inputField	 :	'dateCompleted',	  // id of the input field
                                                ifFormat	   :	'%Y-%m-%d',	   // format of the input field
                                                showsTime	  :	false,			// will display a time selector
                                                button		 :	'dateCompleted_trigger',   // trigger for the calendar (button ID)
                                                singleClick	:	true,		   // single-click mode
                                                step		   :	1				// show all years in drop-down boxes (instead of every other year as default)
                                        });
                                </script>
						<button type='button' id='dateCompleted_today' onclick="javascript:completeToday('dateCompleted');">Today</button>
                </div>
                <div class='formrow'>
                        <label for='description' class='left first'>Description:</label>
                        <textarea rows='12' cols='50' name='description' id='description'><?php echo htmlspecialchars(stripslashes($currentrow['description'])); ?></textarea>
                </div>
                <div class='formrow'>
                        <label for='outcome' class='left first'>Desired Outcome:</label>
                        <textarea rows='4'  cols='50' name='desiredOutcome' id='outcome' class='big'><?php echo htmlspecialchars(stripslashes($currentrow['desiredOutcome'])) ?></textarea>
                </div>
                <div class='formrow'>
                    <?php if ($values['itemId']) { ?>
                        <label class='left first'>Warning:</label>
                        <span class='text'>changing the item type will sever all parent &amp; child relationships with this item</span>
                        </div>
                        <div class='formrow'>
                    <?php } ?>
                        <label for='value' class='left first'>Type:</label>
                        <input type='radio' name='type' id='value' value='m' class="first" <?php if ($values['type']=='m') echo "checked='checked' "; ?>/><label for='value' class='right'>Value</label>
                        <input type='radio' name='type' id='vision' value='v' class="notfirst" <?php if ($values['type']=='v') echo "checked='checked' "; ?>/><label for='vision' class='right'>Vision</label>
                        <input type='radio' name='type' id='role' value='o' class="notfirst" <?php if ($values['type']=='o') echo "checked='checked' "; ?>/><label for='role' class='right'>Role</label>
                        <input type='radio' name='type' id='goal' value='g' class="notfirst" <?php if ($values['type']=='g') echo "checked='checked' "; ?>/><label for='goal' class='right'>Goal</label>
                        <input type='radio' name='type' id='project' value='p' class="notfirst" <?php if ($values['type']=='p') echo "checked='checked' "; ?>/><label for='project' class='right'>Project</label>
                </div>
                <div class='formrow'>
                        <label class='left first'>&nbsp;</label>
                        <input type='radio' name='type' id='action' value='a' class="first" <?php if ($values['type']=='a') echo "checked='checked' "; ?>/><label for='action' class='right'>Action</label>
                        <input type='radio' name='type' id='reference' value='r' class="notfirst" <?php if ($values['type']=='r') echo "checked='checked' "; ?>/><label for='reference' class='right'>Reference</label>
                        <input type='radio' name='type' id='waiting' value='w' class="notfirst" <?php if ($values['type']=='w') echo "checked='checked' "; ?>/><label for='waiting' class='right'>Waiting</label>
                        <input type='radio' name='type' id='inbox' value='i' class="notfirst" <?php if ($values['type']=='i') echo "checked='checked' "; ?>/><label for='inbox' class='right'>Inbox</label>
                </div>

                <div class='formrow'>
                        <label for='repeat' class='left first'>Repeat every&nbsp;</label><input type='text' name='repeat' id='repeat' size='3' value='<?php echo $currentrow['repeat']; ?>' /><label for='repeat'>&nbsp;days</label>
                </div>

                <div class='formrow'>
                        <label for='suppress' class='left first'>Tickler:</label>
                        <input type='checkbox' name='suppress' id='suppress' value='y' title='Temporarily puts this into the tickler file, hiding it from the active view' <?php if ($currentrow['suppress']=="y") echo " checked='checked' "; ?>/>
                        <label for='suppressUntil'>Tickle&nbsp;</label>
                        <input type='text' size='3' name='suppressUntil' id='suppressUntil' value='<?php echo $currentrow['suppressUntil'];?>' /><label for='suppressUntil'>&nbsp;days before deadline</label>
                </div>

                <div class='formrow'>
                        <label for='nextAction' class='left first'>Next Action:</label><input type="checkbox" name="nextAction" id="nextAction" value="y" <?php if ($nextaction) echo " checked='checked'"; ?> />
                        <label for='isSomeday' class='left first'>Someday:</label><input type='checkbox' name='isSomeday' id='isSomeday' value='y' title='Places item in Someday file'<?php if ($values['isSomeday']==='y') echo " checked='checked'";?> />
                </div>
<?php
$referrer=getVarFromGetPost('referrer');
echo "<input type='hidden' name='referrer' value='$referrer' />\n";
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
    ,":</label>\n"
	,"<input type='radio' name='afterCreate' id='parentNext' value='parent' class='first'"
	 	,($tst=='parent')?" checked='checked' ":""
		," /><label for='parentNext' class='right'>View parent</label>\n"
	,"<input type='radio' name='afterCreate' id='itemNext' value='item' class='notfirst'"
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
        echo "		<span class='detail'>Date Added: ".$currentrow['dateCreated']."</span>\n";
        echo "		<span class='detail'>Last Modified: ".$currentrow['lastModified']."</span>\n";
        echo "	</div>\n";
}
include_once('footer.php');
?>
