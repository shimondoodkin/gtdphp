<?php
//INCLUDES
include_once('header.php');

//CONNECT TO DATABASE
$connection = mysql_connect($host, $user, $pass) or die ("Unable to connect!");
mysql_select_db($db) or die ("Unable to select database!");

//FORM DATA COLLECTION AND PARSING
$values['title'] = mysql_real_escape_string($_POST['title']);
$values['description'] = mysql_real_escape_string($_POST['description']);
$values['projectId'] = (int) $_POST['projectId'];
$values['contextId'] = (int) $_POST['contextId'];
$values['completed'] = $_POST['completed'];
$values['timeframeId'] = (int) $_POST['timeframeId'];
$values['dateCompleted'] = $_POST['dateCompleted'];
$values['delete'] = $_POST['delete']{0};
$values['itemId'] = (int) $_GET['itemId'];
$values['repeat'] = (int) $_POST['repeat'];
$values['deadline'] = $_POST['deadline'];
$values['suppress'] = $_POST['suppress']{0};
$values['suppressUntil'] = (int) $_POST['suppressUntil'];
$values['type']=$_POST['type']{0};
$values['nextAction']=$_POST['nextAction']{0};

if ($values['suppress']!="y") $values['suppress']="n";

//SQL CODE AREA
if($values['delete']=="y"){

    query("deleteitemstatus",$config,$values);
    query("deleteitemattributes",$config,$values);
    query("deleteitem",$config,$values);

        echo '<META HTTP-EQUIV="Refresh" CONTENT="0; url=projectReport.php?projectId='.$projectId.'" />';
//        echo "<p>Number of Records Deleted: ";
//        echo mysql_affected_rows();
	if(values['nextAction']=='y')query("deletenextaction",$config,$values);
	}

else {
        $projectTitle=getProjectTitle($projectId);

        $query = "UPDATE items
            SET description = '$description', title = '$title'
            WHERE itemId = '$itemId'";
        $result = mysql_query($query) or die ("Error in query");

	$query = "UPDATE itemattributes
		SET type = '$type', projectId = '$projectId', contextId = '$contextId', timeframeId = '$timeframeId', 
		deadline ='$deadline', `repeat` = '$repeat', suppress='$suppress', suppressUntil='$suppressUntil' 
		WHERE itemId = '$itemId'";
        $result = mysql_query($query) or die ("Error in query");

	$query = "UPDATE itemstatus
		SET dateCompleted = '$dateCompleted'
		WHERE itemId = '$itemId'";
        $result = mysql_query($query) or die ("Error in query");

	if($values['nextAction']=='y') {
		$query = "INSERT INTO nextactions (projectId,nextaction) VALUES ('$projectId','$itemId') 
			ON DUPLICATE KEY UPDATE nextaction='$itemId'";
       		$result = mysql_query($query) or die ("Error in query");
		}

	else {
	        $query= "DELETE FROM nextactions WHERE nextAction='$itemId'";
       		$result = mysql_query($query) or die ("Error in query");
		}


        echo '<META HTTP-EQUIV="Refresh" CONTENT="0; url=projectReport.php?projectId='.$projectId.'" />';
	}

mysql_close($connection);
include_once('footer.php');
?>
