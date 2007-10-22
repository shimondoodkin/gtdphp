<?php
//INCLUDES
include_once('header.php');

$values=array();

//SQL Code

//Select notes
$values['filterquery'] = " WHERE ".sqlparts("notefilter",$config,$values);
$reminderresult = query("getnotes",$config,$values,$options,$sort);

//get # space contexts
$res = query("countspacecontexts",$config,$values,$options,$sort);
$numbercontexts=(is_array($res[0]))?(int) $res[0]['COUNT(*)']:0;

//count active items
$values['type'] = "a";
$values['isSomeday'] = "n";
$values['filterquery']  = " AND ".sqlparts("typefilter",$config,$values);
$values['filterquery'] .= " AND ".sqlparts("issomeday",$config,$values);
$values['filterquery'] .= " AND ".sqlparts("activeitems",$config,$values);
$values['filterquery'] .= " AND ".sqlparts("pendingitems",$config,$values);

//get # nextactions
$res = query("countnextactions",$config,$values,$options,$sort);
$numbernextactions=(is_array($res[0]))?(int) $res[0]['nnextactions']:0;

// get # actions
$res =query("countitems",$config,$values,$options,$sort);
$numberitems =(is_array($res[0]))?(int) $res[0]['COUNT(*)']:0;

// get and count active projects
$values['type']= "p";
$values['isSomeday'] = "n";

$stem  = " WHERE ".sqlparts("typefilter",$config,$values)
        ." AND ".sqlparts("activeitems",$config,$values)
        ." AND ".sqlparts("pendingitems",$config,$values);

$values['filterquery'] = $stem." AND ".sqlparts("issomeday",$config,$values);
$pres = query("getitems",$config,$values,$options,$sort);
$numberprojects=($pres==-1)?0:count($pres);

//get and count someday projects
$values['isSomeday'] = "y";
$values['filterquery'] = $stem." AND ".sqlparts("issomeday",$config,$values);
$sm = query("getitems",$config,$values,$options,$sort);
$numbersomeday=($sm==-1)?0:count($sm);


//PAGE DISPLAY CODE
echo "<h2>GTD Summary</h2>\n";
echo '<h4>Today is '.date($config['datemask']).'. (Week '.date("W").'/52 &amp; Day '.date("z").'/'.(365+date("L")).')</h4>'."\n";

echo "<div class='reportsection'>\n";
if ($reminderresult!="-1") {
        echo "<br /><h3>Reminder Notes</h3>";
        $tablehtml="";
        foreach ($reminderresult as $row) {
                $notehtml .= "<p>".date($config['datemask'],strtotime($row['date'])).": ";
                $notehtml .= '<a href = "note.php?noteId='.$row['ticklerId'].'&amp;referrer=s" title="Edit '.makeclean($row['title']).'">'.makeclean($row['title'])."</a>";
                if ($row['note']!="") $notehtml .= " - ".trimTaggedString($row['note']);
                $notehtml .= "</p>\n";
        }
    echo $notehtml;
    }
echo "</div>";

echo "<div class='reportsection'>\n";
echo "<h3>Next Actions</h3>\n";

if($numbernextactions==1) {
    $verb='is';
    $plural='';
} else {
    $verb='are';
    $plural='s';
}

echo "<p>There $verb $numbernextactions"
    ," <a href='listItems.php?type=a&amp;nextonly=true'>Next Action$plural</a> pending"
    ," out of a total of $numberitems <a href='listItems.php?type=a'>Action"
    ,($numberitems==1)?'':'s'
    ,"</a>.</p>\n</div>\n";

echo "<div class='reportsection'>\n";
    echo "<h3>Contexts</h3>\n";
if($numbercontexts==1) {
    echo "<p>There is $numbercontexts <a href='reportContext.php'>Spatial Context</a>.</p>\n";
} else {
    echo "<p>There are $numbercontexts <a href='reportContext.php'>Spatial Contexts</a>.</p>\n";
}
    echo "</div>\n";

    echo "<div class='reportsection'>\n";
	echo "<h3>Project</h3>\n";

    if($numberprojects==1){
        echo '<p>There is 1 active <a href="listItems.php?type=p">Project</a>.</p>'."\n";
    }else{
        echo '<p>There are ' .$numberprojects. ' active <a href="listItems.php?type=p">Projects</a>.</p>'."\n";
    }

	if($numberprojects) {
        echo "<table summary='table of projects'><tbody>\n"
            ,columnedTable(3,$pres)
            ,"</tbody></table>\n";
    }
	echo "</div>\n";

    echo "<div class='reportsection'>\n";
	echo "<h3>Someday/Maybes</h3>\n";

    if($numbersomeday==1){
        echo '<p>There is 1 <a href="listItems.php?type=p&amp;someday=true">Someday/Maybe</a>.</p>'."\n";
    }else{
        echo '<p>There are ' .$numbersomeday.' <a href="listItems.php?type=p&amp;someday=true">Someday/Maybes</a>.</p>'."\n";
    }

	if($numbersomeday) {
        echo "<table summary='table of someday/maybe items'><tbody>\n"
            ,columnedTable(3,$sm)
            ,"</tbody></table>\n";
    }
	echo "</div>\n";

	include_once('footer.php');
?>
