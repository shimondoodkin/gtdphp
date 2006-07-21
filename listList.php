<?php

//INCLUDES
	include_once('header.php');
	include_once('config.php');

//CONNECT TO DATABASE
	$connection = mysql_connect($host, $user, $pass) or die ("unable to connect");
	mysql_select_db($db) or die ("unable to select database!");

		echo "<h2>Lists</h2>";

//SJK  Allows viewing of checklists by category
                echo '<form action="listList.php" method="post">';
                echo '<p>Category:';
                $categoryId=(int)$_POST['categoryId'];

//SELECT categoryId, category.name FROM categories ORDER BY category.name ASC

                $query = "select * from categories";
                $result = mysql_query($query) or die("Error in query");
                echo '&nbsp;<select name="categoryId">';
                echo '<option value="0">All</option>';
                while($row = mysql_fetch_row($result)){
                if($row[0]==$categoryId){
                        echo "<option selected value='" .$row[0] . "'>".stripslashes($row[1])."</option>\n";
                } else {
                        echo "<option value='" .$row[0] . "'>" .stripslashes($row[1]). "</option>\n";
                        }
                }
                echo '</select>';
                mysql_free_result($result);
                echo '<input type="submit" align="right" class="button" value="Update" name="submit"></p>';  // $


        if ($categoryId==NULL) $categoryId='0';
        if ($categoryId=='0') {
               $query = "SELECT list.listId, list.title, list.description,
		list.categoryId, categories.category 
		FROM list, categories 
		WHERE list.categoryId=categories.categoryId 
		ORDER BY categories.category ASC";
                $result = mysql_query($query) or die ("Error in query");
                }
        else {
               $query = "SELECT list.listId, list.title, list.description,
		list.categoryId, categories.category 
		FROM list, categories 
		WHERE list.categoryId=categories.categoryId AND list.categoryId='$categoryId' 
		ORDER BY categories.category ASC";
                $result = mysql_query($query) or die ("Error in query");
                }


	if (mysql_num_rows($result) > 0){
		echo "Select list for report.";
		echo "<table>";
		echo '<tr>';
		echo '<th>Category</th>';
		echo '<th>Title</th>';
		echo '<th>Description</th>';
		echo '</tr>';
		while($row = mysql_fetch_row($result)){
			echo "<tr>";
			echo "<td>".stripslashes($row[4])."</td>";
			echo '<td><a href="listReport.php?listId='.$row[0].'&listTitle='.urlencode($row[1]).'">'.stripslashes($row[1]).'</a></td>';
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
