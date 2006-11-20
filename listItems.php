<?php

//INCLUDES
include_once('header.php');

//GET URL VARIABLES
$values = array();
$values['type']=$_GET["type"]{0};
if ($_GET['categoryId']>0) $values['categoryId']=(int) $_GET['categoryId'];
else $values['categoryId']=(int) $_POST['categoryId'];
if ($_GET['contextId']>0) $values['contextId']=(int) $_GET['contextId'];
else $values['contextId']=(int) $_POST['contextId'];
if ($_GET['timeId']>0) $values['timeframeId']=(int) $_GET['timeId'];
else $values['timeframeId']=(int) $_POST['timeId'];

$values['notspacecontext']=$_POST['notspacecontext'];
$values['nottimecontext']=$_POST['nottimecontext'];
$values['notcategory']=$_POST['notcategory'];

if ($values['type']=='s') $values['isSomeday']='y';
else $values['isSomeday']='n';

//Check Session Variables
//If we have contextId from a new filter, change Session value
$contextId=$values['contextId'];
if ($contextId>=0) $_SESSION['contextId']=$contextId;
else $values['contextId']=$_SESSION['contextId'];

//If we have categoryId from a new filter, change Session value
$categoryId=$values['categoryId'];
if ($categoryId>=0) $_SESSION['categoryId']=$categoryId;
else $values['categoryId']=$_SESSION['categoryId'];

$show=array();
/*
parentdetails
    description
    desiredOutcome
    isSomeday
    suppressUntil
    dateCreated
    lastModified
    category
    space context
    time context
    deadline / due
    neglected

childdetails
    desiredOutcome
    isSomeday
    suppressUntil
    dateCreated
    lastModified
    category
    space context
    time context
    deadline / due
    neglected
parent (show at all)


$filter=array();
type
parent type
issomeday //should handle like completed-- not as seperate type
tickler vs active vs completed //?  completed overlays all item types ; remove type=c option from referrers
repeats/doesnotrepeat
space context
time context
category
deadline
due today
neglected

$dynamicsort=array();
on column header
*/


//determine item and parent labels
    switch ($values['type']) {
        case "m" : $typename="Values"; $parentname=""; $values['ptype']=""; $show['parent']="false"; break;
        case "v" : $typename="Visions"; $parentname="Value"; $values['ptype']="m"; break;
        case "o" : $typename="Roles"; $parentname="Vision"; $values['ptype']="v"; break;
        case "g" : $typename="Goals"; $parentname="Role"; $values['ptype']="o"; break;
        case "p" : $typename="Projects"; $parentname="Goal"; $values['ptype']="g"; break;
        case "s" : $typename="Someday/Maybe"; $parentname="Goal"; $values['ptype']="g"; break;
        case "a" : $typename="Actions"; $parentname="Project"; $values['ptype']="p"; break;
        case "n" : $typename="Next Actions"; $parentname="Project";$values['ptype']="p";$values['type']="a"; $display="nextonly"; break;
        case "w" : $typename="Waiting On"; $parentname="Project"; $values['ptype']="p"; break;
        case "r" : $typename="References"; $parentname="Project"; $values['ptype']="p"; break;
        case "i" : $typename="Inbox Items"; $parentname=""; $values['ptype']=""; $show['parent']="false"; break;
        default  : $typename="Items"; $parentname=""; $values['ptype']="";
        }

//SQL CODE
//create filter selectboxes
$cashtml=categoryselectbox($config,$values,$options,$sort);
$cshtml=contextselectbox($config,$values,$options,$sort);
$tshtml=timecontextselectbox($config,$values,$options,$sort);

//select all nextactions for test
$result = query("getnextactions",$config,$values,$options,$sort);

$nextactions = array();
if ($result!="-1") {
    $i=0;
    foreach ($result as $row) {
        $nextactions[$i] = $row['nextaction'];
        $i++;
        }
    }

//Select items

//set query fragments based on filters  : parent and child filters!
//add other filter possibilities

//make generic based on type/someday, etc.
$values['parentfilterquery'] = sqlparts("ptypefilter-w",$config,$values);
$values['parentfilterquery'] .= sqlparts("issomeday",$config,$values);
$values['parentfilterquery'] .= sqlparts("activeitems",$config,$values);

$values['childfilterquery'] = sqlparts("typefilter-w",$config,$values);
//$values['childfilterquery'] .= sqlparts("issomeday",$config,$values);  //?
$values['childfilterquery'] .= sqlparts("activeitems",$config,$values);

if ($values['categoryId'] != NULL && $values['notcategory']!="true") $values['childfilterquery'] .= sqlparts("categoryfilter",$config,$values);
if ($values['categoryId'] != NULL && $values['notcategory']=="true") $values['childfilterquery'] .= sqlparts("notcategoryfilter",$config,$values);

if ($values['contextId'] != NULL && $values['notspacecontext']!="true") $values['childfilterquery'] .= sqlparts("contextfilter",$config,$values);
if ($values['contextId'] != NULL && $values['notspacecontext']=="true") $values['childfilterquery'] .= sqlparts("notcontextfilter",$config,$values);

if ($values['timeframeId'] != NULL && $values['nottimecontext']!="true") $values['childfilterquery'] .= sqlparts("timeframefilter",$config,$values);
if ($values['timeframeId'] != NULL && $values['nottimecontext']=="true") $values['childfilterquery'] .= sqlparts("nottimeframefilter",$config,$values);

//Get items for display
$result = query("getitems",$config,$values,$options,$sort);

//PAGE DISPLAY CODE
	echo '<h2>';
        
        if ($values['completed']=="y") echo 'Completed&nbsp;'.$typename."</h2>\n";
        else echo '<a href="item.php?type='.$values['type'].'" title="Add new '.str_replace("s","",$typename).'">'.$typename."</a></h2>\n";
        echo '<div id="filter">'."\n";
	echo '<form action="listItems.php?type='.$values['type'].'" method="post">'."\n";
	echo "<p>Category:&nbsp;\n";
	echo '<select name="categoryId" title="Filter items by parent category">'."\n";
	echo '	<option value="0">All</option>'."\n";
	echo $cashtml;
	echo "</select>\n";
        echo '<input type="checkbox" name="notcategory" title="Exclude category from list" value="true"';
        if ($values['notcategory']=="true") echo " CHECKED";
        echo '> NOT'."\n";
	echo "&nbsp;&nbsp;&nbsp;\nContext:&nbsp;\n";
	echo '<select name="contextId" title="Filter items by context">'."\n";
	echo '	<option value="">All</option>'."\n";
	echo $cshtml;
	echo "</select>\n";
        echo '<input type="checkbox" name="notspacecontext" title="Exclude spatial context from list" value="true"';
        if ($values['notspacecontext']=="true") echo " CHECKED";
        echo '> NOT'."\n";
        echo "&nbsp;&nbsp;&nbsp;\nTime:&nbsp;\n";
	echo '<select name="timeId" title="Filter items by time context">'."\n";
	echo '	<option value="">All</option>'."\n";
	echo $tshtml;
	echo "</select>\n";
        echo '<input type="checkbox" name="nottimecontext" title="Exclude time context from list" value="true"';
        if ($values['nottimecontext']=="true") echo " CHECKED";
        echo '> NOT'."\n";
        echo '&nbsp;&nbsp;&nbsp;<input type="submit" class="button" value="Filter" name="submit" title="Filter '.$typename.' by category and/or contexts">'."\n";
	echo "</p>\n";
	echo "</form>\n\n";
        echo "</div>\n";
        
	if ($result!="-1") {
                $tablehtml="";
                foreach ($result as $row) {
                    $showme="y";
                    //filter out all but nextactions if $display=nextonly
                    if (($display=='nextonly')  && !($key = array_search($row['itemId'],$nextactions))) $showme="n";
                    if($showme=="y") {
                        $tablehtml .= "	<tr>\n";

                        //parent title
                            if ($show['parent']!="false")$tablehtml .= '		<td><a href = "itemReport.php?itemId='.$row['parentId'].'" title="Go to '.htmlspecialchars(stripslashes($row['ptitle'])).' '.$parentname.' report">';
//                            if ($nonext=="true" && $values['completed']!="y") echo '<span class="noNextAction" title="No next action defined!">!</span>'; 
                            $tablehtml .= stripslashes($row['ptitle'])."</a></td>\n";

                        //item title
                        //if nextaction, add icon in front of action (* for now)
                        if ($key = array_search($row['itemId'],$nextactions)) $tablehtml .= '		<td><a href = "item.php?itemId='.$row['itemId'].'" title="Edit '.htmlspecialchars(stripslashes($row['title'])).'">*&nbsp;'.stripslashes($row['title'])."</td>\n";
                        else $tablehtml .= '		<td><a href = "item.php?itemId='.$row['itemId'].'" title="Edit '.htmlspecialchars(stripslashes($row['title'])).'">'.stripslashes($row['title']).'</td>';

                        //item description
                        $tablehtml .= '		<td>'.nl2br(substr(stripslashes($row['description']),0,72))."</td>\n";

                        //item category
                        $tablehtml .= '          <td><a href="reportCategory.php#'.$row['category'].'" title="Go to the  '.htmlspecialchars(stripslashes($row['category'])).' category">'.stripslashes($row['category'])."</a></td>\n";

                        //item context name
                        $tablehtml .= '		<td><a href = "reportContext.php#'.$row['cname'].'" title="Go to the  '.htmlspecialchars(stripslashes($row['cname'])).' context report">'.stripslashes($row['cname'])."</td>\n";
                        
                        //item timeframe name
                        $tablehtml .= '         <td><a href = "reportTimeContext.php#'.$row['timeframe'].'" title="Go to '.htmlspecialchars(stripslashes($row['timeframe'])).' time context report">'.stripslashes($row['timeframe'])."</td>\n";
                        
                        //item deadline
                        $tablehtml .= "		<td>";
                        if(($row['deadline']) == "0000-00-00" || $row['deadline'] ==NULL) $tablehtml .= "&nbsp;";
                        elseif(($row['deadline']) < date("Y-m-d")) $tablehtml .= '<font color="red"><strong title="Item overdue">'.date("D M j, Y",strtotime($row['deadline'])).'</strong></font>';  //highlight overdue actions
                        elseif(($row['deadline']) == date("Y-m-d")) $tablehtml .= '<font color="green"><strong title="Item due today">'.date("D M j, Y",strtotime($row['deadline'])).'</strong></font>'; //highlight actions due today
                        else $tablehtml .= date("D M j, Y",strtotime($row['deadline']));
                        $tablehtml .= "</td>\n";

                        //item repeat
                        if ($row['repeat']=="0") $tablehtml .= "		<td></td>\n";
                        else $tablehtml .= "		<td>".$row['repeat']."</td>\n";

                        //completion checkbox
                        if ($values['completed']!="y") $tablehtml .= '		<td align="center"><input type="checkbox" align="center" title="Complete '.htmlspecialchars(stripslashes($row['title'])).'" name="completedNas[]" value="'.$row['itemId'].'" /></td>'."\n";
                        $tablehtml .= "	</tr>\n";
                        }
                    }

		if ($tablehtml!="") {
                        echo "<p>Click on ".$parentname." for individual report.</p>\n";
			echo '<form action="processItemUpdate.php" method="post">'."\n";
			echo "<table class='datatable'>\n";
			echo "	<thead>\n";
		    if ($show['parent']!="false") echo "		<td>".$parentname."</td>\n";
			echo "		<td>".$typename."</td>\n";
			echo "		<td>Description</td>\n";
                        echo "          <td>Category</td>\n";
                        echo "          <td>Space Context</td>\n";
			echo "		<td>Time Context</td>\n";
			echo "		<td>Deadline</td>\n";
			echo "		<td>Repeat</td>\n";
                        if ($values['completed']!="y") echo "           <td>Completed</td>\n";
			echo "	</thead>\n";
			echo $tablehtml;
			echo "</table>\n";
			echo '<input type="hidden" name="type" value="'.$values['type'].'" />'."\n";
			echo '<input type="hidden" name="timeId" value="'.$values['timeframeId'].'" />'."\n";
                        echo '<input type="hidden" name="contextId" value="'.$values['contextId'].'" />'."\n";
                        echo '<input type="hidden" name="categoryId" value="'.$values['categoryId'].'" />'."\n";
			echo '<input type="hidden" name="referrer" value="i" />'."\n";
			echo '<input type="submit" class="button" value="Complete '.$typename.'" name="submit">'."\n";
			echo "</form>\n";
		}else{
			$message="Nothing was found.";
			nothingFound($message);
		}
	}elseif($values['completed']!="y") {
		$message="You have no ".$typename." remaining.";
		$prompt="Would you like to create a new ".str_replace("s","",$typename)."?";
		$yeslink="item.php?type=".$values['type'];
		nothingFound($message,$prompt,$yeslink);
	}

	include_once('footer.php');
?>
