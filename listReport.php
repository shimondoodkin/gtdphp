<?php
include_once('header.php');

$values = array();
$values['listId'] = (int) $_GET['listId'];

$result = query("selectlist",$config,$values,$options,$sort);

if ($result!="-1") {
    $row=$result[0];

    echo "<h1>List Report: ".$row['title']."</h1>\n";
    echo '<form action="processListUpdate.php?listId='.$row['listId'].'" method="post">'."\n";

    echo '<p><span class="editbar">[ <a href="editList.php?listId='.$row['listId'].'">Edit List</a> ]'."\n";
    echo "</span></p>\n";

    echo '<h2><a href = "newListItem.php?listId='.$row['listId'].'" style="text-decoration:none">List Items</a></h2>'."\n";

    $values['filterquery']= " AND ".sqlparts("activelistitems",$config,$values);
    $result=query("getlistitems",$config,$values,$options,$sort);
    if ($result!="-1") {
        $counter=0;

        echo '<table class="datatable sortable" id="itemtable" summary="table of list items">'."\n";
        echo "	<thead><tr>\n";
        echo "		<td>Item</td>\n";
        echo "		<td>Description</td>\n";
        echo "		<td>Completed</td>\n";
        echo "	</tr></thead>\n";

        foreach($result as $row) {
            echo '	<tr>'."\n";
            echo '		<td><a href = "editListItem.php?listItemId='.$row['listItemId'].'" title="Edit '.htmlspecialchars(stripslashes($row['item'])).'">'.htmlspecialchars(stripslashes($row['item'])).'</a></td>'."\n";
            echo '		<td>'.stripslashes($row['notes']).'</td>'."\n";
            echo '		<td><input type="checkbox" name="completedLis[]" title="Complete '.htmlspecialchars(stripslashes($row['item'])).'" value="'.$row['listItemId'].'" /></td>'."\n";
            echo '	</tr>'."\n";
            $counter = $counter+1;
            }
        echo "</table>\n";
        echo '<p><input type="submit" class="button" value="Update List Items" name="submit" />'."</p>\n";
        if($counter==0){
            echo "No list items";
            }
        }
    else echo "<p>None</p>";

    $values['filterquery']= " AND ".sqlparts("completedlistitems",$config,$sort);
    $result = query("getlistitems",$config,$values,$options,$sort);

    echo "<h2>Completed List Items</h2>\n";
    if ($result!="-1"){
            echo '<table class="datatable sortable" id="donetable" summary="completed list items">'."\n";
            echo "	<thead><tr>\n";
            echo "		<td>Item</td>\n";
            echo "		<td>Notes</td>\n";
            // echo '<td>Completed</td>';
            echo "	</tr></thead>\n";
            foreach($result as $row) {
                    echo "	<tr>\n";
            echo '               <td><a href = "editListItem.php?listItemId='.$row['listItemId'].'" title="Edit '.htmlspecialchars(stripslashes($row['item'])).'">'.htmlspecialchars(stripslashes($row['item'])).'</td>'."\n";
            echo '          <td>'.htmlspecialchars(stripslashes($row['notes'])).'</td>'."\n";
            echo "	</tr>\n";
            }
            echo "</table>\n";
        }
    else echo "<p>None</p>";
    
    echo "</form>\n";
    }

else echo "<p>Invalid List ID.</p>";


include_once('footer.php');
?>
