<?php
//INCLUDES
include_once('gtdfuncs.php');
include_once('header.php');
include_once('config.php');

//RETRIEVE URL VARIABLES
$projectId =(int) $_GET["projectId"];
$type = $_GET['type']{0};

//SQL CODE
$connection = mysql_connect($host, $user, $pass) or die ("Unable to connect");
mysql_select_db($db) or die ("unable to select database!");

//Get project details
if ($projectId>0) {
	$query= "SELECT projects.projectId, projects.name, projects.description, projects.desiredOutcome, 
		projectstatus.dateCreated, projectstatus.dateCompleted, projectattributes.categoryId, projectattributes.deadline,
		projectattributes.repeat, projectattributes.suppress, projectattributes.suppressUntil, projectattributes.isSomeday 
		FROM projects, projectattributes, projectstatus 
		WHERE projectstatus.projectId=projects.projectId and projectattributes.projectId=projects.projectId and 
		projects.projectId = '$projectId'";
	$result = mysql_query($query) or die ("Error in query");
	$row = mysql_fetch_assoc($result);
	mysql_free_result($result);
	if ($type=$row['isSomeday']=="y") $type='s';
	else $type='p';
	}

//select all categories for dropdown list
$query = "SELECT categories.categoryId, categories.category from categories ORDER BY categories.category ASC";
$result = mysql_query($query) or die("Error in query");
$cshtml="";
while($catrow = mysql_fetch_assoc($result)) {
        if($catrow['categoryId']==$row['categoryId']){
                $cshtml .= "<option selected value='" .$catrow['categoryId'] . "'>" . stripslashes($catrow['category']) . "</option>\n";
                }
        else {
                $cshtml .= "<option value='" .$catrow['categoryId'] . "'>" . stripslashes($catrow['category']) . "</option>\n";
                }
        }
mysql_free_result($result);

//PAGE DISPLAY CODE
//determine project labels
if ($type=="s") $typename="Someday/Maybe";
else $typename="Project";

if ($projectId>0) {
	echo "<h2>Edit&nbsp;".$typename."</h2>";	
	echo '<form action="updateProject.php?projectId='.$projectId.'" method="post">';
	}

else {
	echo "<h2>New&nbsp;".$typename."</h2>";
	echo '<form action="processProject.php" method="post">';
	}


echo '<table border="0">';
echo '<tr><td>Category&nbsp;<select name="categoryId">'.$cshtml.'</select></td>';
echo '<td><input type="checkbox" name="isSomeday" value="y" title="Places project in Someday file"';
if ($type=='s') echo 'CHECKED';
echo '>&nbsp;Someday</td>';
echo '<td><input type="checkbox" name="delete" value="y" title="Deletes project and ALL associated items">&nbsp;Delete&nbsp;'.$typename.'</td></tr>';
echo '<tr><td><input type="checkbox" name="suppress" value="y" title="Hides this project from the active view"';
if ($row['suppress']=="y") echo " CHECKED";
echo '>Tickle&nbsp;<input type="text" size="3" name="suppressUntil" value="'.$row['suppressUntil'].'">'.'&nbsp;days before deadline</td>';
echo '<td colspan="2">Deadline:&nbsp;';
if ($row['deadline']=="0000-00-00" || $row['deadline']==NULL || $row['deadline']>date("Y-m-d")) {
	DateDropDown(365,"deadline",$row['deadline']);
	}
else echo '<input type="text" size="10" name="deadline" value="'.$row['deadline'].'" />';
echo '</td></tr>';
echo '<tr><td>Repeat every&nbsp;<input type="text" name="repeat" size="3" value="'.$row['repeat'].'">&nbsp;days</td>';
echo '<td colspan="2">Completed:&nbsp;';

if ($row['dateCompleted']=="0000-00-00" || $row['dateCompleted']==NULL) {
        DateDropDown(365,"dateCompleted",$currentrow['dateCompleted']);
        }
else echo '<input type="text" size="10" name="dateCompleted" value="'.$row['dateCompleted'].'" />';
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
