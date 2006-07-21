<?php

//INCLUDES
include_once('header.php');
include_once('config.php');

//RETRIEVE URL VARIABLES
$pId = (int) $_GET['projectId'];

//SQL CODE AREA
$connection = mysql_connect($host, $user, $pass) or die ("unable to connect");
mysql_select_db($db) or die ("Unable to select database!");

//GET project details
$query = "SELECT projects.name, projects.description, projects.desiredOutcome, projectstatus.dateCreated, 
	projectstatus.dateCompleted, projectstatus.lastModified, projectattributes.deadline, projectattributes.repeat, 
	projectattributes.suppress, projectattributes.suppressUntil, projectattributes.isSomeday
	FROM projects,projectattributes, projectstatus 
	WHERE projectstatus.projectId = projects.projectId AND projectattributes.projectId = projects.projectId AND
	projects.projectId = '$pId'";
$result = mysql_query($query) or die ("Error in query");
$project = mysql_fetch_assoc($result);
if ($project['isSomeday']=="y") $pType="s";
else $pType="p";
mysql_free_result($result);

//Function to select items of a specific type
function doitemquery($projectId,$type,$completed='n') {
	if ($completed=="y") $compq = "itemstatus.dateCompleted > 0";
	else $compq = "itemstatus.dateCompleted IS NULL OR itemstatus.dateCompleted = '0000-00-00'";

	$query = "SELECT items.itemId, items.title, items.description, itemstatus.dateCreated, itemstatus.dateCompleted,
		context.contextId, context.name AS cname, itemattributes.deadline, itemattributes.repeat, 
		itemattributes.suppress, itemattributes.suppressUntil
		FROM items, itemattributes, itemstatus, context
		WHERE itemstatus.itemId = items.itemId AND itemattributes.itemId = items.itemId AND
		itemattributes.contextId = context.contextId AND itemattributes.projectId = '$projectId' 
		AND itemattributes.type = '$type' AND (".$compq.") ORDER BY items.title ASC, cname ASC";
	$result = mysql_query($query) or die ("Error in query");
	return $result;
	}

//select all nextactions for test
$query = "SELECT projectId, nextaction FROM nextactions";
$result = mysql_query($query) or die ("Error in query");
$nextactions = array();
while ($nextactiontest = mysql_fetch_assoc($result)) {
        //populates $nextactions with itemIds using projectId as key
        $nextactions[$nextactiontest['projectId']] = $nextactiontest['nextaction'];
        }


//PAGE DISPLAY AREA
if ($pType=="s") $typename="Someday/Maybe";
else $typename="Project";

echo '<form action="processItemUpdate.php?projectId='.$pId.'" method="post">';

echo "<h1>".$typename."&nbsp;Report:&nbsp;".stripslashes($project['name'])."</h1>";
echo '[ <a href="project.php?projectId='.$pId.'" title="Edit '.stripslashes($project['name']).'">Edit</a> ]';
echo '<p>Created:'.$project['dateCreated'];
echo '<br />Description:&nbsp;'.stripslashes($project['description']);
if ($project['desiredOutcome']!="") echo '<br />Desired Outcome:&nbsp;'.stripslashes($project['desiredOutcome']);
if ($project['deadline']!=NULL && $project['deadline']!="0000-00-00") echo '<br />Deadline:&nbsp;'.$project['deadline'];
if ($project['repeat']>0) echo '<br />Repeat every&nbsp;'.$project['repeat'].'&nbsp;days';
if ($project['suppress']=='y') echo '<br />Suppressed Until:&nbsp;'.$project['suppressUntil'];
if ($project['dateCompleted']>0) echo '<br />Completed On:&nbsp;'.$project['dateCompleted'];
echo "</p>";
//Create iteration arrays
$type = array("a","w","r");
$typelabel = array("a" => "Actions","w" => "Waiting On", "r" => "References");
$completed = array("n","y");

//table display loop
foreach ($completed as $comp) {
foreach ($type as $value) {

	if ($comp=="y") echo '<h2>Completed&nbsp;'.$typelabel[$value].'</h2>';
	else echo '<h2><a href = "item.php?type='.$value.'&projectId='.$pId.'&pType='.$pType.'" title="Add new '.str_replace("s","",$typelabel[$value]).'">'.$typelabel[$value].'</a></h2>';

	$result=doitemquery($pId,$value,$comp);
	if (mysql_num_rows($result) > 0) {
		$counter=0;
		echo "<table>";
		echo '<th>'.$typelabel[$value].'</th><th>Description</th><th>Context</th><th>Date Created</th>';
		if ($comp=="n") echo '<th>Deadline</th><th>Repeat</th><th>Suppress</th><th>Completed</th>';
		echo '</tr>';

		while($row = mysql_fetch_assoc($result)) {
			echo "<tr>";

                        //if nextaction, add icon in front of action (* for now)
                        if ($key = array_search($row['itemId'],$nextactions)) echo '<td><a href = "item.php?itemId='.$row['itemId'].'&pType='.$pType.'" title="Edit '.htmlspecialchars(stripslashes($row['title'])).'">*&nbsp;'.stripslashes($row['title']).'</a></td>';
                        else echo '<td><a href = "item.php?itemId='.$row['itemId'].'&pType='.$pType.'" title="Edit '.htmlspecialchars(stripslashes($row['title'])).'">'.stripslashes($row['title']).'</a></td>';


        		echo '<td>'.nl2br(stripslashes($row['description'])).'</td>';
			echo '<td><a href = "reportContext.php?contextId='.$row['contextId'].'" title="Go to '.htmlspecialchars(stripslashes($row['cname'])).' context report">'.stripslashes($row['cname']).'</a></td>';
			echo "<td>".date("D M j, Y",strtotime($row['dateCreated']))."</td>";

			if ($comp=="n") {
				echo "<td>";
				//Blank out empty deadlines
				if(($row['deadline']) == "0000-00-00") echo "&nbsp;";
				//highlight overdue actions
				elseif(($row['deadline']) < date("Y-m-d")) echo '<font color="red"><strong title="Overdue">'.date("D M j, Y",strtotime($row['deadline'])).'</strong></font>';
				//highlight actions due
				elseif(($row['deadline']) == date("Y-m-d")) echo '<font color="green"><strong title="Due today">'.date("D M j, Y",strtotime($row['deadline'])).'</strong></font>';
				else echo date("D M j, Y",strtotime($row['deadline']));
				echo "</td>";

				if ($row['repeat']=="0") echo '<td>--</td>';
				else echo "<td>".$row['repeat']."</td>";

				if ($row['suppress']=="y") $suppressText=$row['suppressUntil'];
				else $suppressText="--";
				echo "<td>".$suppressText."</td>";

				echo '<td align=center><input type="checkbox" align="center" name="completedNas[]" title="Complete '.htmlspecialchars(stripslashes($row['title'])).'" value="';  
				echo $row['itemId'];
				echo '"></td>';
				}

	                echo "</tr>";
	                $counter = $counter+1;
			}
		echo "</table>";
		echo '<input type="hidden" name="referrer" value="p">';
		if ($comp=="n") echo '<input type="submit" align="right" class="button" value="Complete '.$typelabel[$value].'" name="submit">';

			if($counter==0){
				echo 'No&nbsp;'.$typelabel[$value].'&nbsp;items';
				}
			}
		else echo "None";
	}
}

echo '</form>';
mysql_free_result($result);
mysql_close($connection);
include_once('footer.php');
?>
