<?php
$title = "Email Import";

include_once 'imap.inc.php';
include_once 'mailconfig.inc.php';
include_once 'header.inc.php';

//Choose account to work with
?>
<form method="POST"  action="addon.php?addonid=mail&url=mail.inc.php">
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

$selected_folder="[Gmail]/All Mail";

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
