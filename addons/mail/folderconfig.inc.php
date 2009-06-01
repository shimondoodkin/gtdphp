<?php
$title = "Folder Setup for Email Synchronization";

include_once 'imap.inc.php';
include_once 'mailconfig.inc.php';
include_once 'header.inc.php';

//General flow

/*

Per account loop:

	Make connection
	Retrieve folder listing
	Retreive # messages per folder and any other useful server-side data
	Close connection

Retrieve known folders in database along with their preference data
	match known folder data to retrieved folders
	

create form/table of folders, with associated preferences data and server-side data 
mark inconsistencies (new icon for new folders, red x for missing folders) 

display form

if missing folders note in browser that submitting form will delete missing folders from database... are you sure?

note in form that recommended NOT to sync all folders in all accounts--- leave reference folders (example) for manual sync
autoselect DRAFTS, TRASH/DELETED, SENT, as ignored folders.

Docs-- can use sent folders as waitingons-- but does everything you send need to be a waitingon?  might be better to manually move some items from sent to waitingon or cc self thoseto wait on

note for docs-- this setup is flexible enough to work well with automatic message processing rules in the email client-- move messages with certain content to specific folders for GTD-PHP tracking 
 

on submit-- go to sep script for database manipulation

	delete folders that no longer exist on server
	add new folders to database 	
	display result to browser

return to folder preferences page

*/

//Choose account to work with
?>
<form method="POST"  action="addon.php?addonid=mail&url=folderconfig.inc.php">
<select name="account">
<?php
foreach ($mailaccounts AS $accountno=>$account) {
	echo('<option name="account" value="'.$accountno.'">'.$account["label"].'</option>');
	}
?>
</select>
<input type="submit" value="Change" />
</form>

<?php

//make connection

if ($_POST[account]!=NULL) $selectedAccount=$_POST[account];
	else $selectedAccount=1;

$ICL_PORT = $mailaccounts[$selectedAccount]["port"];
$ICL_SSL = $mailaccounts[$selectedAccount]["use_ssl"];

$conn = iil_Connect($mailaccounts[$selectedAccount]["host"], $mailaccounts[$selectedAccount]["user"], $mailaccounts[$selectedAccount]["pass"]);

echo "<p>".$iil_error."</p>";

//retrieve list of all folders
$folder = "*";
$folder_list = iil_C_ListMailboxes($conn, $mailaccounts[$selectedAccount]["prefix"], $folder);

//Create selectbox html
//create select boxes for category, context, time:
$cashtml=str_replace('--','NONE',categoryselectbox($values));
$cshtml =str_replace('--','NONE',contextselectbox($values));
$tshtml =str_replace('--','NONE',timecontextselectbox($values));

//Create selectbox for item types
$types=gettypes();
$ishtml='<option value="0">NONE</option>'."\n";
if ($types) {
    foreach($types as $key=>$value) {
        $ishtml .= '<option value="'.$key.'" title="'.$value.'"';
            if($key==$folder['category']) $isshtml .= ' selected="selected"';  //will have to move this section to form to selection works (others too)
        $ishtml .= '>'.$value."</option>\n";
        }
    }


//Create selectbox for folders
$fshtml='<option value="0">NONE</option>'."\n";
foreach($folder_list AS $key=>$value) {
    $fshtml.='<option value="'.$key.'" title="'.$value.'"';
        if($key==$folder['id']) $fshtml .= ' selected="selected"'; //move into loop
        $fshtml.= '>'.$value."</option>\n";
    }

//Display folder configuration table

?>

<table>
<th>Folder</th><th>AutoSync</th><th>Ignore</th><th>Type</th><th>Someday</th><th>Category</th><th>Space</th><th>Time</th><th>Tags</th><th>Delete Completed</th><th>Move Completed</th>

<?php
foreach ($folder_list AS $key=>$value) {
?>
<tr>
<td><?php echo $value; ?></td>
<td><input type='checkbox' name='autosync' id='autosync' value='true' <?php if ($folder['autosync']=="true") echo 'checked="checked"'?> title="AutoSync" /></td>
<td><input type='checkbox' name='ignore' id='ignore' value='true' <?php if ($folder['ignore']=="true") echo 'checked="checked"'?> title="Ignore" /></td>
<td><select name="type" id="type" title="Assign emails in this folder to selected item type"><?php echo $ishtml; ?></select></td>
<td><input type='checkbox' name='someday' id='someday' value='true' <?php if ($folder['someday']=="true") echo 'checked="checked"'?> title="Someday" /></td>
<td><select name="categoryId" id="categoryId" title="Assign emails in this folder to selected category"><?php echo $cashtml; ?></select></td>
<td><select name="spaceId" id="spaceId" title="Assign emails in this folder to selected space context"><?php echo $cshtml; ?></select></td>
<td><select name="timeId" id="timeId" title="Assign emails in this folder to selected time context"><?php echo $tshtml; ?></select></td>
<td><input type="text" name="tags" id="tags" title="Assign emails in this folder to selected category"><?php echo $tags; ?></select></td>
<td><input type='checkbox' name='delete' id='delete' value='true' <?php if ($folder['delete']=="true") echo 'checked="checked"'?> title="Delete" /></td>
<td><select name="completed" id="completed" title="Move completed items to this folder"><?php echo $fshtml; ?></select></td>
</tr>

<?php
	}
        
echo ("</table>");


//Compare folder listing to database (split to a function)

//(Need mail functions file?)
//Split folder maintenance / preferences off on own page 

iil_close($conn);

include 'footer.inc.php';
?>
