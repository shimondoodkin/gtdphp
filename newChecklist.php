<?php
if (!isset($_POST['submit'])) {
	//form not submitted
    include_once('header.php');
    $cashtml=categoryselectbox($config,array('categoryId'=>0),$sort);
?>
<h1>New Checklist</h1>

<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
	<div class='form'>
		<div class='formrow'>
			<label for='title' class='left first'>Title:</label>
			<input type="text" name="title" id="title" />
		</div>

		<div class='formrow'>
			<label for='category' class='left first'>Category:</label>
			<select name='categoryId' id='category'>
                <?php echo $cashtml; ?>
			</select>
		</div>

		<div class='formrow'>
			<label for='description' class='left first'>Description:</label>
			<textarea rows="10" cols="80" name="description" id="description"></textarea>
		</div>
	</div>
	<div class='formbuttons'>
		<input type="submit" value="Add Checklist" name="submit" />
	</div>
</form>

<?php
}else {
    include_once('headerDB.inc.php');
    $values = array();
    $values['title'] = empty($_POST['title']) ? die("Error: Enter a checklist title") : $_POST['title'];
    $values['description'] = empty($_POST['description']) ? die("Error: Enter a checklist description") : $_POST['description'];
    $values['categoryId'] = (int) $_POST['categoryId'];

    $result= query("newchecklist",$config,$values,$sort);

    $_SESSION['message'][]=($GLOBALS['ecode']=="0")?("Checklist: ".$values['title']." inserted."):"Checklist NOT inserted.";
    if ($GLOBALS['ecode']!="0")
        $_SESSION['message'][]="Error Code: ".$GLOBALS['ecode']."=> ".$GLOBALS['etext'];

	$url='checklistReport.php?checklistId='.mysql_insert_id();
    nextScreen($url);
}
include_once('footer.php');
?>
