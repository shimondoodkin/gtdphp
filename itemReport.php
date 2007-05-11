<?php
//INCLUDES
include_once('header.php');

//RETRIEVE URL VARIABLES
$values=array();
$values['itemId'] = (int) $_GET['itemId'];

//Get item details
$result = query("selectitem",$config,$values,$options,$sort);
$item = $result[0];

//select all nextactions for test
$nextactions=(getNextActionsArray($config,$values,$options,$sort));
$wasNAonEntry = array(); // stash this in case we introduce marking actions as next actions onto this screen

//Find previous and next projects
$values['isSomeday']="n";
$values['type']=$item['type'];
$values['filterquery']  = " WHERE ".sqlparts("typefilter",$config,$values);
$values['filterquery'] .= " AND ".sqlparts("activeitems",$config,$values);
$values['filterquery'] .= " AND ".sqlparts("issomeday",$config,$values);
$result = query("getitems",$config,$values,$options,$sort);

$c=0;
if ($result!="-1") {
    foreach ($result as $row) {
        $ids[$c]=$row['itemId'];
        $titles[$c]=$row['title'];
        if($ids[$c]==$values['itemId']){
            $id=$c;
            }
        $c++;
        }
    }

$n=sizeof($ids);
if(isset($id)){
    if($id==$n-1){
        $nextId=$ids[0];
        $nexttitle=$titles[0];
    }else{
        $nextId=$ids[$id+1];
        $nexttitle=$titles[$id+1];
    }
    if($id==0){
        $previousId=$ids[$n-1];
        $previoustitle=$titles[$n-1];
    }else{
        $previousId=$ids[$id-1];
        $previoustitle=$titles[$id-1];
    }
}

//PAGE DISPLAY AREA

//set item labels
$typename=array();
$typename=getTypes();

$childtype=array();  //I don't like this... but it's the best solution at the moment...

$childtype=getChildType($item['type']);

echo "<h1>".$typename[$item['type']]."&nbsp;Report:&nbsp;".htmlspecialchars(stripslashes($item['title']))."</h1>\n";

//Edit, next, and previous buttons
echo '<span class="editbar">[ <a href="item.php?itemId='.$values['itemId'].'" title="Edit '.htmlspecialchars(stripslashes($item['title'])).'">Edit</a> ]'."\n";
if(isset($previousId)) echo '[ <a href="itemReport.php?itemId='.$previousId.'" title="'.$previoustitle.'">Previous</a> ]'."\n";
if(isset($nextId))  echo '[ <a href="itemReport.php?itemId='.$nextId.'" title="'.$nexttitle.'">Next</a> ]'."\n";
echo "</span>\n";
//Item details
echo '<p>Created: '.$item['dateCreated']."<br />\n";
if ($item['description']) echo 'Description: '.htmlspecialchars(stripslashes($item['description']))."<br />\n";
if ($item['desiredOutcome']) echo 'Desired Outcome:&nbsp;'.htmlspecialchars(stripslashes($item['desiredOutcome']))."<br />\n";
if ($item['categoryId']) echo 'Category:&nbsp;'.htmlspecialchars(stripslashes($item['category']))."<br />\n";
if ($item['contextId']) echo 'Space Context:&nbsp;'.htmlspecialchars(stripslashes($item['cname']))."<br />\n";
if ($item['timeframeId']) echo 'Time Context:&nbsp;'.htmlspecialchars(stripslashes($item['timeframe']))."<br />\n";
if ($item['deadline']) echo 'Deadline:&nbsp;'.$item['deadline']."<br />\n";
if ($item['repeat']) echo 'Repeat every&nbsp;'.$item['repeat'].'&nbsp;days'."<br />\n";
if ($item['suppress']==='y') {
	$reminddate=getTickleDate($item['deadline'],$item['suppressUntil']);
	echo 'Suppressed Until:&nbsp;'.date($config['datemask'],$reminddate)."<br />\n";
}
if ($item['dateCompleted']>0) echo 'Completed On:&nbsp;'.$item['dateCompleted']."\n";
echo "</p>\n";


if ($childtype!=NULL) {
	echo '<form action="processItems.php" method="post">'."\n";
	$values['parentId']=$values['itemId'];
	
	//Create iteration arrays
	$completed = array("n","y");
	
	//table display loop
	foreach ($completed as $comp) foreach ($childtype as $thistype) {
		echo "<div class='reportsection'>\n";
	
	    //Select items by type
	    $values['type']=$thistype;
	    $values['filterquery'] = " AND ".sqlparts("typefilter",$config,$values);
	    if ($comp=="y") {
			$values['filterquery'] .= " AND ".sqlparts("completeditems",$config,$values);
			$result = query("getchildren",$config,$values,$options,$sort);
		} else {
			$values['filterquery'] .= " AND ".sqlparts("pendingitems",$config,$values);  //suppressed items will be shown on report page
			$result = query("getchildren",$config,$values,$options,$sort);
		}
	
		echo ($result != "-1")?'<h2>':'<h3>No '
			,($comp=="y")?('Completed&nbsp;'):('<a href="item.php?parentId='.$values['itemId'].'&amp;action=create&amp;type='.$thistype.'" title="Add new '.$typename[$value].'">')
			,$typename[$thistype],'s'
			,($comp=="y")?'':'</a>'
			,($result != "-1")?'</h2>':'</h3>'
			,"\n";
	
	    if ($result == "-1") {
	    	echo '</div>';
			continue;
		}
		$shownext=( ($comp==="n") && ($values['type']==="a" || $values['type']==="w") );
		$counter=0;
		$suppressed=0;
		echo "<table class='datatable sortable' id='i$comp$thistype' summary='table of children of this item'>\n";
		echo "	<thead><tr>\n";
        if ($shownext) echo "          <td>Next</td>\n";
		echo "		<td>".$typename[$value]."s</td>\n";
		echo "		<td>Description</td>\n";
		echo "		<td>Context</td>\n";
                echo "          <td>Date Created</td>\n";
		if ($comp=="n") {
                        echo "          <td>SuppressUntil</td>\n";
			echo "		<td>Deadline</td>\n";
			echo "		<td>Repeat</td>\n";
			echo "		<td>Complete</td>\n";
		} else {
			echo "		<td>Date Completed</td>\n";
		}
		echo "	</tr></thead>\n";

		foreach ($result as $row) {
			if ($comp=="n") {                                
                //Calculate reminder date as # suppress days prior to deadline
                if ($row['suppress']==='y' && $row['deadline']!=='') {
					$reminddate=getTickleDate($row['deadline'],$row['suppressUntil']);
					if ($reminddate>time()) { // item is not yet tickled - count it, then skip displaying it
						$suppressed++;
						continue;
					}
				} else
					$reminddate=''; 			
			}
			$cleaned=makeClean($row['title']);
			echo '<tr';
			if ($shownext) {
				$isna = (($key = in_array($row['itemId'],$nextactions)) && ($comp!="y"));
				echo ($isna)?' class = "nextactionrow"':''
					,">\n<td><input type="
					,($config['nextaction']=='multiple')?'"checkbox"':'"radio"'
					,'name="isNAs[]"'
					,($isna)?' checked':''
					,' value="'.$row['itemId'].'" title="Mark as a Next Action" /></td>'."\n";
			} else {
				// can't be a next action if completed
				$isna=FALSE;
				echo '>';
			}
			if ($isna) array_push($wasNAonEntry,$row['itemId']);
			echo '<td'
				,($isna)?' class="nextactioncell"':''
				,'><a href = "itemReport.php?itemId=',$row['itemId'],'"><img src="themes/',$config['theme'],'/report.gif" alt="Go to ',$cleaned,' report" /></a>'
				,'<a href = "item.php?itemId=',$row['itemId'],'"><img src="themes/',$config['theme'],'/edit.gif" alt="Edit '.$cleaned.'" /></a>'
				,'<a title="Edit '.$cleaned.'" href="item.php?itemId='.$row['itemId'].'"'
				,($isna)?' class="nextactionlink"><span class="nextActionMarker" title="Next Action">*</span>':'>'
				,$cleaned."</a></td>\n";

			echo '		<td>'.nl2br(stripslashes($row['description']))."</td>\n";
			echo '		<td><a href = "reportContext.php?contextId='.$row['contextId'].'" title="Go to '.htmlspecialchars(stripslashes($row['cname'])).' context report">'.htmlspecialchars(stripslashes($row['cname']))."</a></td>\n";
            echo "          <td>".date($config['datemask'],strtotime($row['dateCreated']))."</td>\n";
			if ($comp=="n") {
				echo '<td>'
					,($reminddate=='')?'&nbsp;':date($config['datemask'],$reminddate)
					,"</td>\n";
				echo prettyDueDate('td',$row['deadline'],$config['datemask']),"\n";
				echo "		<td>".((($row['repeat'])=="0")?'&nbsp;':($row['repeat']))."</td>\n";
				echo '		<td><input type="checkbox" name="isMarked[]" title="Mark '
					,htmlspecialchars(stripslashes($row['title'])),'" value="'
					,$row['itemId'],'" /></td>',"\n";
			} else {
				echo "		<td>".date($config['datemask'],strtotime($row['dateCompleted']))."</td>\n";
			}
			echo "	</tr>\n";
			$counter = $counter+1;
		}
		echo "</table>\n";
		if ($suppressed) {
			echo '<p><a href="listItems.php?tickler=true&amp;type=',$thistype
				,"&amp;parentId=",$values['parentId']
				,'"> There '
				,($suppressed===1)?'is also 1 tickler item':"are also $suppressed tickler items"
				," not yet due for action</a></p>\n";
		}
		$thisurl=parse_url($_SERVER['PHP_SELF']);
		$thisfile=htmlentities(basename($thisurl['path']),ENT_QUOTES);
		echo "<input type='hidden' name='referrer' value='{$thisfile}?itemId={$values['itemId']}' />\n";
		echo '<input type="hidden" name="multi" value="y" />'."\n";
		echo '<input type="hidden" name="action" value="complete" />'."\n";
		echo '<input type="hidden" name="wasNAonEntry" value="'.implode(',',$wasNAonEntry).'" />'."\n"; 
		if ($comp=="n") echo '<input type="submit" class="button" value="Update marked '.$typename[$thistype].'s" name="submit" />'."\n";

		if($counter==0)
			echo 'No&nbsp;'.$typename[$thistype]."&nbsp;items\n";

		echo "</div>\n";
}
echo "</form>\n";
}
include_once('footer.php');
?>
