<?php
	include_once('header.php');

    //helper functions
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


    // some reporting 
    // get server information for problem reports
    echo "<h2>gtd-php installation/upgrade</h2>\n";
    $v="<h3>Installation Info</h3>\n";
    $v.="<ul><li>";
    $v.="php: ".phpversion()."<br>";
    $v.="</li>\n<li>";
    $v.="mysql: ".mysql_get_server_info()."</li></ul>\n";
    echo $v;



    //check if db exists
    $msg='<font color="red">Unable to select gtd database.<br>Please create the ';
    $msg.=$config['db'].' mysql database and rerun this script.';
	mysql_select_db($config['db']) or die ($msg);


    // check if this is a new install or an upgrade
    // we only handle 1 pont upgrades, so users must be at 0.7 to upgrade to
    // 0.8.

    // check number of tables in db. 17=0.7, 0=new, 15=no upgrade neededi
    $nt=0;
    $tables = mysql_list_tables($config['db']);
    while ($tbl = mysql_fetch_row($tables)){
       $nt++;
    }

    // new tables shared by upgrade and install paths
    function createVersion()  {
       $q="CREATE TABLE ".$config['prefix']."version (";
       $q.="`version` float unsigned NOT NULL, ";
       $q.="`updated` timestamp NOT NULL default CURRENT_TIMESTAMP on update ";
       $q.=" CURRENT_TIMESTAMP);";
       $result = mysql_query($q);
       # do we want to keep version somewhere more central? just updating here in
       # the install script kinda smells funny to me.
       $q="INSERT INTO ".$config['prefix']."version (`version`) VALUES";
       $q.=" ('0.8rc-1');";
       $result = mysql_query($q);
    }

    function createLookup() {
    
       $q="CREATE TABLE ".$config['prefix']."lookup (";
       $q.="`parentId` int(11) NOT NULL default '0', ";
       $q.="`itemId` int(10) unsigned NOT NULL default '0', ";
       $q.="PRIMARY KEY  (`parentId`,`itemId`) );";
       $result = mysql_query($q);
    }

      
    function createPreferences() {
       $q="CREATE TABLE ".$config['prefix']."preferences (";
       $q.="`id`  int(10) unsigned NOT NULL auto_increment, ";
       $q.="`uid` int(10)  NOT NULL default '0', ";
       $q.="`option`  text, ";
       $q.="`value`  text, ";
       $q.="PRIMARY KEY  (`id`)); ";
       $result = mysql_query($q);
    }
 
    

    echo "Number of tables: $nt";
    if($nt==0){
       # new install
       // start creating new tables
       echo "<br>New install";
       $q="create table ".$config['prefix']."categories (";
       $q.="`categoryId` int(10) unsigned NOT NULL auto_increment, "; 
       $q.="`category` text NOT NULL, "; 
       $q.="`description` text, ";
       $q.="PRIMARY KEY  (`categoryId`), ";
       $q.="FULLTEXT KEY `category` (`category`), ";
       $q.="FULLTEXT KEY `description` (`description`));";
       $result = mysql_query($q);

       $q="create table ".$config['prefix']."checklist (";
       $q.="`checklistId` int(10) unsigned NOT NULL auto_increment, "; 
       $q.="`title` text NOT NULL, "; 
       $q.="`categoryId` int(10) unsigned NOT NULL default '0', "; 
       $q.="`description` text, ";
       $q.="PRIMARY KEY  (`checklistId`),    ";
       $q.="FULLTEXT KEY `description` (`description`), ";
       $q.="FULLTEXT KEY `title` (`title`)); ";
       $result = mysql_query($q);

       $q="create table ".$config['prefix']."checklistItems (";
       $q.="`checklistItemId` int(10) unsigned NOT NULL auto_increment, "; 
       $q.="`item` text NOT NULL, "; 
       $q.="`notes` text, "; 
       $q.="`checklistId` int(10) unsigned NOT NULL default '0', "; 
       $q.="`checkedd` enum('y','n') NOT NULL default 'n', "; 
       $q.="PRIMARY KEY  (`checklistItemId`), ";
       $q.="KEY `checklistId` (`checklistId`), ";
       $q.="FULLTEXT KEY `notes` (`notes`), ";
       $q.="FULLTEXT KEY `item` (`item`)); ";
       $result = mysql_query($q);

       $q="create table ".$config['prefix']."context (";
       $q.="`contextId` int(10) unsigned NOT NULL auto_increment, "; 
       $q.="`name` text NOT NULL, "; 
       $q.="`description` text, "; 
       $q.="PRIMARY KEY  (`contextId`), ";
       $q.="FULLTEXT KEY `name` (`name`), ";
       $q.="FULLTEXT KEY `description` (`description`)); ";
       $result = mysql_query($q);

       $q="create table ".$config['prefix']."itemattributes (";
       $q.="`itemId` int(10) unsigned NOT NULL auto_increment, "; 
       $q.="`type` enum ('m','v','o','g','p','a','r','w','i') NOT NULL default 'i', ";
       $q.="`isSomeday` enum('y','n') NOT NULL default 'n', ";
       $q.="`categoryId` int(11) unsigned NOT NULL default '0', ";
       $q.="`contextId` int(10) unsigned NOT NULL default '0', ";
       $q.="`timeframeId` int(10) unsigned NOT NULL default '0', ";
       $q.="`deadline` date default NULL, ";
       $q.="`repeat` int(10) unsigned NOT NULL default '0', ";
       $q.="`suppress` enum('y','n') NOT NULL default 'n', ";
       $q.="`suppressUntil` int(10) unsigned default NULL, ";
       $q.="PRIMARY KEY (`itemId`), ";
       $q.="KEY `contextId` (`contextId`), ";
       $q.="KEY `suppress` (`suppress`), ";
       $q.="KEY `type` (`type`), ";
       $q.="KEY `timeframeId` (`timeframeId`), ";
       $q.="KEY `isSomeday` (`isSomeday`),    ";  
       $q.="KEY `categoryId` (`categoryId`),  ";
       $q.="KEY `isSomeday_2` (`isSomeday`));";
       $result = mysql_query($q);

       $q="create table ".$config['prefix']."items (";
       $q.="`itemId` int(10) unsigned NOT NULL auto_increment, "; 
       $q.="`title` text NOT NULL, "; 
       $q.="`description` longtext, ";
       $q.="`desiredOutcome` text, ";
       $q.="PRIMARY KEY  (`itemId`), ";
       $q.="FULLTEXT KEY `title` (`title`), ";
       $q.="FULLTEXT KEY `desiredOutcome` (`desiredOutcome`), ";
       $q.="FULLTEXT KEY `description` (`description`));";
       $result = mysql_query($q);


       $q="create table ".$config['prefix']."itemstatus (";
       $q.="`itemId` int(10) unsigned NOT NULL auto_increment, ";
       $q.="`dateCreated` date  default NULL, ";
       $q.="`lastModified` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP, ";
       $q.="`dateCompleted` date default NULL, ";
       $q.="PRIMARY KEY  (`itemId`));";
       $result = mysql_query($q);

       $q="CREATE TABLE ".$config['prefix']."list (";
       $q.="`listId` int(10) unsigned NOT NULL auto_increment, ";
       $q.="`title` text NOT NULL, ";
       $q.="`categoryId` int(10) unsigned NOT NULL default '0', ";
       $q.="`description` text, ";
       $q.="PRIMARY KEY  (`listId`), ";
       $q.="KEY `categoryId` (`categoryId`), ";
       $q.="FULLTEXT KEY `description` (`description`), ";
       $q.="FULLTEXT KEY `title` (`title`));";
       $result = mysql_query($q);

       $q="CREATE TABLE ".$config['prefix']."listItems (";
       $q.="`listItemId` int(10) unsigned NOT NULL auto_increment, ";
       $q.="`item` text NOT NULL, ";
       $q.="`notes` text, ";
       $q.="`listId` int(10) unsigned NOT NULL default '0', ";
       $q.="`dateCompleted` date default NULL, ";
       $q.="PRIMARY KEY  (`listItemId`), ";
       $q.="KEY `listId` (`listId`), ";
       $q.="FULLTEXT KEY `notes` (`notes`), ";
       $q.="FULLTEXT KEY `item` (`item`));"; 
       $result = mysql_query($q);

       createLookup();
       createPreferences();

       $q="CREATE TABLE ".$config['prefix']."nextactions (";
       $q.="`parentId` int(10) unsigned NOT NULL default '0', ";
       $q.="`nextaction` int(10) unsigned NOT NULL default '0', ";
       $q.="PRIMARY KEY  (`parentId`,`nextaction`));";
       $result = mysql_query($q);

       $q="CREATE TABLE ".$config['prefix']."tickler (";
       $q.="`ticklerId` int(10) unsigned NOT NULL auto_increment, ";
       $q.="`date` date NOT NULL default '0000-00-00', ";
       $q.="`title` text NOT NULL, ";
       $q.="`note` longtext, ";
       $q.="`repeat` int(10) unsigned NOT NULL default '0', ";
       $q.="`suppressUntil` int(10) unsigned NOT NULL default '0', ";
       $q.="PRIMARY KEY  (`ticklerId`), ";
       $q.="KEY `date` (`date`), ";
       $q.="FULLTEXT KEY `notes` (`note`), ";
       $q.="FULLTEXT KEY `title` (`title`));";
       $result = mysql_query($q);

       $q="CREATE TABLE ".$config['prefix']."timeitems (";
       $q.="`timeframeId` int(10) unsigned NOT NULL auto_increment, ";
       $q.="`timeframe` text NOT NULL, ";
       $q.="`description` text, ";
       $q.="`type` enum('v','o','g','p','a') NOT NULL default 'a', ";
       $q.="PRIMARY KEY  (`timeframeId`), ";
       $q.="KEY `type` (`type`), ";
       $q.="FULLTEXT KEY `timeframe` (`timeframe`), ";
       $q.="FULLTEXT KEY `description` (`description`));"; 
       $result = mysql_query($q);

       createVersion();

       // give some direction about what happens next for the user.
       
       ?>
       
       <h2>Welcome to GTD-PHP</h2>
       
       <p>You have just successfully installed GTD-PHP.
       There are some preliminary steps you should take to set up your
       installation for use and familiarize yourself with the system.</p>
       <p>
		   <ol>
              <li>You need to set up <a href="newContext.php" target="_blank">spatial</a> and
              <a href="newTimeContext.php" target="_blank">time contexts</a> that suit your situation.</li>
			   <li>You need to enter ....</li>
			   <li></li>
			   <li></li>
		   </ol>
       </p>
       <?php

       // end new install
    }else if($nt==17){
       //upgrading from 0.7
       echo "<br>Upgrading from 0.7";
       // update
       // keep a backup of the db?
       // move each of the old tables into the appropriate new tables

       // if they were using 0.7 they were not using prefixes. do we need them
       // here?

       $q="create table ".$config['prefix']."t_categories (";
       $q.="`categoryId` int(10) unsigned NOT NULL auto_increment, "; 
       $q.="`category` text NOT NULL, "; 
       $q.="`description` text, ";
       $q.="PRIMARY KEY  (`categoryId`), ";
       $q.="FULLTEXT KEY `category` (`category`), ";
       $q.="FULLTEXT KEY `description` (`description`));";
       $result = mysql_query($q);

       $q="INSERT INTO ".$config['prefix']."t_categories select * from `categories`";
       $result = mysql_query($q);

       // drop categories
       $q="drop table `categories`";
       $result = mysql_query($q);

       // rename t_categories to categories
       $q="rename table ".$config['prefix']."t_categories to `categories`";
       $result = mysql_query($q);

       // checklist
       $q="create table ".$config['prefix']."t_checklist (";
       $q.="`checklistId` int(10) unsigned NOT NULL auto_increment, "; 
       $q.="`title` text NOT NULL, "; 
       $q.="`categoryId` int( 10 ) unsigned NOT NULL default '0', "; 
       $q.="`description` text, ";
       $q.="PRIMARY KEY  (`checklistId`)) ";
       $result = mysql_query($q);

       $q="INSERT INTO ".$config['prefix']."t_checklist  SELECT * FROM `checklist`";
       $result = mysql_query($q);

       // rename t_checklist to checklist
       $q="drop table `checklist`";
       $result = mysql_query($q);
       $q="rename table ".$config['prefix']."t_checklist to `checklist`";
       $result = mysql_query($q);

       // checklistItems
       $q="create table ".$config['prefix']."t_checklistItems (";
       $q.="`checklistItemId` int(10) unsigned NOT NULL auto_increment, "; 
       $q.="`item` text NOT NULL, "; 
       $q.="`notes` text, "; 
       $q.="`checklistId` int(10) unsigned NOT NULL default '0', "; 
       $q.="`checked` enum ('y', 'n') NOT NULL default 'n', "; 
       $q.="PRIMARY KEY (`checklistItemId`), KEY `checklistId` (`checklistId`),"; 
       $q.="FULLTEXT KEY `notes` (`notes`), FULLTEXT KEY `item` (`item`))"; 

       $result = mysql_query($q);
       if (!$result) {
             echo $q;
             die('Invalid query: ' . mysql_error());
          }
       $q="INSERT INTO ".$config['prefix']."t_checklistItems  SELECT * FROM `checklistItems`";
       $result = mysql_query($q);
       if (!$result) {
             echo $q;
             die('Invalid query: ' . mysql_error());
          }
       // rename t_checklist to checklist
       $q="drop table `checklistItems`";
       $result = mysql_query($q);
       $q="rename table ".$config['prefix']."t_checklistItems to `checklistItems`";
       $result = mysql_query($q);
       if (!$result) {
             echo $q;
             die('Invalid query: ' . mysql_error());
          }
       createVersion();

    }else if($nt==15){
       //has a 0.8 db
       echo "<br>No upgrade needed";
    }else{
       echo "<br>You must be at version 0.7 to upgrade to 0.8.";
    }
	include_once('footer.php');
?>
