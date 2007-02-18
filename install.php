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
       // rename t_checklistItems to checklistItems
       $q="drop table `checklistItems`";
       $result = mysql_query($q);
       $q="rename table ".$config['prefix']."t_checklistItems to `checklistItems`";
       $result = mysql_query($q);
       if (!$result) {
             echo $q;
             die('Invalid query: ' . mysql_error());
          }


       // context
       $q="create table ".$config['prefix']."t_context (";
       $q.="`contextId` int(10) unsigned NOT NULL auto_increment, "; 
       $q.="`name` text NOT NULL, "; 
       $q.="`description` text, "; 
       $q.="PRIMARY KEY (`contextId`))"; 

       $result = mysql_query($q);
       if (!$result) {
             echo $q;
             die('Invalid query: ' . mysql_error());
          }
       $q="INSERT INTO ".$config['prefix']."t_context  SELECT * FROM `context`";
       $result = mysql_query($q);
       if (!$result) {
             echo $q;
             die('Invalid query: ' . mysql_error());
          }
       // rename t_context to context
       $q="drop table `context`";
       $result = mysql_query($q);
       $q="rename table ".$config['prefix']."t_context to `context`";
       $result = mysql_query($q);
       if (!$result) {
             echo $q;
             die('Invalid query: ' . mysql_error());
          }


       // goals
       $q="create table ".$config['prefix']."t_goals (";
       $q.="`id` int(11) NOT NULL auto_increment, "; 
       $q.="`goal`   longtext, ";
       $q.="`description`   longtext, ";
       $q.="`created` date default NULL, ";
       $q.="`deadline` date default NULL, ";
       $q.="`completed` date default NULL, ";
       $q.="`type` enum('weekly', 'quarterly') default NULL ,";
       $q.="`projectId` int(11) default NULL, PRIMARY KEY (`id`) )";

       $result = mysql_query($q);
       if (!$result) {
             echo $q;
             die('Invalid query: ' . mysql_error());
          }
       $q="INSERT INTO ".$config['prefix']."t_goals  SELECT * FROM `goals`";
       $result = mysql_query($q);
       if (!$result) {
             echo $q;
             die('Invalid query: ' . mysql_error());
          }
       // rename t_goals to goals
       $q="drop table `goals`";
       $result = mysql_query($q);
       $q="rename table ".$config['prefix']."t_goals to `goals`";
       $result = mysql_query($q);
       if (!$result) {
             echo $q;
             die('Invalid query: ' . mysql_error());
          }

       // itemattributes
       $q="create table ".$config['prefix']."t_itemattributes (";
       $q.="`itemId` int(10) NOT NULL auto_increment, "; 
       $q.="`type` enum('a', 'r', 'w') NOT NULL default 'a' ,";
       $q.="`projectId` int(10) unsigned NOT NULL default '0', "; 
       $q.="`contextId` int(10) unsigned NOT NULL default '0', "; 
       $q.="`timeframeId` int(10) unsigned NOT NULL default '0', "; 
       $q.="`deadline` date default NULL , ";
       $q.="`repeat` int( 10 ) unsigned NOT NULL default '0', ";
       $q.=" `suppress` enum( 'y', 'n' ) NOT NULL default 'n', ";
       $q.="`suppressUntil` int( 10 ) unsigned default NULL , ";
       $q.="PRIMARY KEY ( `itemId` ) , KEY `projectId` ( `projectId` ) ,";
       $q.="KEY `contextId` ( `contextId` ) , ";
       $q.="KEY `suppress` ( `suppress` ) , KEY `type` ( `type` ) ,";
       $q.=" KEY `timeframeId` ( `timeframeId` ) )";

       $result = mysql_query($q);
       if (!$result) {
             echo $q;
             die('Invalid query: ' . mysql_error());
          }
       $q="INSERT INTO ".$config['prefix']."t_itemattributes  SELECT * FROM `itemattributes`";
       $result = mysql_query($q);
       if (!$result) {
             echo $q;
             die('Invalid query: ' . mysql_error());
          }
       // rename t_itemattributes to itemattributes
       $q="drop table `itemattributes`";
       $result = mysql_query($q);
       $q="rename table ".$config['prefix']."t_itemattributes to `itemattributes`";
       $result = mysql_query($q);
       if (!$result) {
             echo $q;
             die('Invalid query: ' . mysql_error());
          }
       // items
       $q="CREATE TABLE ".$config['prefix']."t_items ( ";
       $q.=" `itemId` int( 10 ) unsigned NOT NULL auto_increment , `title`
       text NOT NULL , `description` longtext, PRIMARY KEY ( `itemId` ) ,
       FULLTEXT KEY `title` ( `title` ) , FULLTEXT KEY `description` (
          `description` ) )" ;
       $result = mysql_query($q);
       if (!$result) {
             echo $q;
             die('Invalid query: ' . mysql_error());
          }
       $q="INSERT INTO ".$config['prefix']."t_items SELECT * from `items` ";
       $result = mysql_query($q);
       if (!$result) {
             echo $q;
             die('Invalid query: ' . mysql_error());
          }

       $q="drop table `items`";
       $result = mysql_query($q);
       if (!$result) {
             echo $q;
             die('Invalid query: ' . mysql_error());
          }
       $q="rename table ".$config['prefix']."t_items to `items`";
       $result = mysql_query($q);
       if (!$result) {
             echo $q;
             die('Invalid query: ' . mysql_error());
       }


       $q="CREATE TABLE ".$config['prefix']."t_itemstatus ( ";
       $q.="`itemId` int( 10 ) unsigned NOT NULL auto_increment ,";
       $q.=" `dateCreated` date NOT NULL default '0000-00-00', ";
       $q.="`lastModified` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP , ";
       $q.="`dateCompleted` date default NULL , ";
       $q.=" `completed` int( 10 ) unsigned default NULL , ";
       $q.="PRIMARY KEY ( `itemId` ) ) ";
       $result = mysql_query($q);
       if (!$result) {
             echo $q;
             die('Invalid query: ' . mysql_error());
       }
       $q="INSERT INTO ".$config['prefix']."t_itemstatus SELECT * FROM `gtd`.`itemstatus`";
       $result = mysql_query($q);
       if (!$result) {
             echo $q;
             die('Invalid query: ' . mysql_error());
       }
       $q="drop table `itemstatus`";
       $result = mysql_query($q);
       if (!$result) {
             echo $q;
             die('Invalid query: ' . mysql_error());
          }
       $q="rename table ".$config['prefix']."t_itemstatus to `itemstatus`";
       $result = mysql_query($q);
       if (!$result) {
             echo $q;
             die('Invalid query: ' . mysql_error());
       }
       
       $q="CREATE TABLE ".$config['prefix']."t_list ( ";
       $q.="`listId` int( 10 ) unsigned NOT NULL auto_increment ,";
       $q.=" `title` text NOT NULL , `categoryId` int( 10 ) unsigned NOT NULL
       default '0', `description` text, PRIMARY KEY ( `listId` ) , KEY
       `categoryId` ( `categoryId` ) , FULLTEXT KEY `description` (
          `description` ) , FULLTEXT KEY `title` ( `title` ) )";
       $result = mysql_query($q);
       if (!$result) {
             echo $q;
             die('Invalid query: ' . mysql_error());
       }
       $q="INSERT INTO ".$config['prefix']."t_list  SELECT * FROM `list` ";
       $result = mysql_query($q);
       if (!$result) {
             echo $q;
             die('Invalid query: ' . mysql_error());
       }
       $q="drop table `list`";
       $result = mysql_query($q);
       if (!$result) {
             echo $q;
             die('Invalid query: ' . mysql_error());
       }
       $q="rename table ".$config['prefix']."t_list to `list`";
       $result = mysql_query($q);
       if (!$result) {
             echo $q;
             die('Invalid query: ' . mysql_error());
          }
       $q="CREATE TABLE ".$config['prefix']."t_listItems ( ";
       $q.="`listItemId` int( 10 ) unsigned NOT NULL auto_increment , ";
       $q.="`item` text NOT NULL , `notes` text, ";
       $q.="`listId` int( 10 ) unsigned NOT NULL default '0', ";
       $q.="`dateCompleted` date default '0000-00-00', PRIMARY KEY (
          `listItemId` ) , ";
       $q.="KEY `listId` ( `listId` ) , FULLTEXT KEY `notes` ( `notes` ) , ";
       $q.="FULLTEXT KEY `item` ( `item` ) ) ";
       $result = mysql_query($q);
       if (!$result) {
             echo $q;
             die('Invalid query: ' . mysql_error());
       }
       $q="INSERT INTO ".$config['prefix']."t_listItems SELECT * FROM `listItems`";
       $result = mysql_query($q);
       if (!$result) {
             echo $q;
             die('Invalid query: ' . mysql_error());
       }
       $q="drop table `listItems`";
       $result = mysql_query($q);
       if (!$result) {
             echo $q;
             die('Invalid query: ' . mysql_error());
       }
       $q="rename table ".$config['prefix']."t_listItems to `listItems`";
       $result = mysql_query($q);
       if (!$result) {
             echo $q;
             die('Invalid query: ' . mysql_error());
       }
       $q="CREATE TABLE ".$config['prefix']."t_nextactions ( ";
       $q.="`projectId` int( 10 ) unsigned NOT NULL default '0', ";
       $q.=" `nextaction` int( 10 ) unsigned NOT NULL default '0', ";
       $q.=" PRIMARY KEY ( `projectId` , `nextaction` ) ) ";
       $result = mysql_query($q);
       if (!$result) {
             echo $q;
             die('Invalid query: ' . mysql_error());
       }
       $q="INSERT INTO ".$config['prefix']."t_nextactions SELECT * FROM `nextactions`";
       $result = mysql_query($q);
       if (!$result) {
             echo $q;
             die('Invalid query: ' . mysql_error());
       }
       $q="drop table `nextactions`";
       $result = mysql_query($q);
       if (!$result) {
             echo $q;
             die('Invalid query: ' . mysql_error());
       }
       $q="rename table ".$config['prefix']."t_nextactions to `nextactions`";
       $result = mysql_query($q);
       if (!$result) {
             echo $q;
             die('Invalid query: ' . mysql_error());
       }


       $q="CREATE TABLE ".$config['prefix']."t_projectattributes ( ";
       $q.="`projectId` int( 10 ) unsigned NOT NULL auto_increment , ";
       $q.=" `categoryId` int( 10 ) unsigned NOT NULL default '1', ";
       $q.="`isSomeday` enum( 'y', 'n' ) NOT NULL default 'n', ";
       $q.=" `deadline` date default NULL , `repeat` int( 11 ) unsigned NOT
       NULL default '0', ";
       $q.="`suppress` enum( 'y', 'n' ) NOT NULL default 'n', ";
       $q.=" `suppressUntil` int( 10 ) unsigned default NULL , PRIMARY KEY (
          `projectId` ) ,";
       $q.=" KEY `categoryId` ( `categoryId` ) , KEY `isSomeday` (
          `isSomeday`) ,";
          $q.="KEY `suppress` ( `suppress` ) ) ";
       $result = mysql_query($q);
       if (!$result) {
             echo $q;
             die('Invalid query: ' . mysql_error());
          }
       $q="INSERT INTO ".$config['prefix']."t_projectattributes SELECT * FROM `projectattributes` ";
       $result = mysql_query($q);
       if (!$result) {
             echo $q;
             die('Invalid query: ' . mysql_error());
          }
       $q="drop table `projectattributes`";
       $result = mysql_query($q);
       if (!$result) {
             echo $q;
             die('Invalid query: ' . mysql_error());
       }
       $q="rename table ".$config['prefix']."t_projectattributes to `projectattributes`";
       $result = mysql_query($q);
       if (!$result) {
             echo $q;
             die('Invalid query: ' . mysql_error());
       }
       $q="CREATE TABLE ".$config['prefix']."t_projects ( ";
       $q.="`projectId` int( 10 ) unsigned NOT NULL auto_increment , ";
       $q.=" `name` text NOT NULL , `description` text, `desiredOutcome` text, ";
       $q.="PRIMARY KEY ( `projectId` ) , FULLTEXT KEY `desiredOutcome` (
          `desiredOutcome` ) , ";
       $q.=" FULLTEXT KEY `name` ( `name` ) , FULLTEXT KEY `description` (
          `description` ) ) ";
       $result = mysql_query($q);
       if (!$result) {
             echo $q;
             die('Invalid query: ' . mysql_error());
          }
       $q="INSERT INTO ".$config['prefix']."t_projects SELECT * FROM `projects` ";
       $result = mysql_query($q);
       if (!$result) {
             echo $q;
             die('Invalid query: ' . mysql_error());
          }
       $q="drop table `projects`";
       $result = mysql_query($q);
       if (!$result) {
             echo $q;
             die('Invalid query: ' . mysql_error());
       }
       $q="rename table ".$config['prefix']."t_projects to `projects`";
       $result = mysql_query($q);
       if (!$result) {
             echo $q;
             die('Invalid query: ' . mysql_error());
       }
       $q="CREATE TABLE ".$config['prefix']."t_projectstatus ( ";
       $q.="`projectId` int( 10 ) unsigned NOT NULL auto_increment ,
       `dateCreated` date NOT NULL default '0000-00-00', `lastModified`
       timestamp NOT NULL default CURRENT_TIMESTAMP on update
       CURRENT_TIMESTAMP , `dateCompleted` date default NULL , PRIMARY KEY (
          `projectId` ) ) ";
       $result = mysql_query($q);
       if (!$result) {
             echo $q;
             die('Invalid query: ' . mysql_error());
       }
       $q="INSERT INTO ".$config['prefix']."t_projectstatus SELECT * FROM
       `projectstatus`";
       $result = mysql_query($q);
       if (!$result) {
             echo $q;
             die('Invalid query: ' . mysql_error());
       }
       $q="drop table `projectstatus`";
       $result = mysql_query($q);
       if (!$result) {
             echo $q;
             die('Invalid query: ' . mysql_error());
       }
       $q="rename table ".$config['prefix']."t_projectstatus to `projectstatus`";
       $result = mysql_query($q);
       if (!$result) {
             echo $q;
             die('Invalid query: ' . mysql_error());
       }

       $q="CREATE TABLE ".$config['prefix']."t_tickler ( ";
       $q.="`ticklerId` int( 10 ) unsigned NOT NULL auto_increment , ";
       $q.="`date` date NOT NULL default '0000-00-00', `title` text NOT NULL ,
       `note` longtext, PRIMARY KEY ( `ticklerId` ) , KEY `date` ( `date` ) ,
       FULLTEXT KEY `notes` ( `note` ) ) ";
       $result = mysql_query($q);
       if (!$result) {
             echo $q;
             die('Invalid query: ' . mysql_error());
       }
       $q="INSERT INTO ".$config['prefix']."t_tickler  SELECT * FROM
       `tickler`";
       $result = mysql_query($q);
       if (!$result) {
             echo $q;
             die('Invalid query: ' . mysql_error());
       }
       $q="drop table `tickler`";
       $result = mysql_query($q);
       if (!$result) {
             echo $q;
             die('Invalid query: ' . mysql_error());
       }
       $q="rename table ".$config['prefix']."t_tickler to `tickler`";
       $result = mysql_query($q);
       if (!$result) {
             echo $q;
             die('Invalid query: ' . mysql_error());
       }

       $q="CREATE TABLE  ".$config['prefix']."t_timeitems ( ";
       $q.="`timeframeId` int( 10 ) unsigned NOT NULL auto_increment , ";
       $q.=" `timeframe` text NOT NULL , `description` text, PRIMARY KEY (
          `timeframeId` ) )";
       $result = mysql_query($q);
       if (!$result) {
             echo $q;
             die('Invalid query: ' . mysql_error());
       }

       $q="INSERT INTO ".$config['prefix']."t_timeitems SELECT * FROM
       `timeitems`";
       $result = mysql_query($q);
       if (!$result) {
             echo $q;
             die('Invalid query: ' . mysql_error());
       }
       $q="drop table `timeitems`";
       $result = mysql_query($q);
       if (!$result) {
             echo $q;
             die('Invalid query: ' . mysql_error());
       }
       $q="rename table ".$config['prefix']."t_timeitems to `timeitems`";
       $result = mysql_query($q);
       if (!$result) {
             echo $q;
             die('Invalid query: ' . mysql_error());
       }


       $q="ALTER TABLE ".$config['prefix']."tickler ADD `repeat` INT UNSIGNED
       NOT NULL DEFAULT '0'";
       $result = mysql_query($q);
       if (!$result) {
             echo $q;
             die('Invalid query: ' . mysql_error());
       }
       $q="ALTER TABLE ".$config['prefix']."tickler ADD `suppressUntil` INT
       UNSIGNED NOT NULL DEFAULT '0'";
       $result = mysql_query($q);
       if (!$result) {
             echo $q;
             die('Invalid query: ' . mysql_error());
       }
       createLookup();
       $q="INSERT INTO ".$config['prefix']."lookup (`parentId`,`itemId`) SELECT `projectId`,`itemId`
       FROM `itemattributes`";
       $result = mysql_query($q);
       if (!$result) {
             echo $q;
             die('Invalid query: ' . mysql_error());
       }
       $q="ALTER TABLE ".$config['prefix']."itemattributes DROP `projectId`";
       $result = mysql_query($q);
       if (!$result) {
             echo $q;
             die('Invalid query: ' . mysql_error());
       }
       $q="ALTER TABLE ".$config['prefix']."itemattributes ADD `isSomeday`
       ENUM( 'y', 'n' ) NOT NULL DEFAULT 'n' AFTER `type`, ADD `categoryId`
       INT( 11 ) UNSIGNED NOT NULL DEFAULT '0' AFTER `isSomeday` ";
       $result = mysql_query($q);
       if (!$result) {
             echo $q;
             die('Invalid query: ' . mysql_error());
       }
       $q="ALTER TABLE ".$config['prefix']."itemattributes ADD INDEX (
          `isSomeday` )";
       $result = mysql_query($q);
       if (!$result) {
             echo $q;
             die('Invalid query: ' . mysql_error());
       }
       $q="ALTER TABLE ".$config['prefix']."itemattributes ADD INDEX (
          `categoryId`)";
       $result = mysql_query($q);
       if (!$result) {
             echo $q;
             die('Invalid query: ' . mysql_error());
       }

       $q="ALTER TABLE ".$config['prefix']."items ADD `desiredOutcome` TEXT
       NULL";
       $result = mysql_query($q);
       if (!$result) {
             echo $q;
             die('Invalid query: ' . mysql_error());
       }
       $q="ALTER TABLE ".$config['prefix']."items ADD FULLTEXT
       (`desiredOutcome`)";
       $result = mysql_query($q);
       if (!$result) {
             echo $q;
             die('Invalid query: ' . mysql_error());
       }
       $q="ALTER TABLE ".$config['prefix']."itemstatus DROP `completed`";
       $result = mysql_query($q);
       if (!$result) {
             echo $q;
             die('Invalid query: ' . mysql_error());
       }
       $q="ALTER TABLE ".$config['prefix']."itemattributes CHANGE `type`
       `type` ENUM( 'm', 'v', 'o', 'g', 'p', 'a', 'r', 'w', 'i' ) NOT NULL
       DEFAULT 'i'";
       $result = mysql_query($q);
       if (!$result) {
             echo $q;
             die('Invalid query: ' . mysql_error());
       }

       $q="ALTER TABLE ".$config['prefix']."projectattributes ADD `type` ENUM(
          'p' ) NOT NULL DEFAULT 'p' AFTER `projectId`";
       $result = mysql_query($q);
       if (!$result) {
             echo $q;
             die('Invalid query: ' . mysql_error());
       }
       $q="ALTER TABLE ".$config['prefix']."nextactions DROP PRIMARY KEY, ADD
       PRIMARY KEY ( `projectId` , `nextaction`)";
       $result = mysql_query($q);
       if (!$result) {
             echo $q;
             die('Invalid query: ' . mysql_error());
       }

       $q="ALTER TABLE ".$config['prefix']."nextactions CHANGE `projectId`
       `parentId` INT( 10 ) UNSIGNED NOT NULL DEFAULT'0'";
       $result = mysql_query($q);
       if (!$result) {
             echo $q;
             die('Invalid query: ' . mysql_error());
       }

       $q="ALTER TABLE ".$config['prefix']."items ADD `prikey` INT UNSIGNED
       NOT NULL FIRST";
       $result = mysql_query($q);
       if (!$result) {
             echo $q;
             die('Invalid query: ' . mysql_error());
       }
       $q="ALTER TABLE ".$config['prefix']."itemattributes ADD `prikey` INT
       UNSIGNED NOT NULL FIRST";
       $result = mysql_query($q);
       if (!$result) {
             echo $q;
             die('Invalid query: ' . mysql_error());
       }

       $q="ALTER TABLE ".$config['prefix']."itemstatus ADD `prikey` INT
       UNSIGNED NOT NULL FIRST";
       $result = mysql_query($q);
       if (!$result) {
             echo $q;
             die('Invalid query: ' . mysql_error());
       }
       $q="ALTER TABLE ".$config['prefix']."items CHANGE `itemId` `itemId`
       INT( 10 ) UNSIGNED NOT NULL";
       $result = mysql_query($q);
       if (!result) {
          echo $q;
          die('Invalid query: ' . mysql_error());
       }
       
       $q="ALTER TABLE ".$config['prefix']."itemattributes CHANGE `itemId`
       `itemId` INT( 10 ) UNSIGNED NOT NULL";
       $result = mysql_query($q);
       if (!result) {
          echo $q;
          die('Invalid query: ' . mysql_error());
       }

       $q="ALTER TABLE ".$config['prefix']."itemstatus CHANGE `itemId`
       `itemId` INT( 10 ) UNSIGNED NOT NULL";
       $result = mysql_query($q);
       if (!result) {
          echo $q;
          die('Invalid query: ' . mysql_error());
       }



       $q=" UPDATE ".$config['prefix']."items SET `prikey`=`itemId` +( 
	CASE  WHEN (SELECT MAX(`id`) FROM `goals`) IS NULL THEN 0
		ELSE (SELECT MAX(`id`) FROM `goals`)
	END
	)+(
	CASE  WHEN (SELECT MAX(`projectId`) FROM `projects`) IS NULL THEN 0
		ELSE (SELECT MAX(`projectId`) FROM `projects`)
	END
	)";
       $result = mysql_query($q);
       if (!result) {
          echo $q;
          die('Invalid query: ' . mysql_error());
       }


    $q="UPDATE ".$config['prefix']."itemattributes SET `prikey`=`itemId`+(
	CASE  WHEN (SELECT MAX(`id`) FROM `goals`) IS NULL THEN 0
		ELSE (SELECT MAX(`id`) FROM `goals`)
	END
	)+(
	CASE  WHEN (SELECT MAX(`projectId`) FROM `projects`) IS NULL THEN 0
		ELSE (SELECT MAX(`projectId`) FROM `projects`)
	END
	)";
       $result = mysql_query($q);
       if (!result) {
          echo $q;
          die('Invalid query: ' . mysql_error());
       }


      $q="UPDATE `itemstatus` SET `prikey`=`itemId`+(
	CASE  WHEN (SELECT MAX(`id`) FROM `goals`) IS NULL THEN 0
		ELSE (SELECT MAX(`id`) FROM `goals`)
	END
	)+(
	CASE  WHEN (SELECT MAX(`projectId`) FROM `projects`) IS NULL THEN 0
		ELSE (SELECT MAX(`projectId`) FROM `projects`)
	END
	)";
       $result = mysql_query($q);
       if (!result) {
          echo $q;
          die('Invalid query: ' . mysql_error());
       }

       $q="ALTER TABLE ".$config['prefix']."items DROP PRIMARY KEY, ADD
       PRIMARY KEY (`prikey`)";
       $result = mysql_query($q);
       if (!result) {
          echo $q;
          die('Invalid query: ' . mysql_error());
       }

       $q="ALTER TABLE ".$config['prefix']."itemattributes DROP PRIMARY KEY,
       ADD PRIMARY KEY (`prikey`)";
       $result = mysql_query($q);
       if (!result) {
          echo $q;
          die('Invalid query: ' . mysql_error());
       }
       $q="ALTER TABLE ".$config['prefix']."itemstatus DROP PRIMARY KEY, ADD
       PRIMARY KEY (`prikey`)";
       $result = mysql_query($q);
       if (!result) {
          echo $q;
          die('Invalid query: ' . mysql_error());
       }

       $q="ALTER TABLE ".$config['prefix']."items DROP `itemId`";
       $result = mysql_query($q);
       if (!result) {
          echo $q;
          die('Invalid query: ' . mysql_error());
       }

       $q="ALTER TABLE ".$config['prefix']."itemattributes DROP `itemId`";
       $result = mysql_query($q);
       if (!result) {
          echo $q;
          die('Invalid query: ' . mysql_error());
       }
       $q="ALTER TABLE ".$config['prefix']."itemstatus DROP `itemId`";
       $result = mysql_query($q);
       if (!result) {
          echo $q;
          die('Invalid query: ' . mysql_error());
       }

       $q="ALTER TABLE ".$config['prefix']."items CHANGE `prikey` `itemId`
       INT( 10 ) UNSIGNED NOT NULL DEFAULT '0'";
       $result = mysql_query($q);
       if (!result) {
          echo $q;
          die('Invalid query: ' . mysql_error());
       }

       $q="ALTER TABLE ".$config['prefix']."itemattributes CHANGE `prikey`
       `itemId` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0'";
       $result = mysql_query($q);
       if (!result) {
          echo $q;
          die('Invalid query: ' . mysql_error());
       }

       $q="ALTER TABLE ".$config['prefix']."itemstatus CHANGE `prikey`
       `itemId` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0'";
       $result = mysql_query($q);
       if (!result) {
          echo $q;
          die('Invalid query: ' . mysql_error());
       }
       $q="DELETE FROM ".$config['prefix']."nextactions WHERE `nextaction`
       ='0'";
       $result = mysql_query($q);
       if (!result) {
          echo $q;
          die('Invalid query: ' . mysql_error());
       }

       $q="UPDATE `nextactions` SET `nextaction`=`nextaction`+( CASE  WHEN
       (SELECT MAX(`id`) FROM `goals`) IS NULL THEN 0 ELSE (SELECT MAX(`id`)
       FROM `goals`) END)+( CASE  WHEN (SELECT MAX(`projectId`) FROM
       `projects`) IS NULL THEN 0 ELSE (SELECT MAX(`projectId`) FROM
       `projects`) END)";
       $result = mysql_query($q);
       if (!result) {
          echo $q;
          die('Invalid query: ' . mysql_error());
       }

       $q="ALTER TABLE ".$config['prefix']."lookup ADD `prikey` INT UNSIGNED
       NOT NULL";
       $result = mysql_query($q);
       if (!result) {
          echo $q;
          die('Invalid query: ' . mysql_error());
       }

       $q="UPDATE `lookup` SET `prikey` =`itemId`+( CASE  WHEN (SELECT
       MAX(`id`) FROM `goals`) IS NULL THEN 0 ELSE (SELECT MAX(`id`) FROM
       `goals`) END)+( CASE  WHEN (SELECT MAX(`projectId`) FROM `projects`) IS
       NULL THEN 0 ELSE (SELECT MAX(`projectId`) FROM `projects`) END)";
       $result = mysql_query($q);
       if (!result) {
          echo $q;
          die('Invalid query: ' . mysql_error());
       }

       $q="ALTER TABLE ".$config['prefix']."lookup DROP PRIMARY KEY, ADD
       PRIMARY KEY (`parentId` , `prikey`)";
       $result = mysql_query($q);
       if (!result) {
          echo $q;
          die('Invalid query: ' . mysql_error());
       }

       $q="ALTER TABLE ".$config['prefix']."lookup DROP `itemId`";
       $result = mysql_query($q);
       if (!result) {
          echo $q;
          die('Invalid query: ' . mysql_error());
       }

       $q="ALTER TABLE ".$config['prefix']."lookup CHANGE `prikey` `itemId`
       INT( 10 ) UNSIGNED NOT NULL DEFAULT '0'";
       $result = mysql_query($q);
       if (!result) {
          echo $q;
          die('Invalid query: ' . mysql_error());
       }

       $q="INSERT INTO ".$config['prefix']."items
       (`itemId`,`title`,`description`,`desiredOutcome`) SELECT
       `projectId`,`name`,`description`,`desiredOutcome` FROM `projects`";
       $result = mysql_query($q);
       if (!result) {
          echo $q;
          die('Invalid query: ' . mysql_error());
       }

       $q="INSERT INTO
       ".$config['prefix']."itemattributes(`itemId`,`type`,`categoryId`,`isSomeday`,`deadline`,`repeat`,`suppress`,`suppressUntil`)
       SELECT
       `projectId`,`type`,`categoryId`,`isSomeday`,`deadline`,`repeat`,`suppress`,`suppressUntil`
       FROM `projectattributes`";
       $result = mysql_query($q);
       if (!result) {
          echo $q;
          die('Invalid query: ' . mysql_error());
       }


       $q="INSERT INTO ".$config['prefix']."itemstatus
       (`itemId`,`dateCreated`, `lastModified`, `dateCompleted`) SELECT
       `projectId`,`dateCreated`, `lastModified`, `dateCompleted` FROM
       `projectstatus`";
       $result = mysql_query($q);
       if (!result) {
          echo $q;
          die('Invalid query: ' . mysql_error());
       }

       $q="ALTER TABLE ".$config['prefix']."goals ADD `prikey` INT UNSIGNED
       NOT NULL FIRST";
       $result = mysql_query($q);
       if (!result) {
          echo $q;
          die('Invalid query: ' . mysql_error());
       }

       $q="ALTER TABLE ".$config['prefix']."goals CHANGE `id` `id` INT( 10 )
       UNSIGNED NOT NULL";
       $result = mysql_query($q);
       if (!result) {
          echo $q;
          die('Invalid query: ' . mysql_error());
       }
       $q="UPDATE ".$config['prefix']."goals SET `prikey`=`id`+(
	CASE  WHEN (SELECT MAX(`projectId`) FROM `projects`) IS NULL THEN 0
		ELSE (SELECT MAX(`projectId`) FROM `projects`)
	END
	)";
       $result = mysql_query($q);
       if (!result) {
          echo $q;
          die('Invalid query: ' . mysql_error());
       }

       $q="ALTER TABLE ".$config['prefix']."goals DROP PRIMARY KEY, ADD
       PRIMARY KEY (`prikey`)";
       $result = mysql_query($q);
       if (!result) {
          echo $q;
          die('Invalid query: ' . mysql_error());
       }

       $q="ALTER TABLE ".$config['prefix']."goals DROP `id`";
       $result = mysql_query($q);
       if (!result) {
          echo $q;
          die('Invalid query: ' . mysql_error());
       }

       $q="ALTER TABLE ".$config['prefix']."goals CHANGE `prikey` `id` INT( 10
    ) UNSIGNED NOT NULL DEFAULT '0'";
       $result = mysql_query($q);
       if (!result) {
          echo $q;
          die('Invalid query: ' . mysql_error());
       }

       $q="ALTER TABLE ".$config['prefix']."timeitems ADD `type` ENUM( 'v',
       'o', 'g', 'p', 'a' ) NOT NULL DEFAULT 'a'";
       $result = mysql_query($q);
       if (!result) {
          echo $q;
          die('Invalid query: ' . mysql_error());
       }

       $q="ALTER TABLE ".$config['prefix']."timeitems ADD INDEX ( `type` )";
       $result = mysql_query($q);
       if (!result) {
          echo $q;
          die('Invalid query: ' . mysql_error());
       }


       $q="ALTER TABLE ".$config['prefix']."goals ADD `timeframeId` INT
       UNSIGNED NOT NULL";
       $result = mysql_query($q);
       if (!result) {
          echo $q;
          die('Invalid query: ' . mysql_error());
       }

       $q="UPDATE `goals` SET `timeframeId`= (1 + (
	CASE  WHEN (SELECT MAX(`timeframeId`) FROM `timeitems`) IS NULL THEN 0
		ELSE (SELECT MAX(`timeframeId`) FROM `timeitems`)
	END
	)) WHERE `type`='weekly'";
       $result = mysql_query($q);
       if (!result) {
          echo $q;
          die('Invalid query: ' . mysql_error());
       }

       $q="UPDATE `goals` SET `timeframeId`= (2 + (
	CASE  WHEN (SELECT MAX(`timeframeId`) FROM `timeitems`) IS NULL THEN 0
		ELSE (SELECT MAX(`timeframeId`) FROM `timeitems`)
	END
	)) WHERE `type`='quarterly'";
       $result = mysql_query($q);
       if (!result) {
          echo $q;
          die('Invalid query: ' . mysql_error());
       }

       $q="ALTER TABLE ".$config['prefix']."goals CHANGE `type` `type`
       ENUM('g') NOT NULL DEFAULT 'g'";
       $result = mysql_query($q);
       if (!result) {
          echo $q;
          die('Invalid query: ' . mysql_error());
       }
       $q="INSERT INTO ".$config['prefix']."items
       (`itemId`,`title`,`description`) SELECT `id`,`goal`,`description` FROM
       `goals`";
       $result = mysql_query($q);
       if (!result) {
          echo $q;
          die('Invalid query: ' . mysql_error());
       }

       $q="INSERT INTO ".$config['prefix']."itemattributes
       (`itemId`,`type`,`timeframeId`,`deadline`) SELECT
       `id`,`type`,`timeframeId`, `deadline` FROM `goals`";
       $result = mysql_query($q);
       if (!result) {
          echo $q;
          die('Invalid query: ' . mysql_error());
       }

       $q="INSERT INTO ".$config['prefix']."itemattributes
       (`itemId`,`type`,`timeframeId`,`deadline`) SELECT
       `id`,`type`,`timeframeId`, `deadline` FROM `goals`";
       $result = mysql_query($q);
       if (!result) {
          echo $q;
          die('Invalid query: ' . mysql_error());
       }

       $q="INSERT INTO ".$config['prefix']."itemstatus
       (`itemId`,`dateCreated`, `dateCompleted`) SELECT `id`, `created`,
       `completed` FROM `goals`";
       $result = mysql_query($q);
       if (!result) {
          echo $q;
          die('Invalid query: ' . mysql_error());
       }


       $q="INSERT INTO ".$config['prefix']."lookup (`parentId`,`itemId`)
       SELECT `projectId`,`id` FROM `goals`";
       $result = mysql_query($q);
       if (!result) {
          echo $q;
          die('Invalid query: ' . mysql_error());
       }


       $q="INSERT INTO ".$config['prefix']."timeitems ( `timeframeId` ,
       `timeframe` , `description` , `type` ) VALUES (NULL , 'Weekly', NULL,
       'g'), (NULL , 'Quarterly', NULL , 'g')";
       $result = mysql_query($q);
       if (!result) {
          echo $q;
          die('Invalid query: ' . mysql_error());
       }

       $q="DROP TABLE
       ".$config['prefix']."projectattributes,`projects`,`projectstatus`,`goals`
       ";
       $result = mysql_query($q);
       if (!result) {
          echo $q;
          die('Invalid query: ' . mysql_error());
       }

       $q="ALTER TABLE ".$config['prefix']."items  ORDER BY `itemId`";
       $result = mysql_query($q);
       if (!result) {
          echo $q;
          die('Invalid query: ' . mysql_error());
       }       $q="ALTER TABLE ".$config['prefix']."itemattributes  ORDER BY `itemId`";
       $result = mysql_query($q);
       if (!result) {
          echo $q;
          die('Invalid query: ' . mysql_error());
       }

       $q="ALTER TABLE ".$config['prefix']."itemstatus  ORDER BY `itemId`";
       $result = mysql_query($q);
       if (!result) {
          echo $q;
          die('Invalid query: ' . mysql_error());
       }


       $q="ALTER TABLE ".$config['prefix']."itemattributes ADD INDEX (
          `isSomeday`)";
       $result = mysql_query($q);
       if (!result) {
          echo $q;
          die('Invalid query: ' . mysql_error());
       }


       $q="ALTER TABLE ".$config['prefix']."items CHANGE `itemId` `itemId`
       INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT";
       $result = mysql_query($q);
       if (!result) {
          echo $q;
          die('Invalid query: ' . mysql_error());
       }

       $q="ALTER TABLE ".$config['prefix']."itemattributes CHANGE `itemId`
       `itemId` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT";
       $result = mysql_query($q);
       if (!result) {
          echo $q;
          die('Invalid query: ' . mysql_error());
       }
       $q="ALTER TABLE ".$config['prefix']."itemattributes CHANGE `itemId`
       `itemId` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT";
       $result = mysql_query($q);
       if (!result) {
          echo $q;
          die('Invalid query: ' . mysql_error());
       }

        $q="ALTER TABLE ".$config['prefix']."itemstatus CHANGE `itemId`
        `itemId` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT";
       $result = mysql_query($q);
       if (!result) {
          echo $q;
          die('Invalid query: ' . mysql_error());
       }
       $q="ALTER TABLE ".$config['prefix']."timeitems ADD FULLTEXT
       (`timeframe`)";
       $result = mysql_query($q);
       if (!result) {
          echo $q;
          die('Invalid query: ' . mysql_error());
       }

       $q="ALTER TABLE ".$config['prefix']."timeitems ADD FULLTEXT
       (`description`)";
       $result = mysql_query($q);
       if (!result) {
          echo $q;
          die('Invalid query: ' . mysql_error());
       }
       $q="ALTER TABLE ".$config['prefix']."tickler ADD FULLTEXT (`title`)";
       $result = mysql_query($q);
       if (!result) {
          echo $q;
          die('Invalid query: ' . mysql_error());
       }

       $q="ALTER TABLE ".$config['prefix']."context ADD FULLTEXT (`name`)";
       $result = mysql_query($q);
       if (!result) {
          echo $q;
          die('Invalid query: ' . mysql_error());
       }

       $q="ALTER TABLE ".$config['prefix']."context ADD FULLTEXT
       (`description`)";
       $result = mysql_query($q);
       if (!result) {
          echo $q;
          die('Invalid query: ' . mysql_error());
       }

       $q="ALTER TABLE ".$config['prefix']."checklist ADD FULLTEXT
       (`description`)";
       $result = mysql_query($q);
       if (!result) {
          echo $q;
          die('Invalid query: ' . mysql_error());
       }

       $q="ALTER TABLE ".$config['prefix']."checklist ADD FULLTEXT
       (`description`)";
       $result = mysql_query($q);
       if (!result) {
          echo $q;
          die('Invalid query: ' . mysql_error());
       }

       $q="ALTER TABLE ".$config['prefix']."checklist ADD FULLTEXT (`title`)";
       $result = mysql_query($q);
       if (!result) {
          echo $q;
          die('Invalid query: ' . mysql_error());
       }

       $q="ALTER TABLE ".$config['prefix']."categories ADD FULLTEXT
       (`category`)";
       $result = mysql_query($q);
       if (!result) {
          echo $q;
          die('Invalid query: ' . mysql_error());
       }

       $q="ALTER TABLE ".$config['prefix']."categories ADD FULLTEXT
       (`description`)";
       $result = mysql_query($q);
       if (!result) {
          echo $q;
          die('Invalid query: ' . mysql_error());
       }
       createPreferences();
       createVersion();

       // drop waitingOn
       // note this wasn't in database-upgrade-0.8.sql. do we need to move the
       // waitingOn's over?
       $q="drop table `waitingOn`";
       $result = mysql_query($q);
    }else if($nt==15){
       //has a 0.8 db
       echo "<br>No upgrade needed";
    }else{
       echo "<br>You must be at version 0.7 to upgrade to 0.8.";
    }
	include_once('footer.php');
?>
