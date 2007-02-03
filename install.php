<?php
	include_once('header.php');

	function report($tableName,$success){
		$html="<tr><td>$tableName</td><td>";
		if($success){
			$html .= '<font color="green">Success';
		}else{
			$html .= '<font color="red">Failure';
		}
		$html .= "</td></tr>\n";
		return $html;
	}
    echo "<h2>gtd-php installation/upgrade</h2>\n";

    // get server information for problem reports
    $v="<h3>Installation Info</h3>\n";
    $v.="<ul><li>";
    $v.="php: ".phpversion()."<br>";
    $v.="</li>\n<li>";
    $v.="mysql: ".mysql_get_server_info()."</li></ul>\n";
    echo $v;


    // check if we are using prefixes
    //echo $config['prefix'];


    //check if gtd db exists
    $msg='<font color="red">Unable to select gtd database.<br>Please create the '.$config['db'].' mysql database and rerun this script.';
	mysql_select_db($config['db']) or die ($msg);

    $q="create table ".$config['prefix']."items (";
    $q.="`itemId` int(10) unsigned NOT NULL auto_increment, "; 
    $q.="`title` text NOT NULL, "; 
    $q.="`description` longtext, ";
    $q.="PRIMARY KEY  (`itemId`), ";
    $q.="FULLTEXT KEY `title` (`title`), ";
    $q.="FULLTEXT KEY `description` (`description`));";
    echo $q;
    $result = mysql_query($q);
    echo "Result: ".$result;


	include_once('footer.php');
?>
