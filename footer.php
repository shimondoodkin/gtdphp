<?php include('showMessage.inc.php'); ?>
</div><!-- main -->
<?php if(isset($starttime)) {
    list($usec, $sec) = explode(" ", microtime());
    $tottime=(int) (((float)$usec + (float)$sec - $starttime)*1000);
} ?>
<div id='footer'>
    page generated in <?php echo $tottime; ?>ms
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	gtd-php version 0.8-rc4-rev393
</div>
</div> <!-- Container-->
</body>
</html>
