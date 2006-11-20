<?php
   require_once("ses.php")
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
		  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">

<head>
<?php
$config = array();
$options = array();
$sort = array();
require_once("config.php");

//CONNECT TO DATABASE: this will need modification to connect to other dtabases (use SWITCH)
$connection = mysql_connect($config['host'], $config['user'], $config['pass']) or die ("Unable to connect!");
mysql_select_db($config['db']) or die ("Unable to select database!");

require_once("gtdfuncs.php");
require_once("query.inc.php");

echo'	<title>'.$config['title']."</title>\n";

$config['theme']=$_SESSION['theme'];
?>

	<!-- theme main stylesheet -->
	<link rel="stylesheet" href="themes/<?php echo $config['theme']; ?>/style.css" type="text/css"/>

	<!-- theme screen stylesheet (should check to see if this actually exists) -->
	<link rel="stylesheet" href="themes/<?php echo $config['theme']; ?>/style_screen.css" type="text/css" media="screen" />

	<!-- theme script (should check to see if this actually exists) -->
	<script type="text/javascript" src="themes/<?php echo $config['theme']; ?>/theme.js"></script>

	<link rel="shortcut icon" href="./favicon.ico" />
	
	<!-- calendar stylesheet -->
	<link rel="stylesheet" type="text/css" media="all" href="calendar-win2k-cold-1.css" title="win2k-cold-1" />

	<!-- main calendar program -->
	<script type="text/javascript" src="calendar.js"></script>

	<!-- language for the calendar -->
	<script type="text/javascript" src="lang/calendar-en.js"></script>

	<!-- the following script defines the Calendar.setup helper function, which makes
		  adding a calendar a matter of 1 or 2 lines of code. -->
	<script type="text/javascript" src="calendar-setup.js"></script>
</head>
<body>

<div id="container">
<div id="header">
	<h1 id='sitename'><a href='index.php'><?php echo $config['title'];?></a></h1>
</div>

<div id="menudiv">
	<ul id="menulist">

	 	<li>Capture
			<ul>
                                <li><a href="item.php?type=i" title="Drop an item into the inbox">Inbox item</a></li>
				<li><a href="item.php?type=n" title="Create a new next action">Next Action</a></li>
				<li><a href="item.php?type=a" title="Create a new action">Action</a></li>
                                <li><a href="item.php?type=w" title="Create a new waiting on item">Waiting On</a></li>
                                <li><a href="item.php?type=r" title="Create a reference">Reference</a></li>
				<li><a href="item.php?type=p" title="Create a new project">Project</a></li>
                                <li><a href="item.php?type=s" title="Create a future project">Someday/Maybe</a></li>
                                <li><a href="item.php?type=g" title="Define a new goal">Goal</a></li>
                                <li><a href="item.php?type=o" title="Define a new role">Role</a></li>
                                <li><a href="item.php?type=v" title="Define a new vision">Vision</a></li>
                                <li><a href="item.php?type=m" title="Define a new value">Value</a></li>
				<li><a href="newList.php" title="Create a general purpose list">List</a></li>
				<li><a href="newChecklist.php" title="Create a reusable list">Checklist</a></li>
				<li><a href="newContext.php" title="Define a geographical context">Space Context</a></li>
				<li><a href="newTimeContext.php" title="Define a time window for items">Time Context</a></li>
				<li><a href="newCategory.php" title="Define an new meta-category">Category</a></li>
			</ul>

		<li>Process
			<ul>
                                <li><a href="listItems.php?type=i" title="Inbox">Inbox</a></li> 
				<li><a href="reportContext.php" title="Active items sorted by context">Contexts</a></li>
				<li><a href="listItems.php?type=p" title="Active projects">Projects</a></li>
				<li><a href="listItems.php?type=n" title="Active next actions">Next Actions</a></li>
				<li><a href="listItems.php?type=a" title="Active actions">Actions</a></li>
				<li><a href="listItems.php?type=w" title="Active waiting">Waiting On</a></li>
				<li><a href="listItems.php?type=r" title="Active references">References</a></li>
				<li><a href="listItems.php?type=s" title="Someday projects">Someday/Maybe</a></li>
				<li><a href="listList.php" title="General-purpose lists">Lists</a></li>
				<li><a href="listChecklist.php" title="Reusable checklists">Checklists</a></li>
				<li><a href="listItems.php?type=c" title="Completed projects">Achievements</a></li>
				<li><a href="summaryAlone.php" title="Summary view">Summary</a></li>
				<li><a href="tickler.php" title="Hidden items and reminders">Tickler File</a></li>
			</ul>

		<li>Review
			<ul>
				<li><a href="weekly.php" title="Steps in the Weekly Review">Weekly Review</a></li>
                                <li><a href="leadership.php" title="Leadership view">Leadership</a></li>
                                <li><a href="management.php" title="Management view">Management</a></li>
                                <li><a href="orphans.php" title="List items without a parent item">Orphaned Items</a></li>
			</ul>

                <li>Lists
			<ul>
				<li><a href="listList.php" title="General-purpose lists">Lists</a></li>
				<li><a href="listChecklist.php" title="Reuseable checklists lists">Checklists</a></li>
			</ul>

                <li>Config

			<ul>
				<li><a href="newCategory.php" title="Meta-categories">Categories</a></li>
				<li><a href="newContext.php" title="Spatial contexts">Space Contexts</a></li>
				<li><a href="newTimeContext.php" title="Time contexts">Time Contexts</a></li>
				<li><a href="preferences.php" title="User preferences">User Preferences</a></li>
			</ul>

		<li>About
			<ul>
				<li><a href="about.php">License</a></li>
				<li><a href="credits.php">Credits</a></li>
				<li><a href="http://toae.org/boards">Mailing List</a></li>
				<li><a href="http://www.gtd-php.com">Wiki</a></li>
				<li><a href="http://www.frappr.com/gtdphp">Frappr Map</a></li>
				<li><a href="donate.php">Donate</a></li>
			</ul>
	</ul>
</div>

<div id="main">



