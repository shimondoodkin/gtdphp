<?php
//INCLUDES
include_once('header.php');

// query theme directory to build dropdown selector
$themedir = "./themes";
if ($handle = opendir($themedir)) {
	while (false !== ($file = readdir($handle))) {
		if ($file[0] !== "." && is_dir($themedir. "/" . $file)) {
			$themes[] = $file;
		}
	}
	closedir($handle);
}

$html="";

// ran into a strange PHP bug when using "foreach ($themes as $theme)", so just using $t
foreach ($themes as $t) {
	$html.= '<option value="'.$t;
	$html.='"';
	if($t == $_SESSION['theme']) $html.=" selected='selected' ";
	$html.='>'.$t;
	$html.="</option>";
	$html.="\n";
}
?>

<h2>Preference</h2>
<form action="updatePreferences.php" method="post">
    <div class='formrow'>
        <label for='theme'>Theme:</label>
        <select id='theme' name="theme">
            <?php echo $html; ?>
        </select>
        <input type="submit" class="button" value="Apply" name="submit" />
    </div>
</form>
<?php include_once('footer.php'); ?>
