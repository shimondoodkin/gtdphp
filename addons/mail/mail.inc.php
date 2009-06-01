﻿<?php
$title = "Email Synchronization";
include_once 'imap.inc.php';
include_once 'mailconfig.inc.php';
include_once 'header.inc.php';

/* General flow:

Initial page as main addon interface-- links to folder config, manual sync, autosync.

User actions:
	Data appears in users inbox
	User reads emails in regular mail client
	Deletes, files, replies in mail client as appropriate
	For items that require further processing and/or later action (GTD 2 minute rule)
		Selects specific folders as desired (ACTION and subfolders, WAITING and subfolders, etc) to synchronize	
			would suggest users create specific folders for categories, contexts, projects, or whatever makes most sense to them, but to make it a limited number of folders for speed and simplicity
		Switches to GTD-PHP to collect emails as items
		    Advantage: empty inbox and convenient mirroring of categorization within the email client	
		    Disadvantage: multiple folders in which actionable items may appear
		        Tip: simplist to create only ACTION/WAITING (possibly ACTION-PROFESSIONAL and ACTION-PERSONAL) to limit the number of folders and potential for getting too finely-grained


Retrieve connections to work with (from mailconfig)

Retrieve options (ones that impact prgram flow and do not pertain to which folders to sync--e.g.: deleted messages-- delete item, mark completed, do nothing? etc)

Retrieve list of folders to synchronize for each account from database $folderIDs sync=y

Retrieve known message IDs for each synchronized folder $trackedIDs

Create a loop/function: for each account

	make server connection $conn 
	Retreive header listing $headers
	compare folder contents to message IDs for each folder
		collect header contents for new messages (in folder, not in database) $newMessages
		collect messageIds for messages no longer in folder  $trackedIds, no match in $headers = $deletedMessages

	process new messages according to preferences
		flag imported messages? -- check if works in Gmail-- mail.app flags appear to be client-side only?
		
		new message = create item
			preference: delete message on import? (possible)

		old message -> verify item

			item exists... move on
			item deleted... delete message? (configurable)
			item completed... delete message?  (configurable) move to another folder? (for those who need to keep references/reports, etc)...
			item altered-- 
				This won't be known in current database.  Only associate messageIds with itemIds... status of item isn't tracked... therefore only know current status of item, not historical. 	
				If folder category/item type mapping is complete, can move item to matching attributes--- only works if each folder has a unique mapping. (one folder per category,status,type)  

					move to another folder?  (configurable) delete? ignore?
						per-folder preference? or global folder for altered items? (or per status of item?  someday, waiting, reference, etc...) 

			error handling: message / item mapping already exists-- will throw error to browser... no duplicate messages allowed.
 
	process stale items according to preferences
			an item exists, mapped to a nonexistant messageId in an autosynced folder.

				probably should trigger an account-wide imap folder scan
					can mark mapping as stale for next full-folder scan.  (what if never done... no maintenance?)
					very likely that user moves a message to another folder in the email client when done with it--- GTD-PHP should keep up, 
						rather then force the user into a one-way sync (likely to process emails faster than GTD-PHP items)

				message truly deleted: delete item if message gone? mark completed? do nothing(default option) ? ask user?
				
				message moved to another folder-- update item with folder's mappings (if complete.. don't touch unmapped attributes)

  			Large number of tracked messages could slow down/bloat database. However, could timestamp association and purge after x# days (years in my case?--make configurable)
				or at least search for messages/items that are old-- and ask what to do with them.... (work on this): if any timestamp > x hours/days-- present to user...
	
	if item marked done-- what to do with item?

Emails associated with completed items can be deleted on next folder refresh
    automatically, by preference setting
    manually, by deleting emails marked with a completed icon
    Would modify the backend to update this table on item completion
        alternatively if this is developed as a plug-in, would have to query associated ItemIDs on plugin refresh
 
 
Importing item copies email subject into item title, date into date created field, and email body into the description field
    Opens standard item editing screen for turning email into useful data
        Can then change type (import as action, reference, etc)
        Can modify subject to make more useful and can delete unnecessary data
    Option: can map folder to item import type
        requires preferences table for settings
            folder name - item type
            ACTION - ACTIONS
            WAITING - WAITING ONs
            Really important project - ACTIONS
        if implemented properly, can also map specific folders to other settings, like category or context
 
data imported into database as a new item, as usual
    ItemID and IMAP message ID added to a lookup table to maintain association
        messageID is now stored, and will be recognized upon IMAP folder refresh

data is manipulated in GTD-PHP as usual

Other saved emails in a folder can be ignored
    belonging to the same email thread
    unactionable
    Ideally, neither of these will be used, as adherence to proper GTD-principles would require placing these items in a reference folder
    However, many people would prefer to keep all material related to a specific project or construct in one place
        Ideally, importing actionable emails into GTD-PHP as actions/projects, etc. will facilitate this, as you can then deal with them within GTD-PHP and leave the rest of the folder intact as emails.
        alternatively, you could import the other emails as references associated with a project
            these would then get tracking Ids as well
    The "ignoring" would be done via a checkbox and a mass "ignore checked emails" button
    Ignoring will not remove them from view, but rather mark them as ignored in the message listing, to prevent one from having to reprocess them again
    Ignoring will add the message Ids to the known message ID table, but without a corresponding item ID
these would then get tracking Ids as well


loop

close connection

display results per account/folder level

rather than automatically sync everything statically in a folder, can there be a second step to modify the import result?  Use folder preferences to pre-fill items, then display items as like a mass-item addition page for editing?

email headers contained in selected IMAP folder appear in a table
    Subject
    Sender
    Date
    Optional:variable length of body (configurable)
    Tracking icons
        New
        Unread
        Imported/Tracked/Mapped
            Serves as link to item within GTD-PHP
        Has attachment
            Paperclip
        Completed
            Strike-out?

Import/Track/Map button
    Either mass-import with checkboxes
    Likely initially alongside each email item


user input to deal with conflicts?

user input to manually sync one folder --> reruns script with single folderID
	use selectbox dropdown to select folder from all folders in database set to ignore=n

user input to manually sync all folders --> reruns script with all folders in database set to ignore=n

Message Listing
    Lists messages contained in folder
        Icon for tracked items
            Integration with checkbox selection will confuse interface
            Tracking icon
                Search against ID mapping table
            New item icon
                Search against known message ID table
            Completed item icon
                Search against completed item IDs with tracking map
            Attachment icon
                For future use when/if tracking item attachments
            Unread icon
                from IMAP data
            Use flagged field as next action
        Sort options
            Date
            Subject
            To
            From
        Checkboxes
            mass delete
            mass import?



*/

//Choose account to work with
?>
<p><a href="addon.php?addonid=mail&url=folderconfig.inc.php">Configure Folders</a></p>

<form method="POST"  action="addon.php?addonid=mail&url=mail.inc.php">
<p>Account: <select name="account">
<?php
foreach ($mailaccounts AS $accountno=>$account) {
	echo('<option name="account" value="'.$accountno.'">'.$account["label"].'</option>');
	}
?>
</select>
<input type="submit" value="Change" />
</p>
</form>

<?php

//make connection

if ($_POST[account]!=NULL) $selectedAccount=$_POST[account];
	else $selectedAccount=1;

$ICL_PORT = $mailaccounts[$selectedAccount]["port"];
$ICL_SSL = $mailaccounts[$selectedAccount]["use_ssl"];

$conn = iil_Connect($mailaccounts[$selectedAccount]["host"], $mailaccounts[$selectedAccount]["user"], $mailaccounts[$selectedAccount]["pass"]);

echo "<p>".$iil_error."</p>";

//folders to retrieve
//can use to retrieve selected names, not in tree order (for those using @... and WAITING, ACTION, REFERENCE, etc).. MAKE CONFIG
$folder = "*";



//Retrieve folder listing 

$folder_list = iil_C_ListMailboxes($conn, $mailaccounts[$selectedAccount]["prefix"], $folder);

echo ("<ul>");
foreach ($folder_list AS $key=>$value) {
	echo ("<li>".$value."</li>");
	}
echo ("</ul>");


//Compare folder listing to database (split to a function)

//(Need mail functions file?)
//Split folder maintenance / preferences off on own page 




// Retrieve headers in selected folder

$selected_folder="INBOX";

echo ("<p>Folder: ".$selected_folder."</p>");

$count = iil_C_CountMessages($conn, $selected_folder);

echo ("<p>".$count." messages in folder.</p>");
 
echo ("<h3>Messages</h3>");


?>

<table class="datatable sortable" summary= "table of emails" id="emailtable" border=1>

<th>From</th><th>Subject</th><th>Date</th><th>Flags</th><th>Seen</th><th>Answered</th><th>Size</th><th>Encoding</th>

<?php

$i=$count;


for ($j=1;$j<=$count;$j++) {
	$message_set=$message_set.(string)$j;
	if ($j!=$count) $message_set=$message_set.",";
	}

$headers=iil_C_FetchHeaders($conn, $selected_folder, $message_set);


for ($j=1;$j<=$count;$j++) {

	if ($headers[$j]->deleted!=true) {
		echo ("<tr>");
		echo ("<td>".$headers[$j]->from."</td>");	
		echo ("<td>".$headers[$j]->subject."</td>");	
		echo ("<td>".$headers[$j]->date."</td>");	
		echo ("<td>".$headers[$j]->flags."</td>");	
		echo ("<td>".$headers[$j]->seen."</td>");	
		echo ("<td>".$headers[$j]->answered."</td>");	
		echo ("<td>".$headers[$j]->size."</td>");	
		echo ("<td>".$headers[$j]->encoding."</td>");	
		echo("</tr>");
	}
}


//var_dump($headers);

?>
</table>


<?php

iil_close($conn);

include 'footer.inc.php';
?>
