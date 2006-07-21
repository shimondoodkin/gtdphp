<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
      <html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">

<head>

	<title>GTD</title>


	<style type="text/css">
	body {
		text-align: left;
		font-family:"Lucida Grande", "Lucida Sans", Verdana, Helvetica, sans-serif;
		font-size: 11px;
		}
	
	
	h1 {
		font-size: 16px;
		font-weight: bold;
		color: #ccb;
		}
		
	h2 {
		color: #ccb;
		font-size: 16px;
		font-weight: bold;
		background: none;
		border-bottom: 2px solid #ccb;
		width: 90%
		}
	
	h3 {
		color: #998;
		font-size: 12px;
		font-weight: bold;
		background: none;
		}
	
	
	
	a, 
	a:link {
		color:#39c; 
		text-decoration: none;
		font-weight: bold;
		}
	a:visited {
		color:#39c; 
		text-decoration: none;
		}
	a:hover{
		text-decoration: underline;
		color:#039; 
		}
	
	
	select, input, textarea {
		color: #665;
		font-family:"Lucida Grande", "Lucida Sans", Verdana, Helvetica, sans-serif;
		font-size: 11px;
		padding: 2px;
		border: 2px solid #ccb;
	}
	.button {
		margin: 4px 10px;
		}
	
	
	table {
		border: 2px dotted #eed;
		margin: 0 4px 10px 4px;
		}
		
	th {
		border: 1px solid #998;
		background: #bba;
		color: #eed;
		font-weight: bold;
		font-family:"Lucida Grande", "Lucida Sans", Verdana, Helvetica, sans-serif;
		font-size: 10px;
		}
		
	tr {
		border-bottom: 2px solid #ccb;
		}
		
	td {
		background: #fcfcf0;
		color: #666;
		border-top: none;
		border-bottom: 1px dotted #eed;
		border-right: none;
		border-left: none;
		padding: 4px;
		font-weight: normal;
		font-family:"Lucida Grande", "Lucida Sans", Verdana, Helvetica, sans-serif;
		font-size: 10px;
		}
	
        #container{
		width: 90%;
		padding 2px 0px 2px 20px;
		background: none;
		margin: 10px;	
	}

	#header {
/*		width: 90%;
		padding: 2px 0px 2px 20px;
	*/	margin: 10px auto;
		background: none;
		border-bottom: 4px solid #eed;
		}
	
	#sidebar {
		float: left;
		display: inline;
		border-right: 1px solid #eed;
		margin: 0 10px 0 0;
		width: 150px;
		text-align:left;
		font: 10px "Lucida Grande", "Lucida Sans", Verdana, Helvetica, sans-serif;
		}
	
	#main {
		margin: 0 10px 0 210px;
		border: none;
		}
		
	#footer {
		color:#666;
		border-top: 2px solid #eed;
		clear: both;
		/*width: 90%;
		*/margin: 20px 0 10px 0;
		text-align: right;
		}
	
	
	.menu a,
	.menu a:link,
	.menu a:visited {
		background: #fff;
		display: block;
	/*	width: 98%; */ 
		text-decoration: none;
		}
	.menu a:hover {
		background: #eed;
		text-decoration: none;
		}
	
	
	ul.menu {
		list-style: none;
		font-weight: bold;
		margin: 10px 0;
		padding-right: 10px;
		}
		
	ul.menu li {
		list-style: none;
		font-weight: bold;
		margin: 0;
		padding: 2px;
		border-bottom: 1px solid #eed;
		}
	
	ul.menu li ul {
		list-style: none;
		font-weight: bold;
		margin: 0;
		padding: 0 0 10px 10px;
		}
	
	
	
	a.pageheading:hover {
		color:#003300; 
		text-decoration: underline;
		}
		
	a.subheading:hover {
		color:#003300; 
		text-decoration: underline;
		}
	
	.morelink,
	.footer {
		text-align:right;
		font-size: 10px;
		}
		
	.morelink {
		color:#930
		}
		
	.byline {
		text-align:left;
		font: 100% "Lucida Grande", "Lucida Sans", Verdana, Helvetica, sans-serif;
		font-weight:bold;
		}
	
	</style>


</head>
<body>

<div id="container">
<div id="header">
	<h1 id="sitename"><a href="index.php">Getting Things Done</a></h1>
</div>
		

<div id="sidebar">

<ul class="menu">

	<li>Reports</li>
	<li>	<ul>
			<li><a href="reportContext.php" title="Active items sorted by context">Contexts</a></li>
			<li><a href="listProjects.php?pType=p" title="Active projects">Projects</a></li>
			<li><a href="listItems.php?type=n" title="Active Next Actions">Next Actions</a></li>
			<li><a href="listItems.php?type=a" title="Active actions">Actions</a></li>
			<li><a href="listItems.php?type=w" title="Active waiting">Waiting On</a></li>
			<li><a href="listItems.php?type=r" title="Active references">References</a></li>
			<li><a href="listProjects.php?pType=s" title="Someday projects">Someday/Maybe</a></li>
			<li><a href="listList.php" title="General-purpose lists">Lists</a></li>
			<li><a href="listChecklist.php" title="Reusable checklists">Checklists</a></li>
			<li><a href="listProjects.php?pType=c" title="Completed projects">Achievements</a></li>
			<li><a href="summaryAlone.php" title="Summary view">Summary</a></li>
			<li><a href="tickler.php" title="Hidden items and reminders">Tickler File</a></li>
		</ul> 
         </li>


	<li>Review</li>
        <li>
		<ul>
			<li><a href="weekly.php">Weekly Review</a></li>
			<li><a href="listGoals.php">List Goals</a></li>
			<li><a href="newGoal.php">Add Goal</a></li>
    		</ul>
	</li>

    <li>New</li>
    	<li>
		<ul>
			<li><a href="item.php?type=n">Next Action</a></li>
			<li><a href="item.php?type=a">Action</a></li>
			<li><a href="project.php?type=p">Project</a></li>
			<li><a href="item.php?type=w">Waiting On</a></li>
			<li><a href="item.php?type=r">Reference</a></li>
			<li><a href="project.php?type=s">Someday/Maybe</a></li>
			<li><a href="newList.php">List</a></li>
			<li><a href="newChecklist.php">Checklist</a></li>
			<li><a href="newContext.php">Space Context</a></li>
			<li><a href="newTimeContext.php">Time Context</a></li>
			<li><a href="newCategory.php">Category</a></li>
		</ul>
	</li>
    <li>Meta</li>
    	<li>	
		<ul>
			<li><a href="about.php">About</a></li>
			<li><a href="credits.php">Credits</a></li>
			<li><a href="http://gtd-php.sourceforge.net/">Development</a></li>
			<li><a href="http://toae.org/boards">Mailing List</a></li>
			<li><a href="http://www.frappr.com/gtdphp">Map</a></li>
			<li><A href="http://www.gtd-php.com">Wiki</a></li>
		</ul>
	</li>
<!--		
	<li>Admin</li>
		<ul>
 		   	<li><a href="phpSettings.php">Php Info</a></li>
		</ul>
-->

</ul>

</div><!-- sidebar -->

<div id="main">
