<?php
define("_DEBUG",false);
require_once('header.php');
require_once('admin.inc.php');

/*
Structure: scan for available installations.  OFfer to validate or delete each.
Use a javascript onsubmit for the delete verification, and fallback to POST.

LOCK TABLES if possible, while doing admin.

------------------------------------------------------------
NB: you cannot use a locked table multiple times in a single query.
Use aliases instead, in which case you must obtain a lock for each alias separately.
mysql> LOCK TABLE t WRITE, t AS t1 WRITE;
mysql> INSERT INTO t SELECT * FROM t;
ERROR 1100: Table 't' was not locked with LOCK TABLES
mysql> INSERT INTO t SELECT * FROM t AS t1;
------------------------------------------------------------

Add new data-cleaning items, using NOT IN:

1) remove from lookup table if parent or child doesn't exist.
    Advise them to check orphans afterwards.
    Advise them to check listItems for items flagged as having no children,

2) remove from nextactions table if parent or child doesn't exist.
    Check to see if this would mean a current NA loses NA status
    Advise to check listItems for projects with no NA
*/
$action=(isset($_REQUEST['action']))?$_REQUEST['action']:'none';
$showInstallations=true;
$showCommands=true;
$prefix=(isset($_REQUEST['prefix']))?$_REQUEST['prefix']:$config['prefix'];
?>
<h1>gtd-php Admin Tasks</h1>
<?php if ($action==='delete') { ?>
<h2>Delete installation</h2>


<?php }
    if ($action==='validate') {
?>  <h2>Validation checks on installation with prefix '<?php echo $_REQUEST['prefix']; ?>'</h2>
    <p>Estimate of number of errors. NB some errors may overlap, so the total that would be fixed may differ from these figures</p>
    <table summary='validation checks'>
    <thead>
    <?php
        $result=checkErrors($prefix);
        foreach($result['totals'] as $key=>$val)
            echo "<tr><td>$val</td><th>$key</th></tr>\n";
    ?></thead><tbody><?php
        foreach($result['errors'] as $key=>$val) {
            $class=($val)?" class='error' ":'';
            echo "<tr><td $class>$val</td><td $class>$key</td></tr>\n";
        }
    ?></tbody></table>
<?php } if ($showInstallations || $showCommands) { ?>
    <h2>Action</h2>
    <form action='admin.php'>
    <?php if ($showInstallations) { ?>
        <h3>Detected installations in this database</h3>
        <p>Pick one to operate on:</p>
        <div class='formrow'>
            <label class='left first' for='prefix'>prefix</label><input id='prefix' type='text' name='prefix'
            value='<?php echo $prefix; ?>' />
        </div>
    <?php } if ($showCommands) { ?>
        <h3>Action to take:</h3>
        <div class='formrow'>
            <label class='left first'>Action</label>
            <input type='radio' name='action' value='validate' checked='checked' />Validate
<!-- >
            <input type='radio' id='action' name='action' value='delete' />Delete
            <input type='radio' name='action' value='clean' />Clean
< -->
        </div>
    <?php } ?>
    </form>
<?php } ?>
<?php require_once('footer.php'); ?>
