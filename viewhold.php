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
if ($type="") $type="v";
$valueId= (int) $_GET['valueId'];
$visionId= (int) $_GET['visionId'];
$goalId= (int) $_GET['goalId'];
$respId= (int) $_GET['respId'];

switch ($type) {
case "v" : $typelabel="Value";
break;
case "e" : $typelabel="Vision";
break;
case "g" : $typelabel="Goal";
break;
case "r" : $typelabel="Responsibility";
break;
}

//SQL CODE AREA
//select all values for dropdown list
$query = "SELECT valueId, value, description FROM `values` ORDER BY value ASC";
$result = mysql_query($query) or die("Error in query 6");
$vshtml="";
while($row = mysql_fetch_assoc($result)) {
	$vshtml .= '&nbsp;&nbsp;<input type="checkbox" name="vselect[]" value="'.$row['valueId'].'" title="'.$row['description'].'"';
        if($row['valueId']==$valueId){
		$vshtml .= ' CHECKED';
		}
	$vshtml .= '/>&nbsp;'.stripslashes($row['value']);
        }
mysql_free_result($result);


//select all visions for dropdown list
$query = "SELECT visions.visionId, vision, description FROM visions, visionstatus 
	WHERE visions.visionId=visionstatus.visionId AND
	(visionstatus.dateCompleted IS NULL OR visionstatus.dateCompleted = '0000-00-00') ORDER BY vision ASC";
$result = mysql_query($query) or die("Error in query 5");
$eshtml="";
while($row = mysql_fetch_assoc($result)) {
	$eshtml .= '&nbsp;&nbsp;<input type="checkbox" name="eselect[]" value="'.$row['visionId'].'" title="'.$row['description'].'"';
        if($row['visionId']==$visionId){
		$eshtml .= ' CHECKED';
		}
	$eshtml .= '/>&nbsp;'.stripslashes($row['vision']);
        }
mysql_free_result($result);


//select all goals for dropdown list
$query = "SELECT goals_new.goalId AS goalId, goal, description, timeframe FROM goals_new, goalstatus 
	WHERE goals_new.goalId=goalstatus.goalId AND
	(goalstatus.dateCompleted IS NULL OR goalstatus.dateCompleted = '0000-00-00') ORDER BY goal ASC";
$result = mysql_query($query) or die("Error in query 4");
$gshtml="";
while($row = mysql_fetch_assoc($result)) {
	$gshtml .= '&nbsp;&nbsp;<input type="checkbox" name="gselect[]" value="'.$row['goalId'].'" title="'.$row['description'].'"';
        if($row['goalId']==$goalId){
		$gshtml .= ' CHECKED';
		}
	$gshtml .= '/>&nbsp;'.stripslashes($row['goal']);
        }
mysql_free_result($result);

//select all responsibilities for dropdown list
$query = "SELECT responsibilities.respId as respId, responsibility, description FROM responsibilities, respstatus 
	WHERE responsibilities.respId=respstatus.respId AND
	(respstatus.dateCompleted IS NULL OR respstatus.dateCompleted = '0000-00-00') ORDER BY responsibility ASC";
$result = mysql_query($query) or die("Error in query 3");
$rshtml="";
while($row = mysql_fetch_assoc($result)) {
	$rshtml .= '&nbsp;&nbsp;<input type="checkbox" name="rselect[]" value="'.$row['respId'].'" title="'.$row['description'].'"';
        if($row['respId']==$respId){
		$rshtml .= ' CHECKED';
		}
	$rshtml .= '/>&nbsp;'.stripslashes($row['responsibility']);
        }
mysql_free_result($result);

//select all projects for dropdown list (includes someday/maybes)
$query = "SELECT projects.projectId AS projectId, name, description FROM projects, projectstatus
	WHERE projects.projectId=projectstatus.projectId AND
	(projectstatus.dateCompleted IS NULL OR projectstatus.dateCompleted = '0000-00-00') ORDER BY name ASC";
$result = mysql_query($query) or die("Error in query 2");
$pshtml="";
while($row = mysql_fetch_assoc($result)) {
	$pshtml .= '&nbsp;&nbsp;<input type="checkbox" name="pselect[]" value="'.$row['projectId'].'" title="'.$row['description'].'"';
        if($row['projectId']==$projectId){
		$pshtml .= ' CHECKED';
		}
	$pshtml .= '/>&nbsp;'.stripslashes($row['name']);
        }
mysql_free_result($result);

//Get project details
if ($projectId>0) {
	$query= "SELECT projects.projectId, projects.name, projects.description, projects.desiredOutcome, 
		projectstatus.dateCreated, projectstatus.dateCompleted, projectattributes.categoryId, projectattributes.deadline,
		projectattributes.repeat, projectattributes.suppress, projectattributes.suppressUntil, projectattributes.isSomeday 
		FROM projects, projectattributes, projectstatus 
		WHERE projectstatus.projectId=projects.projectId and projectattributes.projectId=projects.projectId and 
		projects.projectId = '$projectId'";
	$result = mysql_query($query) or die ("Error in query 1");
	$row = mysql_fetch_assoc($result);
	mysql_free_result($result);
	if ($type=$row['isSomeday']=="y") $type='s';
	else $type='p';
	}

//PAGE DISPLAY CODE


if ($projectId>0) {
	echo "<h2>Edit&nbsp;".$typename."</h2>";	
	echo '<form action="updateview.php method="post">';
	}

else {
	echo "<h2>New&nbsp;".$typename."</h2>";
	echo '<form action="processview.php" method="post">';
	}


echo '<table border="0">';

echo '<tr><td colspan="2">50,000 ft. view: Values</td></tr>';
echo '<tr><td colspan="2">'.$vshtml.'</td></tr>';
echo '<tr><td colspan="2">40,000 ft. view: Visions</td></tr>';
echo '<tr><td colspan="2">'.$eshtml.'</td></tr>';
echo '<tr><td colspan="2">30,000 ft. view: Goals</td></tr>';
echo '<tr><td colspan="2">'.$gshtml.'</td></tr>';
echo '<tr><td colspan="2">20,000 ft. view: Areas of Responsibility</td></tr>';
echo '<tr><td colspan="2">'.$rshtml.'</td></tr>';
echo '<tr><td colspan="2">10,000 ft. view: Projects</td></tr>';
echo '<tr><td colspan="2">'.$pshtml.'</td></tr>';



echo '<td><input type="checkbox" name="isSomeday" value="y" title="Places project in Someday file"';
if ($type=='s') echo 'CHECKED';
echo '>&nbsp;Someday</td>';
echo '<td><input type="checkbox" name="delete" value="delete" title="Deletes project and ALL associated items">&nbsp;Delete&nbsp;'.$typename.'</td></tr>';
echo '<tr><td><input type="checkbox" name="suppress" value="y" title="Hides this project from the active view"';
if ($row['suppress']=="y") echo " CHECKED";
echo '>Tickle&nbsp;<input type="text" size="3" name="suppressUntil" value="'.$row['suppressUntil'].'">'.'&nbsp;days before deadline</td>';
echo '<td colspan="2">Deadline:&nbsp;';
DateDropDown(365,"deadline",$row['deadline']);
echo '</td></tr>';
echo '<tr><td>Repeat every&nbsp;<input type="text" name="repeat" size="3" value="'.$row['repeat'].'">&nbsp;days</td>';
echo '<td colspan="2">Completed:&nbsp;';

if ($row['dateCompleted']=="0000-00-00" || $row['dateCompleted']==NULL) {
        DateDropDown(365,"dateCompleted",$currentrow['dateCompleted']);
        }
else echo '<input type="text" size="10" value="'.$row['dateCompleted'].'" />';
echo '</td></tr>';
echo '</table>';

echo '<table>';
echo '<tr><td colspan="3">Project Name</td></tr>';
echo '<tr><td colspan="3">';
echo '<input type="text" name="name" size="79" value="'.stripslashes($row['name']).'"></td></tr>';
echo '<tr><td colspan="2">Description</td></tr>';
echo '<tr><td colspan="2">';
echo '<textarea cols="77" rows="8" name="description" wrap=virtual">'.stripslashes($row['description']).'</textarea></td></tr>';
echo '<tr><td colspan="2">Desired Outcome</td></tr>';
echo '<tr><td colspan="2"><textarea cols="77" rows="4" name="outcome" wrap=virtual">'.stripslashes($row['desiredOutcome']).'</textarea></td></tr>';
echo '</table>';

if ($projectId>0) {
        echo '<table>';
        echo '<tr><td>Date Added:&nbsp;'.$row['dateCreated'].'</td>';
        echo '<td>Last Modified:&nbsp;'.$row['lastModified'].'</td></tr>';
        echo '</tr>';
        echo '</table>';
        }

echo '<br>';
echo '<input type="hidden" name="type" value='.$type.'" />';

if ($projectId>0) {
	echo '<input type="submit" class="button" value="Update '.$typename.'" name="submit">';
	}

else echo '<input type="submit" class="button" value="Add '.$typename.'" name="submit">';

include_once('footer.php');
?>
