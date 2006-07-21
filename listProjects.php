<?php

//INCLUDES
	include_once('header.php');
	include_once('config.php');
	include_once('gtdfuncs.php');

//CONNECT TO DATABASE
	$connection = mysql_connect($host, $user, $pass) or die ("Unable to connect");
	mysql_select_db($db) or die ("Unable to select database!");

//RETRIEVE URL VARIABLES
$categoryId=(int) $_POST['categoryId'];
$pType=$_GET["pType"]{0};

if ($pType=="s") {
	$completed="n";
	$isSomeday="y";
	$typename="Someday/Maybe";
	}

elseif ($pType=="c") {
	$completed="y";
	$pType="p";
	$isSomeday="n";
	$typename="Projects";
	}

else {
	$completed="n";
	$pType="p";
	$isSomeday="n";
	$typename="Projects";
	}


//SQL CODE
//select all categories for dropdown list
$query = "SELECT categories.categoryId, categories.category, categories.description from categories ORDER BY categories.category ASC";
$result = mysql_query($query) or die("Error in query");
$cshtml="";
while($row = mysql_fetch_assoc($result)) {
        $cshtml .= '<option value="'.$row['categoryId'].'" title="'.htmlspecialchars(stripslashes($row['description'])).'"';
	if($row['categoryId']==$categoryId) $cshtml .= ' SELECTED';
	$cshtml .= '>'.stripslashes($row['category']).'</option>\n';
        }
mysql_free_result($result);

if ($completed=="y") $compq = "projectstatus.dateCompleted > 0";
else $compq = "(projectstatus.dateCompleted IS NULL OR projectstatus.dateCompleted = '0000-00-00')
                AND (((CURDATE()>=DATE_ADD(projectattributes.deadline, INTERVAL -(projectattributes.suppressUntil) DAY))
                    OR projectattributes.suppress='n'))";

//Select Projects
if ($categoryId==NULL) $categoryId='0';
if ($categoryId=='0') {
	$query="SELECT projects.projectId, projects.name, projects.description, projectattributes.categoryId, categories.category,
		projectattributes.deadline, projectattributes.repeat, projectattributes.suppress, projectattributes.suppressUntil 
		FROM projects, projectattributes, projectstatus, categories 
		WHERE projectattributes.projectId=projects.projectId AND projectattributes.categoryId=categories.categoryId 
		AND projectstatus.projectId=projects.projectId AND projectattributes.isSomeday = '$isSomeday' AND ".$compq."
		ORDER BY categories.category, projects.name ASC";

	$result = mysql_query($query) or die ("Error in query");
	}

else {
	$query="SELECT projects.projectId, projects.name, projects.description, projectattributes.categoryId, categories.category, 
		projectattributes.deadline, projectattributes.repeat, projectattributes.suppress, projectattributes.suppressUntil 
		FROM projects, projectattributes, projectstatus, categories 
		WHERE projectattributes.projectId=projects.projectId AND projectattributes.categoryId=categories.categoryId 
		AND projectstatus.projectId=projects.projectId AND (".$compq.") AND projectattributes.categoryId='$categoryId' 
		AND projectattributes.isSomeday='$isSomeday'
		ORDER BY categories.category, projects.name ASC";
	$result = mysql_query($query) or die ("Error in query");
	}

if (mysql_num_rows($result) > 0){   

//PAGE DISPLAY CODE

	echo '<h2>';
	if ($completed=="y") echo 'Completed&nbsp;'.$typename.'</h2>';
	else echo '<a href="project.php?type='.$pType.'" title="Add new '.str_replace("s","",$typename).'">'.$typename.'</a></h2>';
	
	//category selection form
	echo '<form action="listProjects.php?pType='.$pType.'" method="post">';
	echo '<p>Category:';
	echo '&nbsp;<select name="categoryId">';
	echo '<option value="0">All</option>';
	echo $cshtml.'</select>';
	echo '<input type="submit" class="button" value="Filter" name="submit" title="Filter '.$typename.' by category" /></p>';
	echo '</form>';

//Project Update form
	echo "<p>Select project for individual report.</p>";
	echo '<form action="processProjectUpdate.php" method="post">';
	echo "<table>";
	echo '<tr>';
	echo '<th>Title</th>';
	echo '<th>Description</th>';
	echo '<th>Category</th>';
	echo '<th>Deadline</th>';
	echo '<th>Repeat</th>';
	echo '<th>Edit</th>';
	if ($completed!="y") echo '<th>Completed</th>';
	echo '</tr>';

	while($row = mysql_fetch_assoc($result)){
		echo "<tr>";
		echo '<td>';
		$nonext=nonext($row['projectId']);		
		if ($nonext=="true" && $completed!="y") echo '<font color="red"><strong title="No next action defined">!&nbsp;</strong></font>';
		echo '<a href = "projectReport.php?projectId='.$row['projectId'].'" title="Go to '.htmlspecialchars(stripslashes($row['name'])).' project report">'.stripslashes($row['name']).'</a></td>';
		echo '<td>'.nl2br(stripslashes($row['description'])).'</td>';
		echo '<td><a href="editCategory.php?categoryId='.$row['categoryId'].'" title="Edit the '.htmlspecialchars(stripslashes($row['category'])).' category">'.stripslashes($row['category']).'</a></td>'; 
		echo '<td>';
                if(($row['deadline']) == "0000-00-00" || $row['deadline']==NULL) $tablehtml .= "&nbsp;";
                elseif(($row['deadline']) < date("Y-m-d") && $completed!="y") echo '<font color="red"><strong title="Project overdue">'.date("D M j, Y",strtotime($row['deadline'])).'</strong></font>';
                elseif(($row['deadline']) == date("Y-m-d") && $completed!="y") echo '<font color="green"><strong title="Project due today">'.date("D M j, Y",strtotime($row['deadline'])).'</strong></font>';
                else echo date("D M j, Y",strtotime($row['deadline']));

		echo '</td>';
		if ($row['repeat']=="0") echo "<td>--</td>";
		else echo "<td>".$row['repeat']."</td>";
		echo '<td><a href="project.php?projectId='.$row['projectId'].'" title="Edit '.htmlspecialchars(stripslashes($row['name'])).' project">Edit</a></td>';
                if ($completed!="y") echo '<td align="center"><input type="checkbox" align="center" title="Mark '.htmlspecialchars(stripslashes($row['name'])).' project completed. Will hide incomplete associated items." name="completedProj[]" value="'.$row['projectId'].'" /></td>';
		echo "</tr>";
		}
	echo "</table>";
	echo '<input type="hidden" name="referrer" value="l" />';
	echo '<input type="hidden" name="type" value="'.$pType.'" />';
        echo '<input type="submit" class="button" value="Complete '.$typename.'" name="submit" /></form>';
	}

else echo "<h4>Nothing was found</h4>";

	mysql_free_result($result);
	mysql_close($connection);
	include_once('footer.php');
?>

