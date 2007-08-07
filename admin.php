<?php

    /* _DEBUG = false | true -
    show lots of debugging information during execution */
define("_DEBUG",false);

    /* _DRY_RUN = false | true - dry run won't change the database, but will
    mime all the actions that would be done: use _DEBUG true to see these */
define("_DRY_RUN",false);


define("_ALLOWUNINSTALL",false); // NOT YET ACTIVE

require_once('header.php');
require_once('admin.inc.php');

/*
TOFIX: scan for available installations
TOFIX: Use a javascript onsubmit for the delete verification, and fallback to POST if no javascript
TOFIX: move DELETE from install.php to here

------------------------------------------------------------
TOFIX: LOCK TABLES if possible, while doing admin.
NB: you cannot use a locked table multiple times in a single query.
Use aliases instead, in which case you must obtain a lock for each alias separately.
mysql> LOCK TABLE t WRITE, t AS t1 WRITE;
mysql> INSERT INTO t SELECT * FROM t;
ERROR 1100: Table 't' was not locked with LOCK TABLES
mysql> INSERT INTO t SELECT * FROM t AS t1;
------------------------------------------------------------

*/
$action=(isset($_REQUEST['action']))?$_REQUEST['action']:'validate';
$showInstallations=true;
$showCommands=true;
$prefix=(isset($_REQUEST['prefix']))?$_REQUEST['prefix']:$config['prefix'];
if (!checkPrefix($prefix)) $prefix='';
$availableActions=array('validate','clean','backup');
if (_ALLOWUNINSTALL) $availableActions[]='delete';

switch ($action) {
    case 'none':
        break;
    case 'delete':
        if (!_ALLOWUNINSTALL) break;
    
        break;
    case 'backup':
        $backup=backupData($prefix);
        break;
    case 'validate':
        $result=checkErrors($prefix);
            $validate="<h2>Validation checks on installation with prefix $prefix</h2>";
        if ($result===false) {
            $validate.="<p class='error'>No database with prefix '$prefix'</p>\n";
            $prefix=$config['prefix'];
        } else {
            $validate.="<p>Estimate of number of errors. NB some errors may overlap,"
                ."so the total that would be fixed may differ from these figures</p>\n"
                ."<table summary='validation checks'><thead>\n";
            foreach($result['totals'] as $key=>$val)
                $validate .="<tr><td>$val</td><th>$key</th></tr>\n";
            $validate .="</thead><tbody>\n";
            foreach($result['errors'] as $key=>$val) {
                $class=($val)?" class='error' ":'';
                $validate .= "<tr><td $class>$val</td><td $class>$key</td></tr>\n";
            }
            $validate .="</tbody></table>\n";
        }
        break;
    case 'clean':
        $pre=checkErrors($prefix);
        fixData($prefix);
        $post=checkErrors($prefix);
        $clean="<h2>Results of cleanup on installation with prefix '$prefix'</h2>\n";
        $clean.="<p>Clean-up completed. Now check <a href='orphans.php'>orphans</a>. \n";
        $clean.=" Also, check for <a href='listItems.php?type=p'>projects</a> that have no actions, or no next actions.</p>\n";
        $clean.="<table summary='result of cleanup'><thead>\n<tr><th>Before</th><th>After</th><th>&nbsp;</th></tr></thead><tbody>";
        foreach($post['totals'] as $key=>$val)
            $clean .="<tr><td>{$pre['totals'][$key]}</td><td>$val</td><th>$key</th></tr>\n";
        foreach($post['errors'] as $key=>$val) {
            $class=($val)?" class='error' ":'';
            $clean .= "<tr><td>{$pre['errors'][$key]}</td><td $class>$val</td><td $class>$key</td></tr>\n";
        }
        $clean .="</tbody></table>\n";
        break;
}
?>
<h1>gtd-php Admin Tasks</h1>
<?php if ($action==='delete') { ?>
    <h2>Delete installation</h2>
<?php }
if (!empty($validate)) echo $validate;

if ($showInstallations || $showCommands) { ?>
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
            <?php foreach ($availableActions as $doit) { ?>
                <label class='notfirst left'><?php echo $doit; ?></label>
                <input type='radio' name='action' value=<?php echo "'$doit'",($doit===$action)?" checked='checked' ":''; ?> />
            <?php } ?>
            <input type='submit' name='submit' value='Go' />
        </div>
    <?php } ?>
    </form>
<?php
}
if (!empty($clean)) echo $clean;
if (!empty($backup)) {
    ?><h2>Backup of installation with prefix '<?php echo $prefix; ?>'</h2>
    <textarea cols="120" rows="10"><?php echo $backup; ?></textarea>
<?php
}
require_once('footer.php');
?>
