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

echo "<h1>".$typename[$item['type']]."&nbsp;Report:&nbsp;".makeclean($item['title'])."</h1>\n";

//Edit, next, and previous buttons
echo '<span class="editbar">[ <a href="item.php?itemId='.$values['itemId'].'" title="Edit '.htmlspecialchars(stripslashes($item['title'])).'">Edit</a> ]'."\n";
if(isset($previousId)) echo '[ <a href="itemReport.php?itemId='.$previousId.'" title="'.$previoustitle.'">Previous</a> ]'."\n";
if(isset($nextId))  echo '[ <a href="itemReport.php?itemId='.$nextId.'" title="'.$nexttitle.'">Next</a> ]'."\n";
echo "</span>\n";
//Item details
echo '<p><span class="reportItem">Created:&nbsp;</span>'.$item['dateCreated']."<br />\n";
if ($item['description']) echo '<span class="reportItem">Description:&nbsp;</span>'.escapeQuotes($item['description'])."<br />\n";
if ($item['desiredOutcome']) echo '<span class="reportItem">Desired Outcome:&nbsp;</span>'.escapeQuotes($item['desiredOutcome'])."<br />\n";
if ($item['categoryId']) echo '<span class="reportItem">Category:&nbsp;</span>'.makeclean($item['category'])."<br />\n";
if ($item['contextId']) echo '<span class="reportItem">Space Context:&nbsp;</span>'.makeclean($item['cname'])."<br />\n";
if ($item['timeframeId']) echo '<span class="reportItem">Time Context:&nbsp;</span>'.makeclean($item['timeframe'])."<br />\n";
if ($item['deadline']) echo '<span class="reportItem">Deadline:&nbsp;</span>'.$item['deadline']."<br />\n";
if ($item['repeat']) echo '<span class="reportItem">Repeat every&nbsp;</span>'.$item['repeat'].'&nbsp;days'."<br />\n";
if ($item['suppress']==='y') {
	$reminddate=getTickleDate($item['deadline'],$item['suppressUntil']);
	echo '<span class="reportItem">Suppressed Until:&nbsp;</span>'.date($config['datemask'],$reminddate)."<br />\n";
}
if ($item['dateCompleted']>0) echo '<span class="reportItem">Completed On:&nbsp;</span>'.$item['dateCompleted']."\n";
echo "</p>\n";


if ($childtype!=NULL) {
	$values['parentId']=$values['itemId'];
	
	$thisurl=parse_url($_SERVER['PHP_SELF']);
	$thisfile=makeclean(basename($thisurl['path']));

	//Create iteration arrays
	$completed = array('n','y');
	
	//table display loop
	foreach ($completed as $comp) foreach ($childtype as $thistype) {

	    //Select items by type
	    if ($thistype==='s') {
	       $values['type']='p';
	       $values['isSomeday']='y';
        } else {
            $values['isSomeday']='n';
            $values['type']=$thistype;
        }
	    $values['filterquery'] = " AND ".sqlparts("typefilter",$config,$values);
	    $values['filterquery'] = " AND ".sqlparts("issomeday",$config,$values);

        $q=($comp==='y')?'completeditems':'pendingitems';  //suppressed items will be shown on report page
		$result = query("getchildren",$config,$values,$options,$sort);

		$values['filterquery'] .= " AND ".sqlparts($q,$config,$values);
		$result = query("getchildren",$config,$values,$options,$sort);
		echo "<div class='reportsection'>\n"
            ,($result != "-1")?'<h2>':'<h3>No '
			,($comp=="y")?('Completed&nbsp;'):('<a href="item.php?parentId='.$values['itemId'].'&amp;action=create&amp;type='.$thistype.'" title="Add new '.$typename[$value].'">')
			,$typename[$thistype],'s'
			,($comp=="y")?'':'</a>'
			,($result != "-1")?'</h2>':'</h3>'
			,"\n";
	    if ($result == "-1") {
	    	echo '</div>';
			continue;
		}
		if ($comp!=='y')echo "<form action='processItems.php' method='post'>\n";

		$shownext= ($comp==='n') && ($values['type']==='a' || $values['type']==='w');
		$suppressed=0;
		$dispArray=array();
        if ($shownext) $dispArray['NA']='Next';
        $dispArray['title']=$typename[$thistype].'s';
        $dispArray['description']='Description';
        $dispArray['context']='context';
        $dispArray['created']='Date Created';
		if ($comp=="n") {
            $dispArray['suppress']='Suppress until';
			$dispArray['deadline']='Deadline';
			$dispArray['repeat']='Repeat';
			$dispArray['checkbox']='Complete';
		} else {
			$dispArray['completed']='Date Completed';
		}
        foreach ($dispArray as $key=>$val) $show[$key]=true;
        $dispArray['NA.type']=($config['nextaction']==='single')?'radio':'checkbox';
		$i=0;
		$maintable=array();
        foreach ($result as $row) {
			$cleantitle=makeclean($row['title']);

            $maintable[$i]=array();
            $maintable[$i]['id']=$row['itemId'];
            $maintable[$i]['title']=$cleantitle;
            $maintable[$i]['description']=$row['description'];
            $maintable[$i]['created']=date($config['datemask'],strtotime($row['dateCreated']));

			$maintable[$i]['contextId']=$row['contextId'];
			$maintable[$i]['context']=makeclean($row['cname']);
			$maintable[$i]['context.title']='Go to '.$maintable[$i]['context'].' context report';

			if ($comp==='n') {
                //Calculate reminder date as # suppress days prior to deadline
                if ($row['suppress']==='y' && $row['deadline']!=='') {
					$reminddate=getTickleDate($row['deadline'],$row['suppressUntil']);
					if ($reminddate>time()) { // item is not yet tickled - count it, then skip displaying it
						$suppressed++;
						array_pop($maintable);
						continue;
					}
				} else
					$reminddate='&nbsp;';
                $maintable[$i]['suppress']=$reminddate;

                $deadline=prettyDueDate($row['deadline'],$config['datemask']);
                $maintable[$i]['deadline']      =$deadline['date'];
                $maintable[$i]['deadline.class']=$deadline['class'];
                $maintable[$i]['deadline.title']=$deadline['title'];

				$maintable[$i]['repeat']=($row['repeat']==0)?'&nbsp;':$row['repeat'];

				$maintable[$i]['checkbox.title']="Mark $cleantitle complete";
    			$maintable[$i]['checkboxname']='isMarked[]';
    			$maintable[$i]['checkboxvalue']=$row['itemId'];

    			if ($shownext) {
                    $maintable[$i]['NA']=$comp!=="y" && $nextactions[$row['itemId']];
                    $maintable[$i]['NA.title']='Mark as a Next Action';
                    if ($maintable[$i]['NA']) array_push($wasNAonEntry,$row['itemId']);
                }
   			} else {
				$maintable[$i]['completed']=date($config['datemask'],strtotime($row['dateCompleted']));
            }

			$i++;
		}
		?>
		<table class='datatable sortable' id='i<?php echo $comp,$thistype; ?>' summary='table of children of this item'>
            <?php require('displayItems.inc.php'); ?>
		</table>
		<?php
		if ($suppressed) {
			echo '<p><a href="listItems.php?tickler=true&amp;type=',$thistype
				,"&amp;parentId=",$values['parentId']
				,'"> There '
				,($suppressed===1)?'is also 1 tickler item':"are also $suppressed tickler items"
				," not yet due for action</a></p>\n";
		}
		if ($comp=="n") {
            ?>
<p>
<input type='hidden' name='referrer' value='<?php echo "{$thisfile}?itemId={$values['itemId']}"; ?>' />
<input type="hidden" name="multi" value="y" />
<input type="hidden" name="action" value="complete" />
<input type="hidden" name="wasNAonEntry" value='<?php echo implode(' ',$wasNAonEntry); ?>' />
<input type="submit" class="button" value="Update marked <?php echo $typename[$thistype]; ?>s" name="submit" />
</p>
</form>
            <?php }
		if(!count($maintable))
			echo 'No&nbsp;'.$typename[$thistype]."&nbsp;items\n";

		echo "</div>\n";
    }
}
include_once('footer.php');
?>
