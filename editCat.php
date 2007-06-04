<?php
require_once('header.php');
require_once('editCat.inc.php');
?>
<h2><?php echo $title; ?></h2>
<form action="updateCat.php" method="post">
<div>
<input type='hidden' name='field' value='<?php echo $field; ?>' />
<?php if ($thiscat['id']!==false) { ?>
    <input type='hidden' name='id' value=<?php echo "'{$thiscat['id']}'"; ?> />
<?php } ?>
</div>
<table class='datatable sortable' id='list' summary='<?php echo $field; ?> table'>
    <thead>
        <tr>
            <th>Name</th>
            <th>Description</th>
            <?php if ($showTypes) { ?>
                <th><label>Type:</label></th>
            <?php
            }
            if ($canDelete) { ?>
                <th>Delete?</th>
            <?php } ?>
        </tr>
    <?php
    if ($thiscat['id']!==false) { ?>
        <tr>
            <td><input type="text" name="name" value="<?php echo $thiscat['name']; ?>" /></td>
            <td><textarea rows="2" cols="50" name="description"><?php echo $thiscat['description']; ?></textarea></td>
            <?php if ($showTypes) { ?><td>
                <input type='radio' name="type" id="goal"    value="g" <?php if ($thiscat['type']==='g') echo "checked='checked'"; ?> class="first" />
                <label for="goal" class="right">Goal</label><br />
                <input type='radio' name="type" id="project" value="p" <?php if ($thiscat['type']==='p') echo "checked='checked'"; ?>class="notfirst" />
                <label for="project" class="right">Project</label><br />
                <input type='radio' name="type" id="action"  value="a" <?php if (!$thiscat['id'] || $thiscat['type']==='a') echo "checked='checked'"; ?>class="notfirst" />
                <label for="action" class="right">Action</label>
            </td><?php } ?>
            <?php if ($canDelete) echo "<td><input type='checkbox' name='delete' value='y' /></td>\n"; ?>
        </tr>
        <tr>
            <td><input type="submit" class="button" value="Update" name="submit" /></td>
            <td><input type="reset" class="button" value="Reset" /></td>
            <?php if ($showTypes) { ?><td>&nbsp;</td><?php } ?>
            <?php if ($canDelete) { ?><td>and replace with</td><?php } ?>
        </tr>
    <?php } ?>
    </thead>
    <tbody>
    <?php foreach ($catlist as $row) { ?>
        <tr>
            <td><?php
                echo "<input type='image' alt='Edit' name='submit{$row['id']}' value='{$row['id']}'"
                    ," src='themes/{$config['theme']}/edit.gif' title='{$prefix}edit {$row['name']} {$field}' />"
                    ,$row['name'];
            ?></td>
            <td><?php echo $row['description']; ?></td>
            <?php if ($showTypes) { ?><td><?php echo getTypes($row['type']); ?>&nbsp;</td><?php } ?>
            <?php if ($canDelete) { ?>
                <td><?php if ($row['type']===$thiscat['type']) { ?>
                    <input type='radio' name='replacewith' value='<?php echo $row['id']; ?>'  />
                    <?php } else echo '&nbsp;' ?>
                </td>
            <?php } ?>
        </tr>
    <?php } ?>
    </tbody>
    <tbody>
        <?php if ($canDelete) { ?>
            <tr>
                <td>None</td>
                <td>&nbsp;</td>
                <?php if ($showTypes) { ?><td>&nbsp;</td><?php } ?>
                <td><input type="radio" name="replacewith" value="0" checked="checked" /></td>
            </tr>
        <?php } ?>
        <tr>
            <td><input type='image' name='submit0' value='0' alt='Create' src='themes/<?php echo $config['theme']; ?>/edit.gif' title='<?php echo $prefix; ?> create a new item' />
                Create new <?php echo $field; ?></td>
            <td>&nbsp;</td>
            <?php if ($showTypes) { ?><td>&nbsp;</td><?php } ?>
            <?php if ($canDelete) echo '<td>&nbsp;</td>'; ?>
        </tr>
    </tbody>
</table>
</form>
<?php include_once('footer.php'); ?>
