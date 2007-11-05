<?php
    //INCLUDES
    include_once('header.php');
    $values=array('itemId' => (int) $_GET['itemId']);
    $types=getTypes();
    $result = query("selectitemshort",$config,$values,$sort);
    $type=$result[0]['type'];
    $typename=getTypes($type);
    $title=makeclean($result[0]['title']);
    unset($types[$type]);
    unset($types['s']);
?><h2>Change the Type of <?php echo "$typename: '$title'"; ?></h2>
<form action='processItems.php' method='post'>
<div class='formrow'>
    <?php foreach ($types as $key=>$val) { ?>
        <button name="type" value="<?php echo $key; ?>" type="submit"><?php echo $val; ?></button>
    <?php } ?>
    <input type='hidden' name='itemId' value='<?php echo $values['itemId']; ?>' />
    <input type='hidden' name='referrer' value='item.php?itemId=<?php echo $values['itemId']; ?>' />
    <input type='hidden' name='action' value='changeType' />
</div>
</form>
<?php include('footer.php') ?>
