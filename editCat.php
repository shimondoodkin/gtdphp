<?php
require_once('header.php');
require_once('editCat.inc.php');
?>
<h2>Edit <?php echo $field; ?></h2>
<form action="updateCat.php?<?php echo $field,'=',$thiscat['id']; ?>" method="post">
<table>
    <thead>
        <tr><th>Name</th><th>Description</th><th>Delete?</th></tr>
        <tr>
            <td><input type="text" name="name" value="<?php echo $thiscat['name']; ?>" /></td>
            <td>
                <textarea rows="2" cols="50" name="description"><?php echo $thiscat['description']; ?></textarea>
            </td>
            <td><input type="checkbox" name="delete" value="y" /></td>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>
                <input type="hidden" name="id" value="<?php echo $thiscat['id'] ?>" />
                <input type="hidden" name="field" value="<?php echo $field ?>" />
                <input type="submit" class="button" value="Update" name="submit" />
            </td>
            <td><input type="reset" class="button" value="Reset" /></td>
            <td>and replace with</td>
        <tr>
            <td>None</td>
            <td>&nbsp;</td>
            <td><input type="radio" name="replacewith" value="0" checked="checked" /></td>
        </tr>
        <?php foreach ($catlist as $row) { ?>
            <tr>
                <td>
                    <input type="image" alt="Edit" name="submit"
                        value="<?php echo $row['id']; ?>"
                        src="themes/<?php echo $config['theme'] ?>/edit.gif"
                    />
                    <?php echo $row['name']; ?>
                </td>
                <td><?php echo $row['description']; ?></td>
                <td>
                    <input type="radio" name="replacewith" value="<?php echo $row['id']; ?>" />
                </td>
            <tr>
        <?php } ?>
    </tbody>
</table>
</form>
<?php include_once('footer.php'); ?>
