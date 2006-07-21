<?php

////////////////////////////////////////////////////////
//File: ChecklistReport.php                           //
//Description: Show details about individual checklist//
//Accessed From: listChecklist.php                    //
//Links to: editChecklist.php, newChecklistItem.php   //
////////////////////////////////////////////////////////

	include_once('header.php');
	include_once('config.php');
	$checklistId = (int) $_GET['checklistId'];
	$checklistTitle =(string) $_GET['checklistTitle'];
 
	$connection = mysql_connect($host, $user, $pass) or die ("unable to connect");

	mysql_select_db($db) or die ("unable to select database!");

	echo '<form action="processChecklistUpdate.php?checklistId='.$checklistId.'" method="POST">';
	echo "<h1>Checklist Report: $checklistTitle</h1>";
	
	echo '[ <a href="editChecklist.php?checklistId='.$checklistId.'&checklistTitle='.$checklistTitle.'">Edit Checklist</a> ]';
	echo "<br />";

	echo '<h2><a href = "newChecklistItem.php?checklistId='.$checklistId.'" style="text-decoration:none">Checklist Items</a></h2>';

	$query = "SELECT checklistItems.checklistitemId, checklistItems.item, checklistItems.notes, checklistItems.checklistId, checklistItems.checked
		FROM checklistItems
		LEFT JOIN checklist on checklistItems.checklistId = checklist.checklistId
		WHERE checklist.checklistId = '$checklistId' ORDER BY checklistItems.checked DESC, checklistItems.item ASC";
	$result = mysql_query($query) or die ("Error in query");

	if (mysql_num_rows($result) > 0){
		$counter=0;
		
		echo "<table cellpadding=2 border=1>";
		echo '<th>Item</th>';
		echo '<th>Notes</th>'; 
		echo '<th>Checked</th>';
		echo '</tr>';
		
		while($row = mysql_fetch_row($result)){
                echo "<tr>";
                $checklistItemId = $row[0];
                echo '<td><a href = "editChecklistItem.php?checklistItemId='.$checklistItemId.'">'.$row[1].'</a></td>';
                echo "<td>".$row[2]."</td>";
		echo '<td align="center"><input type="checkbox" name="checkedClis[]" value="'.$checklistItemId.'" ';
		if ($row[4]=='y') echo 'CHECKED';
		echo '></td>';
                echo "</tr>";
                $counter = $counter+1;
		}
		echo "</table>";

		echo '<p>&nbsp;&nbsp;Clear Checklist&nbsp;<input type="checkbox" name="clear" value="y"></p>';

		echo '<p><input type="submit" align="right" class="button" value="Update Checklist Items" name="submit">';
                echo '<input type="reset" class="button" value="Reset to Saved State"></p>';
		if($counter==0){
			echo "No checklist items";
		}
	}



	mysql_free_result($result);
	mysql_close($connection);
	include_once('footer.php');
?>
