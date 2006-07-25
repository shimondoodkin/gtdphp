<?php
//INCLUDES
include_once('gtdfuncs.php');
include_once('header.php');
include_once('config.php');

//SQL CODE
$connection = mysql_connect($host, $user, $pass) or die ("Unable to connect");
mysql_select_db($db) or die ("unable to select database!");

//RETRIEVE AND PARSE URL VARIABLES
$type = $_GET['type']{0};
if ($type=="") $type="v";
$Id= (int) $_GET['Id'];

//SQL CODE AREA
//Get view details to edit
if ($type=="v" && $Id>0) {
	$dquery = "SELECT values.valueId AS Id, `value` AS name, description, dateCreated, dateCompleted, lastModified 
		FROM `values`, valuestatus  WHERE values.valueId='$Id' ORDER BY name ASC";
	}

else if ($type=="e" && $Id>0) {
	$dquery = "SELECT visions.visionId AS Id, vision AS name, description, dateCreated, dateCompleted, lastModified 
		FROM visions, visionstatus WHERE visions.visionId=visionstatus.visionId AND visions.visionId='$Id' ORDER BY name ASC";
	$cquery = "SELECT valueId as Id from visionlookup WHERE visionId='$Id'";
	}

else if ($type=="g" && $Id>0) {
	$dquery = "SELECT goals_new.goalId AS Id, goal AS name, description, dateCreated, dateCompleted, lastModified 
		FROM goals_new, goalstatus WHERE goals_new.goalId=goalstatus.goalId AND goals_new.goalId='$Id' ORDER BY name ASC";
	$cquery = "SELECT visionId as Id from goallookup WHERE goalId='$Id'";
	}

else if ($type=="r" && $Id>0) {
	$dquery = "SELECT responsibilities.respId AS Id, responsibility AS name, description, dateCreated, dateCompleted, lastModified 
		FROM responsibilities, respstatus WHERE responsibilities.respId=respstatus.respId AND responsibilities.respId='$Id' ORDER BY responsibility ASC";
	$cquery = "SELECT goalId as Id from rslookup WHERE respId='$Id'";
	}

if ($Id>0) {
	echo $dquery;
	$dresult = mysql_query($dquery) or die ("Error in query d");
	$row = mysql_fetch_assoc($dresult);
	mysql_free_result($dresult);
	}

//select higher-order view for checkboxes
$typelabel="Value";
$picklabel="";
switch ($type) {
case "e" : {
	$pquery = "SELECT valueId AS Id, value AS name, description FROM `values` ORDER BY name ASC";
	$typelabel="Vision";
	$picklabel="50,000 ft. view: Values";
	}
break;
case "g" : {
	$pquery = "SELECT visions.visionId AS Id, vision AS name, description FROM visions, visionstatus 
		WHERE visions.visionId=visionstatus.visionId AND
		(visionstatus.dateCompleted IS NULL OR visionstatus.dateCompleted = '0000-00-00') ORDER BY name ASC";
	$typelabel="Goal";
	$picklabel="40,000 ft. view: Visions";
	}
break;
case "r" : {
	$pquery = "SELECT goals_new.goalId AS Id, goal AS name, description, timeframe FROM goals_new, goalstatus 
		WHERE goals_new.goalId=goalstatus.goalId AND
		(goalstatus.dateCompleted IS NULL OR goalstatus.dateCompleted = '0000-00-00') ORDER BY name ASC";
	$typelabel="Responsibility";
	$picklabel="30,000 ft. view: Goals";
	}
break;
}

if ($type!="v") {
	$presult = mysql_query($pquery) or die("Error in query p");
	if ($Id>0) $cresult = mysql_query($cquery) or die ("Error in query c");
	$shtml="";
	while($row = mysql_fetch_assoc($presult)) {
		$shtml .= '&nbsp;&nbsp;<input type="checkbox" name="select[]" value="'.$row['Id'].'" title="'.$row['description'].'"';
		if ($Id>0) {
			while($crow = mysql_fetch_assoc($cresult)) {
        			if($crow['Id']==$row['Id']) $shtml .= ' CHECKED';
				}
			}
		$shtml .= ' />&nbsp;'.stripslashes($row['name']);
        	}
	mysql_free_result($presult);
	if ($Id>0) mysql_free_result($cresult);
	}


//PAGE DISPLAY CODE
//Header
if ($Id>0) {
	echo "<h2>Edit&nbsp;".$typelabel."</h2>";
	echo '<form action="updateview.php?Id='.$Id.'" method="post">';
	}

else {
	echo "<h2>New&nbsp;".$typelabel."</h2>";
	echo '<form action="processview.php" method="post">';
	}

echo '<table border="0"><tr><td><input type="checkbox" name="delete" value="delete" title="Deletes view">&nbsp;Delete&nbsp;'.$typename.'</td><td>Completed:&nbsp;';
if ($row['dateCompleted']=="0000-00-00" || $row['dateCompleted']==NULL) {
        DateDropDown(365,"dateCompleted",$currentrow['dateCompleted']);
        }
else echo '<input type="text" size="10" value="'.$row['dateCompleted'].'" />';
echo '</td></tr></table>';

echo '<table>';
echo '<tr><td>'.$typelabel.'</td></tr>';
echo '<tr><td><input type="text" name="name" size="79" value="'.stripslashes($row['name']).'"></td></tr>';
echo '<tr><td>Description</td></tr>';
echo '<tr><td><textarea cols="77" rows="8" name="description" wrap=virtual">'.stripslashes($row['description']).'</textarea></td></tr>';

if ($type!="v") echo '<tr><td>'.$picklabel.'</td></tr><tr><td>'.$shtml.'</td></tr>';
echo '</table>';

if ($Id>0) {
        echo '<table>';
        echo '<tr><td>Date Added:&nbsp;'.$row['dateCreated'].'</td>';
        echo '<td>Last Modified:&nbsp;'.$row['lastModified'].'</td></tr>';
        echo '</tr>';
        echo '</table>';
        }

echo '<br>';
echo '<input type="hidden" name="type" value='.$type.'" />';

if ($Id>0) echo '<input type="submit" class="button" value="Update '.$typelabel.'" name="submit">';
else echo '<input type="submit" class="button" value="Add '.$typelabel.'" name="submit">';

include_once('footer.php');
?>
