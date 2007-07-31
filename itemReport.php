<?php
//INCLUDES
include_once('header.php');

//RETRIEVE URL VARIABLES
$values=array();
$values['itemId'] = (int) $_GET['itemId'];

//Get item details
$values['childfilterquery']=' WHERE '.sqlparts('singleitem',$config,$values);
$result = query("getitemsandparent",$config,$values,$options,$sort);
$item = $result[0];

//select all nextactions for test
$nextactions=(getNextActionsArray($config,$values,$options,$sort));

//Find previous and next projects

if ($item['isSomeday']=="y") $values['isSomeday']="y";
else $values['isSomeday']="n";


if (isset($_SESSION['idlist-'.$item['type']])) {
    $ndx=$_SESSION['idlist-'.$item['type']];
    unset($result);
} else {
    $values['type']=$item['type'];
    $values['filterquery']  = " WHERE ".sqlparts("typefilter",$config,$values);
    $values['filterquery'] .= " AND ".sqlparts("activeitems",$config,$values);
    $values['filterquery'] .= " AND ".sqlparts("pendingitems",$config,$values);
    $values['filterquery'] .= " AND ".sqlparts("issomeday",$config,$values);
    $result = query("getitems",$config,$values,$options,$sort);
    $c=0;
    $ndx=array();
    if ($result!="-1") {
        foreach ($result as $row) $ndx[]=$row['itemId'];
        $_SESSION['idlist-'.$item['type']]=$ndx;
    }
}

$cnt=count($ndx);
if($cnt>1) {
    $key=array_search($values['itemId'],$ndx);
    if ($key!==false) {
        if ($key==0)
            $prev=$cnt-1;
        else
            $prev=$key-1;
            
        if ($key==$cnt-1)
            $next=0;
        else
            $next=$key+1;

        $previousId=$ndx[$prev];
        $nextId    =$ndx[$next];
        if (isset($result)) {
            $previoustitle=$result[$prev]['title'];
            $nexttitle    =$result[$next]['title'];
        } else {
            $previtem = query("selectitem",$config,array('itemId'=>$previousId),$options,$sort);
            $previoustitle=$previtem[0]['title'];
            $nextitem = query("selectitem",$config,array('itemId'=>$nextId),    $options,$sort);
            $nexttitle    =$nextitem[0]['title'];
        }
    }
}
 
//PAGE DISPLAY AREA

//set item labels
$typename=array();
$typename=getTypes();

$childtype=array();  //I don't like this... but it's the best solution at the moment...

$childtype=getChildType($item['type']);

echo "<h1>".$typename[$item['type']]."&nbsp;Report:&nbsp;".makeclean($item['title']).(($item['isSomeday']=="y")?" (Someday) ":"")."</h1>\n";

//Edit, next, and previous buttons
echo "<div class='editbar'>\n";
if ($item['type']==='i') echo "[<a href='assignType.php?itemId={$values['itemId']}'>Assign Type</a>] \n";
echo " [<a href='item.php?itemId={$values['itemId']}' title='Edit "
    ,makeclean($item['title']),"'>Edit</a>] \n";
if(isset($previousId)) echo " [<a href='itemReport.php?itemId=$previousId' title='",makeclean($previoustitle),"'>Previous</a>] \n";
if(isset($nextId))  echo " [<a href='itemReport.php?itemId=$nextId' title='",makeclean($nexttitle),"'>Next</a>] \n";
echo "</div>\n<table id='report' summary='item attributes'><tbody>";
//Item details
if ($item['description']) echo "<tr><th>Description:</th><td>{$item['description']}</td></tr>\n";
if ($item['desiredOutcome']) echo "<tr><th>Desired Outcome:</th><td>{$item['desiredOutcome']}</td></tr>\n";
if ($parents!=-1) {
    echo "<tr><th>Parents:&nbsp;</th><td>";
    $brk='';
    $pids=explode(',',$item['parentId']);
    $pnames=explode($config['separator'],$item['ptitle']);
    foreach ($pids as $pkey=>$pid) {
        $thisparent=makeclean($pnames[$pkey]);
        echo "$brk<a href='itemReport.php?itemId=$pid' title='Go to the $thisparent report'>$thisparent</a> ";
        $brk=', ';
    }
    echo "</td></tr>\n";
}
if ($item['categoryId']) echo "<tr><th>Category:</th><td><a href='editCat.php?id={$item['categoryId']}&amp;field=category'>".makeclean($item['category'])."</a></td></tr>\n";
if ($item['contextId']) echo "<tr><th>Space Context:</th><td><a href='editCat.php?id={$item['contextId']}&amp;field=context'>".makeclean($item['cname'])."</a></td></tr>\n";
if ($item['timeframeId']) echo "<tr><th>Time Context:</th><td><a href='editCat.php?id={$item['timeframeId']}&amp;field=time-context'>".makeclean($item['timeframe'])."</a></td></tr>\n";
if ($item['deadline']) echo '<tr><th>Deadline:</th><td>'.$item['deadline']."</td></tr>\n";
if ($item['repeat']) echo '<tr><th>Repeat every</th><td>'.$item['repeat'].' days'."</td></tr>\n";
if ($item['suppress']==='y') {
	$reminddate=getTickleDate($item['deadline'],$item['suppressUntil']);
	echo '<tr><th>Suppressed Until:</th><td>'.date($config['datemask'],$reminddate)."</td></tr>\n";
}
echo '<tr><th>Created:</th><td>'.$item['dateCreated']."</td></tr>\n";
if ($item['lastModified']) echo '<tr><th>Last modified:</th><td>'.$item['lastModified']."</td></tr>\n";
if ($item['dateCompleted']) echo '<tr><th>Completed On:</th><td>'.$item['dateCompleted']."</td></tr>\n";

echo "</tbody></table>\n";


if ($childtype!=NULL) {
	$values['parentId']=$values['itemId'];
	
	$thisurl=parse_url($_SERVER['PHP_SELF']);
	$thisfile=makeclean(basename($thisurl['path']));

	//Create iteration arrays
	$completed = array('n','y');
	
	//table display loop
	foreach ($completed as $comp) foreach ($childtype as $thistype) {
        $wasNAonEntry = array(); // reset for each table

	    //Select items by type
	    if ($thistype==='s') {
	       $values['type']='p';
	       $values['isSomeday']='y';
        } else {
            $values['isSomeday']='n';
            $values['type']=$thistype;
        }
	    $values['filterquery'] = " AND ".sqlparts("typefilter",$config,$values);
	    $values['filterquery'] .= " AND ".sqlparts("issomeday",$config,$values);

        $q=($comp==='y')?'completeditems':'pendingitems';  //suppressed items will be shown on report page
		$values['filterquery'] .= " AND ".sqlparts($q,$config,$values);
		
        if ($comp==='y' && $config['ReportMaxCompleteChildren']) {
            $values['maxItemsToSelect']=1+$config['ReportMaxCompleteChildren'];
            $values['limitquery'] =sqlparts('limit',$config,$values);
        } else $values['limitquery'] ='';

		$atLimit=0;
        $result = query("getchildren",$config,$values,$options,$sort);
        if ($values['limitquery'] !=='' && count($result)===1+$config['ReportMaxCompleteChildren']) {
            $atLimit=array_pop(array_pop(query('countselected',$config,$values,$options,$sort)));
            array_pop($result);
            $tfoot="<tfoot><tr><td>\n"
                ."<a href='listItems.php?type=$thistype&amp;parentId={$values['parentId']}&amp;completed=true'>"
                ."more...($atLimit items in total)</a>\n</td></tr></tfoot>\n";

        } else $tfoot='';
        ?>
<div class='reportsection'>
        <?php
        $title=$typename[$thistype].'s';
        if ($comp==="y") {
            $title="Completed $title";
        } else {
            $createURL="item.php?parentId={$values['itemId']}&amp;action=create&amp;type=$thistype";
            // inherit some defaults from parent:
            foreach (array('categoryId','contextId','deadline') as $field)
                if ($item[$field]) $createURL.="&amp;$field={$item[$field]}";
            $title="<a href='$createURL' title='Add new {$typename[$value]}'>$title</a>";
        }
        if ($result=="-1") {
            echo "<h3>No $title</h3></div>";
            continue;
        }

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
            $maintable[$i]['itemId']=$row['itemId'];
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
					$maintable[$i]['suppress']=date($config['datemask'],$reminddate);
				} else
					$maintable[$i]['suppress']='&nbsp;';

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
<h2><?php echo $title; ?></h2>
		<?php if ($comp!=='y') { ?>
<form action='processItems.php' method='post'>
        <?php } ?>
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
		if ($comp==="n") { ?>
<p>
<input type="submit" class="button" value="Update marked <?php echo $typename[$thistype]; ?>s" name="submit" />
<input type='hidden' name='referrer' value='<?php echo "{$thisfile}?itemId={$values['itemId']}"; ?>' />
<input type="hidden" name="multi" value="y" />
<input type="hidden" name="action" value="complete" />
<input type="hidden" name="wasNAonEntry" value='<?php echo implode(' ',$wasNAonEntry); ?>' />
</p>
</form>
        <?php } if(!count($maintable)) echo "No {$typename[$thistype]} items\n"; ?>
</div>
<?php
    }
}
include_once('footer.php');
?>
