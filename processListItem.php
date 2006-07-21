<?php
//INCLUDES
	include_once('header.php');
	include_once('config.php');

//CONNECT TO DATABASE
	$connection = mysql_connect($host, $user, $pass) or die ("Unable to connect!");
	mysql_select_db($db) or die ("Unable to select database!");

//RETRIEVE URL AND FORM VARIABLES
	$listId=(int) $_POST['listId'];
	$item=mysql_real_escape_string($_POST['item']);
	$notes=mysql_real_escape_string($_POST['notes']);


    echo '<META HTTP-EQUIV="Refresh" CONTENT="1; url=listReport.php?listId='.$listId.'"';
	echo '<p>New list item added at ';
	echo date('H:i, jS F');
	echo '</p>';

	# don't forge null
	$query = "INSERT into listItems values (NULL, '$item', '$notes', '$listId', 'n')";
	$result = mysql_query($query) or die ("Error in query");

	echo "New record inserted with ID ".mysql_insert_id();

	mysql_close($connection);
	include_once('footer.php');

?>
