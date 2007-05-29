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
$numbercontexts=(int) $res[0]['COUNT(*)'];

//count active items
$values['type'] = "a";
$values['isSomeday'] = "n";
$values['filterquery']  = " AND ".sqlparts("typefilter",$config,$values);
$values['filterquery'] .= " AND ".sqlparts("issomeday",$config,$values);
$values['filterquery'] .= " AND ".sqlparts("activeitems",$config,$values);
$values['filterquery'] .= " AND ".sqlparts("pendingitems",$config,$values);

//get # nextactions
$res = query("countnextactions",$config,$values,$options,$sort);
$numbernextactions=(int) $res[0]['nnextactions'];

// get # actions
$res =query("countitems",$config,$values,$options,$sort);
$numberitems =(int) $res[0]['COUNT(*)'];

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
                $notehtml .= '<a href = "note.php?noteId='.$row['ticklerId'].'&amp;referrer=s" title="Edit '.htmlspecialchars(stripslashes($row['title'])).'">'.stripslashes($row['title'])."</a>";
                if ($row['note']!="") $notehtml .= " - ".nl2br(stripslashes($row['note']));
                $notehtml .= "</p>\n";
        }
    echo $notehtml;
    }
echo '<p>Reminder notes can be added <a href="note.php?referrer=s" title="Add new reminder">here</a>.</p>'."\n";
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

    $i=0;
    $w1=$numberprojects/3;
    if ($pres!=-1) {
    foreach($pres as $row) {
            if($i < $w1){
                    $c1[]=makeclean($row['title']);
                    $i1[]=$row['itemId'];
                    $q1[]=makeclean($row['description']);
            }
            elseif($i< 2*$w1){
                    $c2[]=makeclean($row['title']);
                    $i2[]=$row['itemId'];
                    $q2[]=makeclean($row['description']);
            }
            else{
                    $c3[]=makeclean($row['title']);
                    $i3[]=$row['itemId'];
                    $q3[]=makeclean($row['description']);
            }
            $i+=1;
            }
    }

//Somedays
   if($numbersomeday){
	$i=0;
        $w2=$numbersomeday/3;
        if ($sm!=-1) {
	foreach($sm as $row) {
                if($i < $w2){
                        $d1[]=makeclean($row['title']);
                        $j1[]=$row['itemId'];
                        $k1[]=makeclean($row['description']);
                }
                elseif($i< 2*$w2){
                        $d2[]=makeclean($row['title']);
                        $j2[]=$row['itemId'];
                        $k2[]=makeclean($row['description']);
                }
                else{
                        $d3[]=makeclean($row['title']);
                        $j3[]=$row['itemId'];
                        $k3[]=makeclean($row['description']);
                }
                $i+=1;
            }
        }
   }

    echo "<div class='reportsection'>\n";
	echo "<h3>Project</h3>\n";

    if($numberprojects==1){
        echo '<p>There is 1 active <a href="listItems.php?type=p">Project</a>.</p>'."\n";
    }else{
        echo '<p>There are ' .$numberprojects. ' active <a href="listItems.php?type=p">Projects</a>.</p>'."\n";
    }

	if($numberprojects) {
        $s="<table summary='table of projects'>\n";
    	$nr = count($c1);

    	for($i=0;$i<$nr;$i+=1){
    		$s.="	<tr>\n";
    		$s.='		<td><a href="itemReport.php?itemId='.$i1[$i].'" title="'.$q1[$i].'">'.$c1[$i]."</a></td>\n";
    		if ($i2[$i]!="" || $nr>1) $s.='		<td><a href="itemReport.php?itemId='.$i2[$i].'" title="'.$q2[$i].'">'.$c2[$i]."</a></td>\n";
    		if ($i3[$i]!="" || $nr>1) $s.='		<td><a href="itemReport.php?itemId='.$i3[$i].'" title="'.$q3[$i].'">'.$c3[$i]."</a></td>\n";
    		$s.="	</tr>\n";
    	}

    	$s.="</table>\n";

    	echo $s;
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
        $t="<table summary='table of someday/maybe items'>\n";
    	$nr = count($d1);

    	for($i=0;$i<$nr;$i+=1){
    		$t.="	<tr>\n";
    		$t.='		<td><a href="itemReport.php?itemId='.$j1[$i].'" title="'.$k1[$i].'">'.$d1[$i]."</a></td>\n";
    		if ($j2[$i]!="" || $nr>1) $t.='		<td><a href="itemReport.php?itemId='.$j2[$i].'" title="'.$k2[$i].'">'.$d2[$i]."</a></td>\n";
    		if ($j3[$i]!="" || $nr>1) $t.='		<td><a href="itemReport.php?itemId='.$j3[$i].'" title="'.$k3[$i].'">'.$d3[$i]."</a></td>\n";
    		$t.="	</tr>\n";
    	}

    	$t.="</table>\n";

    	echo $t;
    }
	echo "</div>\n";

	include_once('footer.php');
?>
