<?php
//INCLUDES
	include_once('gtdfuncs.php');
	include_once('header.php');
	include_once('config.php');

//RETRIEVE URL VARIABLES
	$projectId= (int) $_GET["projectId"];
	$itemId= (int) $_GET["itemId"];
	$type=$_GET["type"]{0};
	if ($type=="n") {
		$type='a';
		$nextactioncheck='true';
		}

	$pType=$_GET["pType"]{0};
	if ($pType=="s") {
		$isSomeday="y";
		$pTypename="Someday/Maybe";
		}
	else { 
		$isSomeday="n";
		$pTypename="Project";
		}

//SQL CODE
	$connection = mysql_connect($host, $user, $pass) or die ("Unable to connect");
	mysql_select_db($db) or die ("Unable to select database!");

	//select item details
	if ($itemId>0) {
	$query= "SELECT items.itemId, itemattributes.projectId, itemattributes.contextId, itemattributes.type, itemattributes.timeframeId, items.title, 
			items.description, itemstatus.dateCreated, itemattributes.deadline, itemstatus.dateCompleted, itemstatus.lastModified, 
			itemattributes.repeat, itemattributes.suppress, itemattributes.suppressUntil FROM items, itemattributes, itemstatus 
			WHERE itemstatus.itemId=items.itemId and itemattributes.itemId=items.itemId and items.itemId = '$itemId'";
	$result = mysql_query($query) or die ("Error in query");
	$currentrow = mysql_fetch_assoc($result);
	mysql_free_result($result);
	$type=$currentrow['type'];
	}

	//Test to see if nextaction
	$query = "SELECT projectId, nextaction FROM nextactions where nextaction='$itemId'";
	$result = mysql_query($query) or die ("Error in query");
	while ($nextactiontest = mysql_fetch_assoc($result)) {
		if ($nextactiontest['nextaction']==$itemId) $nextactioncheck='true';
		}
	mysql_free_result($result);


	//select active or someday projects for selectbox (would make good function!)
	$query="SELECT projects.projectId, projects.name, projects.description
		FROM projects, projectattributes, projectstatus
                WHERE projectattributes.projectId = projects.projectId 
		AND projectstatus.projectId=projects.projectId 
		AND (projectstatus.dateCompleted IS NULL OR projectstatus.dateCompleted = '0000-00-00') 
		AND projectattributes.isSomeday ='".$isSomeday."' ORDER BY projects.name";
	$result = mysql_query($query) or die ("Error in query");
	$pshtml="";
	while($row = mysql_fetch_assoc($result)){
        	$pshtml .= '<option value="'.$row['projectId'].'" title="'.htmlspecialchars(stripslashes($row['description'])).'"';
        	if($row['projectId']==$currentrow['projectId'] || $row['projectId']==$projectId) $pshtml .= ' SELECTED';
        	$pshtml .= '>'.stripslashes($row['name']).'</option>\n';
		}
	mysql_free_result($result);
	
	//select all contexts for selectbox (would make good function!)
	$query = "SELECT contextId, name, description FROM context ORDER BY name ASC";
	$result = mysql_query($query) or die("error in query: $query.  ".mysql_error());
	$cshtml="";

	while($row = mysql_fetch_assoc($result)) {
        	$cshtml .= '<option value="'.$row['contextId'].'" title="'.htmlspecialchars(stripslashes($row['description'])).'"';
        	if($row['contextId']==$currentrow['contextId']) $cshtml .= ' SELECTED';
        	$cshtml .= '>'.stripslashes($row['name']).'</option>\n';
        	}
	mysql_free_result($result);

	//select all itemtimeframes for selectbox (function candidate?)
	$query = "SELECT timeframeId, timeframe, description FROM timeitems ORDER BY timeframe DESC";
	$result = mysql_query($query) or die("error in query: $query.  ".mysql_error());
	$tshtml="";
	while($row = mysql_fetch_assoc($result)){
        	$tshtml .= '<option value="'.$row['timeframeId'].'" title="'.htmlspecialchars(stripslashes($row['description'])).'"';
        	if($row['timeframeId']==$currentrow['timeframeId']) $tshtml .= ' SELECTED';
        	$tshtml .= '>'.stripslashes($row['timeframe']).'</option>\n';
        	}
	mysql_free_result($result);

//PAGE DISPLAY CODE
	
	//determine item label
	if ($type=="a") $typename="Action";
	elseif ($type=="r") $typename="Reference";
	elseif ($type=="w") $typename="Waiting On";
 	else $typename="Item";

	if ($itemId>0) {
		echo "<h2>Edit ".$typename."</h2>";
		echo '<form action="updateItem.php?itemId='.$itemId.'" method="post">';
		}

	else {
		echo "<h2>New ".$typename."</h2>";
		echo '<form action="processItem.php" method="post">';
		}

	echo '<table border="0">';
	echo '<tr><td>'.$pTypename.'</td>';
	echo '<td><select name="projectId">'.$pshtml.'</select></td>';
	echo '<td>Context</td>';
	echo '<td><select name="contextId">'.$cshtml.'</select></td>';
	echo '<td>Time</td>';
	echo '<td><select name="timeframeId">'.$tshtml.'</select></td>';
	echo '</tr></table>';
	
	echo'<table>';
	echo '<tr><td>Type:&nbsp;';
	echo '<input type="radio" name="type" value="a"';
	if ($type=='a') echo " CHECKED";
	echo ' />Action';
	echo '<input type="radio" name="type" value="r"';
	if ($type=='r') echo " CHECKED";
	echo ' />Reference';
	echo '<input type="radio" name="type" value="w"';
	if ($type=='w') echo " CHECKED";
	echo ' />Waiting';
	echo '</td>';

	if ($nextactioncheck=='true') echo '<td><input type="checkbox" name="nextAction" value="y" CHECKED />Next Action</td>';
	else echo '<td><input type="checkbox" name="nextAction" value="y" />Next Action</td>';

	echo '<td><input type="checkbox" name="delete" value="y" />Delete Item</td></tr>';
	echo '<tr>';
	echo '<td><input type="checkbox" name="suppress" value="y" title="Hides item from active view (viewable from tickler file only)."';
	if ($currentrow['suppress']=="y") echo " CHECKED";
	echo '>Tickle&nbsp;<input type="text" size="3" name="suppressUntil" value="'.$currentrow['suppressUntil'].'">';
	echo '&nbsp;days before deadline</td>';
	echo '<td colspan="2">Deadline:&nbsp;';
	if ($currentrow['deadline']=="0000-00-00" || $currentrow['deadline']==NULL || $currentrow['deadline']>date("Y-m-d")) {
	        DateDropDown(365,"deadline",$currentrow['deadline']);
		}
	else echo '<input type="text" size="10" name="deadline" value="'.$currentrow['deadline'].'" />';
	echo '</td>';
	echo '<tr><td>Repeat every&nbsp;<input type="text" name="repeat" size="3" value="';
	echo $currentrow['repeat'];
	echo '">&nbsp;days</td>';
	echo '<td colspan="2">Completed:&nbsp;';
	if ($currentrow['dateCompleted']=="0000-00-00" || $currentrow['dateCompleted']==NULL) {
		DateDropDown(365,"dateCompleted",$currentrow['dateCompleted']);
		}
	else echo '<input type="text" size="10" name="dateCompleted" value="'.$currentrow['dateCompleted'].'" />';
        echo '</td></tr>';
	echo '</table>';

	echo '<table>';
	echo '<tr><td>Title</td></tr>';
	echo '<tr><td><input type="text" size="79" name="title" value="'.stripslashes($currentrow['title']).'"></td></tr>';
	echo '<tr><td>Description</td></tr>';
	echo '<tr><td><textarea cols="77" rows="12" name="description" wrap=virtual">'.stripslashes($currentrow['description']).'</textarea></td></tr>';
	echo '</table>';

	if ($itemId>0) {
	echo '<table>';
	echo '<tr><td>Date Added:&nbsp;'.$currentrow['dateCreated'].'</td>';
	echo '<td>Last Modified:&nbsp;'.$currentrow['lastModified'].'</td></tr>';
	echo '</tr>';
	echo '</table>';
	}

	echo '<br />';

	if ($itemId>0) {
		echo '<input type="submit" class="button" value="Update '.$typename.'" name="submit">';
		}

	else echo '<input type="submit" class="button" value="Add '.$typename.'" name="submit">';

	echo '<input type="reset" class="button" value="Reset">';
	include_once('footer.php');
?>
