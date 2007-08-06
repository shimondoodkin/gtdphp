<?php
//INCLUDES
include_once('headerDB.inc.php');
$html=(!isset($_GET['type']) || ($config['debug'] & _GTD_DEBUG));

if ($html) include_once('header.php');

$values=array('itemId' => (int) $_GET['itemId']);
if (isset($_GET['type'])) {
    $values['type'] = $_GET['type'];
    query("updateitemtype",$config,$values,$options,$sort);
    query("touchitem",$config,$values,$options,$sort);
    nextScreen("item.php?itemId={$values['itemId']}");
    return;
} else {
    $types=getTypes();
    unset($types['i']);
    unset($types['s']);
    $result = query("selectitemshort",$config,$values,$options,$sort);
    ?>
    <h2>Assign type to inbox item: <?php echo makeclean($result[0]['title']); ?></h2>
    <form action='assignType.php' method='get'>
    <div class='formrow'>
        <?php foreach ($types as $key=>$val) { ?>
            <button name="type" value="<?php echo $key; ?>" type="submit"><?php echo $val; ?></button>
        <?php } ?>
        <input type='hidden' name='itemId' value='<?php echo $values['itemId']; ?>' />
    </div>
    </form>
<?php
}
if ($html) include('footer.php')
?>
