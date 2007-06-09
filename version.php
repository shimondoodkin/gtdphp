<?php include_once('header.php'); ?>

<h2>GTD-PHP Version Information</h2>
<p>PHP  version <?php echo PHP_VERSION; ?></p>
<p>MySQL version <?php echo mysql_get_server_info(); ?></p>
<p>GTD-PHP database version: <?php echo array_pop(array_pop(query('getgtdphpversion',$config))); ?></p>

<?php include_once('footer.php'); ?>
