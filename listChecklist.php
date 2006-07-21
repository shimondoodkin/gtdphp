<?php
	include_once('header.php');
	include_once('config.php');
	$connection = mysql_connect($host, $user, $pass) or die ("unable to connect");

	mysql_select_db($db) or die ("unable to select database!");
		echo "<h2>Checklists</h2>";

//SELECT categoryId, category, description FROM categories ORDER by category ASC

//SJK  Allows viewing of checklists by category
                echo '<form action="listChecklist.php" method="post">';
                echo '<p>Category:';
                $categoryId=(int) $_POST['categoryId'];
                $query = "select * from categories";
                $result = mysql_query($query) or die("Error in query");
                echo '&nbsp;<select name="categoryId" title="Filter checklists by category">';
                echo '<option value="0">All</option>';
                while($row = mysql_fetch_row($result)){
                if($row[0]==$categoryId){
                        echo "<option selected value='" .$row[0] . "'>" .stripslashes($row[1])."</option>\n";
                } else {
                        echo "<option value='" .$row[0] . "'>" .stripslashes($row[1]). "</option>\n";
                        }
                }
                echo '</select>';
                mysql_free_result($result);
                echo '<input type="submit" align="right" class="button" value="Update" name="submit"></p>';  // $

        if ($categoryId==NULL) $categoryId='0';
        if ($categoryId=='0') {
               $query = "SELECT checklist.checklistId, checklist.title, checklist.description,
		checklist.categoryId, categories.category 
		FROM checklist, categories 
		WHERE checklist.categoryId=categories.categoryId 
		ORDER BY categories.category ASC";
                $result = mysql_query($query) or die ("Error in query");
                }
        else {
               $query = "SELECT checklist.checklistId, checklist.title, checklist.description,
		checklist.categoryId, categories.category 
		FROM checklist, categories 
		WHERE checklist.categoryId=categories.categoryId AND checklist.categoryId='$categoryId' 
		ORDER BY categories.category ASC";
                $result = mysql_query($query) or die ("Error in query");
                }


	if (mysql_num_rows($result) > 0){
		echo "Select checklist for report.";
		echo "<table>";
		echo '<tr>';
		echo '<th>Category</th>';
		echo '<th>Title</th>';
		echo '<th>Description</th>';
		echo '</tr>';
		while($row = mysql_fetch_row($result)){
			echo "<tr>";
			echo "<td>".stripslashes($row[4])."</td>";
			echo '<td><a href="checklistReport.php?checklistId='.$row[0].'&checklistTitle='.urlencode($row[1]).'">'.stripslashes($row[1]).'</a></td>';
			echo "<td>".stripslashes($row[2])."</td>";
			echo "</tr>";
		}
		echo "</table>";
	}
	else{
		echo "<h4>Nothing was found</h4>";
	}

	mysql_free_result($result);
	mysql_close($connection);
	include_once('footer.php');
?>
