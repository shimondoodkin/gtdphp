
<?php

//INCLUDES
include_once('gtdfuncs.php');
include_once('header.php');
include_once('config.php');

//CONNECT TO DATABASE
	$connection = mysql_connect($host, $user, $pass) or die ("unable to connect");
	mysql_select_db($db) or die ("unable to select database!");

//RETRIEVE FORM AND URL VARIABLES
	$pId = (int) $_GET['projectId'];
	$pName =(string) $_GET['projectName'];
 
	echo "<h2>GTD Summary</h2>";
	echo '<h4>Today is '.date("l, F jS, Y").'. (Week '.date("W").'/52 & Day '.date("z").'/'.(365+date("L")).')</h4>';
	
	//SJK altered to show only active projects	
	$query = "SELECT projects.projectId, projects.name, projects.description, projectattributes.categoryId, categories.category
                FROM projects, projectattributes, projectstatus, categories
                WHERE projectattributes.projectId=projects.projectId AND projectattributes.categoryId=categories.categoryId
                AND projectstatus.projectId=projects.projectId AND 
		(projectstatus.dateCompleted IS NULL OR projectstatus.dateCompleted = '0000-00-00') AND projectattributes.isSomeday='n'
                ORDER BY projects.name ASC";
	$result = mysql_query($query) or die ("Error in query");
	$pres=$result;
	$np=mysql_num_rows($result);
	
	//SJK added someday/maybe
	$query = "SELECT projects.projectId, projects.name, projects.description, projectattributes.categoryId, categories.category
                FROM projects, projectattributes, projectstatus, categories
                WHERE projectattributes.projectId=projects.projectId AND projectattributes.categoryId=categories.categoryId
                AND projectstatus.projectId=projects.projectId AND 
		(projectstatus.dateCompleted IS NULL OR projectstatus.dateCompleted = '0000-00-00') AND projectattributes.isSomeday='y'
                ORDER BY projects.name ASC";
	$result = mysql_query($query) or die ("Error in query");
	$sm=$result;
	$nsm=mysql_num_rows($result);

	$query = "Select * from context";
	$result = mysql_query($query)or die ("Error in query");
	$ncon=mysql_num_rows($result);
	


//Currently shows all actions pending, not just nextactions

    // sjr moved nextAction queries to gtdfuncs.php to isolate date
    // wierdness
    $nNextActions=getNumberOfNextActions();
	echo "<h3>Next Actions</h3>";
    if($nNextActions==1){
                echo 'There is ' .$nNextActions. ' <a href="listItems.php?type=n">Next Action</a> pending';
            }else{
                echo 'There are ' .$nNextActions. ' <a href="listItems.php?type=n">Next Actions</a> pending';
            }
    $nActions=getNumberOfActions();
    echo ' out of a total of ' .$nActions. '<a href="listItems.php?type=a"> Actions</a>.';
	echo "</br>";

    /* Do we need this anymore (sjr)?
    if($nCompleted==1){
	        echo " $nCompleted has been completed out of a total $nAllNextActions.";
        }else{
	        echo " $nCompleted have been completed out of a total $nAllNextActions.";
    }
	echo "<br /><br />";
    */
	
	echo "<h3>Contexts</h3>";
    if($ncon==1){
        echo 'There is ' .$ncon. ' <a href="reportContext.php">Spatial Context</a>.';
    }else{
        echo 'There are ' .$ncon. ' <a href="reportContext.php">Spatial Contexts</a>.';
    }


	mysql_free_result($result);
	mysql_close($connection);
	$i=0;
	$w1=$np/3;
	while($row = mysql_fetch_row($pres)){
		if($i < $w1){
			$c1[]=stripslashes($row[1]);
			$i1[]=$row[0];
		}
		elseif($i< 2*$w1){
			$c2[]=stripslashes($row[1]);
			$i2[]=$row[0];
		}
		else{
			$c3[]=stripslashes($row[1]);
			$i3[]=$row[0];
		}
		$i+=1;
	}

//SJK duplicated for somedays
	$i=0;
        $w2=$nsm/3;
        while($row = mysql_fetch_row($sm)){
                if($i < $w2){
                        $d1[]=stripslashes($row[1]);
                        $j1[]=$row[0];
                }
                elseif($i< 2*$w2){
                        $d2[]=stripslashes($row[1]);
                        $j2[]=$row[0];
                }
                else{
                        $d3[]=stripslashes($row[1]);
                        $j3[]=$row[0];
                }
                $i+=1;
        }



	echo "</br></br>";

	echo "<h3>Projects</h3>";

    if($np==1){
        echo 'There is ' .$np. ' <a href="listProjects.php?type=p">Project</a>.';  //SJK changed to project report
    }else{
        echo 'There are ' .$np. ' <a href="listProjects.php?type=p">Projects</a>.';  //SJK changed to project report
    }
	echo "</br></br>";
	
	$s='<table class="boldtable">';
	$nr = count($c1);

	for($i=0;$i<$nr;$i+=1){
		#$s.='<tr><td><a href="projectReport.php?projectId=1">Test</a></td>';
		$s.='<tr><td><a href="projectReport.php?projectId='.$i1[$i].'">'.$c1[$i].'</a></td>';
		$s.='<td><a href="projectReport.php?projectId='.$i2[$i].'">'.$c2[$i].'</a></td>';
		$s.='<td><a href="projectReport.php?projectId='.$i3[$i].'">'.$c3[$i].'</a></td>';
		$s.="</tr>";
	}
	
	$s.="<table>";
	
	echo $s;

//SJK duplicated for Someday/Maybes

	echo"<br /><br />";
	echo "<h3>Someday/Maybe</h3>";

    if($nsm==1){
        echo 'There is ' .$nsm. ' <a href="listProjects.php?type=s">Someday/Maybe</a>.';
    }else{
        echo 'There are ' .$nsm. ' <a href="listProjects.php?type=s">Someday/Maybes</a>.';
    }

	
	$t='<table class="boldtable">';
	$nr = count($d1);

	for($i=0;$i<$nr;$i+=1){
		#$t.='<tr><td><a href="projectReport.php?projectId=1">Test</a></td>';
		$t.='<tr><td><a href="projectReport.php?projectId='.$j1[$i].'">'.$d1[$i].'</a></td>';
		$t.='<td><a href="projectReport.php?projectId='.$j2[$i].'">'.$d2[$i].'</a></td>';
		$t.='<td><a href="projectReport.php?projectId='.$j3[$i].'">'.$d3[$i].'</a></td>';
		$t.="</tr>";
	}

	$t.="</table>";

	echo $t;

	include_once('footer.php');
?>
