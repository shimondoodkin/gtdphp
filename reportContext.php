<?php
//INCLUDES
include_once('header.php');

$values=array();

//SQL CODE AREA
//obtain all contexts
$contextResults = query("getspacecontexts",$config,$values,$option,$sort);
$contextNames=array(0=>'none');
if ($contextResults!=-1)
    foreach ($contextResults as $row)
	   $contextNames[$row['contextId']]=makeclean($row[name]);

//obtain all timeframes
$values['type']='a';
$values['timefilterquery'] = ($config['useTypesForTimeContexts'])?" WHERE ".sqlparts("timetype",$config,$values):'';
$timeframeResults = query("gettimecontexts",$config,$values,$options,$sort);
$timeframeNames=array(0=>'none');
$timeframeDesc=array(0=>'none');
if ($timeframeResults != -1 ) foreach($timeframeResults as $row) {
	$timeframeNames[$row['timeframeId']]=makeclean($row['timeframe']);
	$timeframeDesc[$row['timeframeId']]=makeclean($row['description']);
	}

//obtain all active item timeframes and count instances of each
$values['filterquery']=sqlparts("activeitems",$config,$values);
if ($config['contextsummary'] == "all") $itemresults = query("countcontextreport_all",$config,$values,$options,$sort);
else $itemresults = query("countcontextreport_naonly",$config,$values,$options,$sort);

$values['filterquery']=sqlparts('isNA',$config,$values);
$values['extravarsfilterquery'] =sqlparts("getNA",$config,$values);;

foreach ($itemresults as $contextRow) {
	$contextArray[$contextRow['contextId']][$contextRow['timeframeId']] = $contextRow['count'];
	}

//PAGE DISPLAY CODE
echo "<h2>Contexts Summary</h2>\n";
echo "<h3>Spatial Context (row), Temporal Context (column)</h3>\n";

//context table
echo '<table class="datatable" summary="table of contexts" id="contexttable">'."\n";
echo "	<thead><tr>\n";
echo "		<td>Context</td>\n";
foreach ($timeframeNames as $tcId => $tname) {
    $clean=makeclean($tname);
	echo "<td><a href='editCat.php?field=time-context&amp;id=$tcId' title='Edit the $clean time context'>$clean</a></td>\n";
}
echo "		<td>Total</td>\n";
echo "	</tr></thead>\n";
$contextTotal=0;
$timeframeTotal=0;
foreach ($contextNames as $contextId => $cname) {
	$contextCount=0;
	echo "	<tr>\n";
	$clean=makeclean($cname);
	echo "<td><a href='editCat.php?field=context&amp;id={$contextId}' title='Edit the $clean context'>$clean</a></td>\n";
	foreach ($timeframeNames as $timeframeId => $tname) {
		if ($contextArray[$contextId][$timeframeId]!="") {
			$count=$contextArray[$contextId][$timeframeId];
			$contextCount=$contextCount+$count;
			echo "<td><a href='#c{$contextId}t{$timeframeId}'>$count</a></td>\n";
			}
		else echo "		<td>0</td>\n";
		}
	echo "<td><a href='#c{$contextId}'>$contextCount</a></td>\n";
	$contextTotal=$contextTotal+$contextCount;
	echo "	</tr>\n";
	}
echo "	<tr>\n";
echo "		<td>Total</td>\n";
foreach ($timeframeNames as $timeframeId => $tname) {
	$timeframeCount=0;
	foreach ($contextNames as $contextId => $cname) {
		if ($contextArray[$contextId][$timeframeId]!="") {
			$count=$contextArray[$contextId][$timeframeId];
			$timeframeCount=$timeframeCount+$count;
			}
		}
	echo "		<td>".$timeframeCount."</td>\n";
	}
echo "		<td>".$contextTotal."</td>\n";
echo "	</tr>\n";
echo "</table>\n";
echo "\n";
echo "<p>To move to a particular space-time context, select the number.<br />To edit a context select the context name.</p>\n";

$thisurl=parse_url($_SERVER['PHP_SELF']);
$dispArray=array('parent'=>'Project'
    ,'NA'=>'NA'
    ,'title'=>'Action'
    ,'description'=>'Description'
    ,'deadline'=>'Deadline'
    ,'repeat'=>'Repeat'
    ,'checkbox'=>'Complete');
$show=array();
foreach ($dispArray as $key=>$val) $show[$key]=true;

//Item listings by context and timeframe
foreach ($contextArray as $values['contextId'] => $timeframe) {

    echo "<a id='c{$values['contextId']}'></a>\n";
    echo "<h2><a href='editCat.php?field=context&amp;id={$values['contextId']}' "
        ,"title='Edit the ",$contextNames[$values['contextId']]," context'>"
        ,"Context:&nbsp;",$contextNames[$values['contextId']],"</a></h2>\n";

    foreach ($timeframe as $values['timeframeId'] => $itemCount) {

        $values['type'] = "a";
        $values['isSomeday'] = "n";
        $values['childfilterquery']  = " WHERE ".sqlparts("typefilter",$config,$values);
        $values['childfilterquery'] .= " AND ".sqlparts("activeitems",$config,$values);
        $values['childfilterquery'] .= " AND ".sqlparts("timeframefilter",$config,$values);
        $values['childfilterquery'] .= " AND ".sqlparts("contextfilter",$config,$values);
        $values['childfilterquery'] .= " AND ".sqlparts("issomeday",$config,$values);
		$values['childfilterquery'] .= " AND ".sqlparts("pendingitems",$config,$values);
        $result = query("getitemsandparent",$config,$values,$options,$sort);

        $maintable=array();
        $i=0;
        $wasNAonEntry=array();
		if (is_array($result)) foreach ($result as $row) {
            $maintable[$i]=array();
            $maintable[$i]['itemId']=$row['itemId'];
			$maintable[$i]['description']=$row['description'];
			$maintable[$i]['repeat'] = ($row['repeat']=="0")?'&nbsp;':$row['repeat'];

            if($row['deadline']) {
                $deadline=prettyDueDate($row['deadline'],$config['datemask']);
                $maintable[$i]['deadline'] =$deadline['date'];
                $maintable[$i]['deadline.class']=$deadline['class'];
                $maintable[$i]['deadline.title']=$deadline['title'];
            } else $maintable[$i]['deadline']='';
        
            $maintable[$i]['title']=makeclean($row['title']);
            $maintable[$i]['title.title']='Edit '.$maintable['title'];

			$maintable[$i]['ptitle']=$row['ptitle'];
			$maintable[$i]['parentId']=$row['parentId'];
			if ($row['parentId']=='') $maintable[$i]['parent.class']='noparent';

			$maintable[$i]['checkboxname']='isMarked[]';
			$maintable[$i]['checkbox.title']='Complete '.$maintable['title'];
			$maintable[$i]['checkboxvalue']=$row['itemId'];

            $maintable[$i]['NA'] = $row['NA'];
            if ($row['NA']) array_push($wasNAonEntry,$row['itemId']);

			$i++;
		}
        if (count($maintable)) {
            echo "<a id='c{$values['contextId']}t{$values['timeframeId']}'></a>\n"
                ,"<h3><a href='editCat.php?field=time-context&amp;id={$values['timeframeId']}' title='{$timeframeDesc[$values['timeframeId']]}'>"
                ,"Time Context:&nbsp;",$timeframeNames[$values['timeframeId']],"</a></h3>\n";
            ?>
            <form action="processItems.php" method="post">
                <table class="datatable sortable" summary="table of actions" id="actiontable<?php echo $values['contextId'],'t',$values['timeframeId']; ?>">
                    <?php require('displayItems.inc.php'); ?>
                </table>
                <div>
                	<input type="hidden" name="referrer" value="<?php echo basename($thisurl['path']),'#',$thisAnchor; ?>" />
                    <input type="hidden" name="multi" value="y" />
        		    <input type="hidden" name="wasNAonEntry" value="<?php echo implode(' ',$wasNAonEntry); ?> " />
                    <input type="hidden" name="action" value="complete" />
                    <input type="submit" class="button" value="Update Actions" name="submit" />
                </div>
            </form>
            <?php
        }
    }
}
$_SESSION['lastfilterp']=$_SESSION['lastfiltera']=basename($thisurl['path']);
include_once('footer.php');
?>
