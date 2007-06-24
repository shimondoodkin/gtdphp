<?php
//INCLUDES
include_once('header.php');
if ($config['debug'] & _GTD_DEBUG) {
    echo '<pre>POST: ',var_dump($_POST),'</pre>';
}
//page display options array--- can put defaults in preferences table/config/session and load into $show array as defaults...
$show=array();

//GET URL VARIABLES
$values = array();
$filter = array();

$filter['type']           =getVarFromGetPost('type','a');
$filter['contextId']      =getVarFromGetPost('contextId',NULL);
if ($filter['contextId']==='0') $filter['contextId']=NULL;
$filter['categoryId']     =getVarFromGetPost('categoryId',NULL);
if ($filter['categoryId']==='0') $filter['categoryId']=NULL;
$filter['timeframeId']    =getVarFromGetPost('timeframeId',NULL);
if ($filter['timeframeId']==='0') $filter['timeframeId']=NULL;
$filter['notcategory']    =getVarFromGetPost('notcategory');
$filter['notspacecontext']=getVarFromGetPost('notspacecontext');
$filter['nottimecontext'] =getVarFromGetPost('nottimecontext');
$filter['tickler']        =getVarFromGetPost('tickler');           //suppressed (tickler file): true/false
$filter['someday']        =getVarFromGetPost('someday');           //someday/maybe:true/empty
$filter['nextonly']       =getVarFromGetPost('nextonly');          //next actions only: true/empty 
$filter['completed']      =getVarFromGetPost('completed');         //status:true/false (completed/pending)
$filter['dueonly']        =getVarFromGetPost('dueonly');           //has due date:true/empty
$filter['repeatingonly']  =getVarFromGetPost('repeatingonly');     //is repeating:true/empty
$filter['parentId']       =getVarFromGetPost('parentId');
$filter['everything']     =getVarFromGetPost('everything');        //overrides filter:true/empty

if ($filter['type']==='s') {
    $filter['someday']=true;
    $filter['type']='p';
}
$values['type']           =$filter['type'];
$values['parentId']       =$filter['parentId'];
$values['contextId']      =$filter['contextId'];
$values['categoryId']     =$filter['categoryId'];
$values['timeframeId']    =$filter['timeframeId'];

if ($config['debug'] & _GTD_DEBUG) echo '<pre>Filter:',print_r($filter),'</pre>';

//SQL CODE

//create filters for selectboxes
$values['timefilterquery'] = ($config['useTypesForTimeContexts'])?" WHERE ".sqlparts("timetype",$config,$values):'';

//create filter selectboxes
$cashtml=categoryselectbox($config,$values,$options,$sort);
$cshtml=contextselectbox($config,$values,$options,$sort);
$tshtml=timecontextselectbox($config,$values,$options,$sort);

//select all nextactions for test
$nextactions=(getNextActionsArray($config,$values,$options,$sort));

/*
    ===================================================================
    build array of notes
    ===================================================================
*/
//Tickler file header and notes section
$remindertable=array();

if ($filter['tickler']=="true") {
    $values['filterquery'] = '';
    $result = query("getnotes",$config,$values,$options,$sort);
    if ($result!="-1") {
        foreach ($result as $row) {
            $remindertable[]=array(
                'id'=>$row['ticklerId']
                ,'date'=>$row['date']
                ,'title'=>htmlentities(stripslashes($row['title']),ENT_QUOTES)
                ,'note'=>nl2br($row['note'])
            );
        }
    }
}

/*
    ===================================================================
    finished building array of notes
    ===================================================================
*/
// pass filters in referrer
$thisurl=parse_url($_SERVER['PHP_SELF']);
$referrer = basename($thisurl['path']).'?';
foreach($filter as $filterkey=>$filtervalue)
    if ($filtervalue!='') $referrer .= "{$filterkey}={$filtervalue}&amp;";


//Select items

//set default table column display options (kludge-- needs to be divided into multidimensional array for each table type and added to preferences table
$show['parent']=TRUE;
$show['NA']=FALSE;
$show['title']=TRUE;
$show['description']=TRUE;
$show['outcome']=FALSE;
$show['isSomeday']=FALSE;
$show['suppress']=FALSE;
$show['suppressUntil']=FALSE;
$show['dateCreated']=FALSE;
$show['lastModified']=FALSE;
$show['category']=TRUE;
$show['context']=TRUE;
$show['timeframe']=TRUE;
$show['deadline']=TRUE;
$show['repeat']=TRUE;
$show['dateCompleted']=FALSE;
$show['checkbox']=TRUE;

//determine item and parent labels, set a few defaults
switch ($values['type']) {
    case "m" : $typename="Value"; $parentname=""; $values['ptype']=""; $show['parent']=FALSE; $show['checkbox']=FALSE; $show['repeat']=FALSE; $show['dateCreated']=TRUE; $show['deadline']=FALSE; $show['outcome']=TRUE; $show['context']=FALSE; $show['timeframe']=FALSE; $checkchildren=TRUE; break;
    case "v" : $typename="Vision"; $parentname="Value"; $values['ptype']="m"; $show['checkbox']=FALSE; $show['repeat']=FALSE; $show['dateCreated']=TRUE; $show['deadline']=FALSE; $show['outcome']=TRUE; $show['context']=FALSE; $show['timeframe']=FALSE; $checkchildren=TRUE; break;
    case "o" : $typename="Role"; $parentname="Vision"; $values['ptype']="v"; $show['checkbox']=FALSE; $show['repeat']=FALSE; $show['deadline']=FALSE; $show['outcome']=TRUE; $show['context']=FALSE; $show['timeframe']=FALSE; $checkchildren=TRUE; break;
    case "g" : $typename="Goal"; $parentname="Role"; $values['ptype']="o"; $show['outcome']=TRUE; $show['context']=FALSE; $checkchildren=TRUE; break;
    case "p" : $typename="Project"; $parentname="Goal"; $values['ptype']="g"; $show['context']=FALSE; $show['timeframe']=FALSE; $checkchildren=TRUE; break;
    case "a" : $typename="Action"; $parentname="Project"; $values['ptype']="p"; $show['parent']=TRUE; $show['NA']=TRUE; $show['category']=FALSE; $checkchildren=FALSE; break;
    case "w" : $typename="Waiting On"; $parentname="Project"; $values['ptype']="p"; $show['parent']=TRUE; $checkchildren=FALSE; break;
    case "r" : $typename="Reference"; $parentname="Project"; $values['ptype']="p"; $show['parent']=TRUE; $show['category']=FALSE; $show['context']=FALSE; $show['timeframe']=FALSE; $show['checkbox']=FALSE; $show['repeat']=FALSE; $show['dateCreated']=TRUE; $checkchildren=FALSE; break;
    case "i" : $typename="Inbox Item"; $parentname=""; $values['ptype']=""; $show['parent']=FALSE; $show['category']=FALSE; $show['context']=FALSE; $show['timeframe']=FALSE; $show['deadline']=FALSE; $show['dateCreated']=TRUE; $show['repeat']=FALSE; $checkchildren=FALSE; break;
    default  : $typename="Item"; $parentname=""; $values['ptype']=""; $checkchildren=FALSE; 
}

$show['flags']=$checkchildren; // temporary measure; to be made user-configurable later


if ($filter['someday']=="true") {
    $show['dateCreated']=TRUE;
    $show['context']=FALSE;
    $show['repeat']=FALSE;
    $show['NA']=FALSE;
    $show['deadline']=FALSE;
    $show['timeframe']=FALSE;
    $checkchildren=FALSE; 
}

if ($filter['tickler']=="true") $show['suppressUntil']=TRUE;

if ($filter['dueonly']=="true") $show['deadline']=TRUE;

if ($filter['repeatingonly']=="true") {
    $show['deadline']=TRUE;
    $show['repeat']=TRUE;
}

if ($filter['completed']=="true") {
    $show['suppress']=FALSE;
    $show['NA']=FALSE;
    $show['flags']=FALSE;
    $show['suppressUntil']=FALSE;
    $show['dateCreated']=TRUE;
    $show['deadline']=FALSE;
    $show['repeat']=FALSE;
    $show['dateCompleted']=TRUE;
    $show['checkbox']=FALSE;
    $checkchildren=FALSE; 
}

if ($filter['everything']=="true") {
$show['parent']=TRUE;
$show['NA']=FALSE;
$show['title']=TRUE;
$show['description']=TRUE;
$show['outcome']=FALSE;
$show['isSomeday']=FALSE;
$show['suppress']=FALSE;
$show['suppressUntil']=TRUE;
$show['dateCreated']=TRUE;
$show['lastModified']=FALSE;
$show['category']=TRUE;
$show['context']=TRUE;
$show['timeframe']=TRUE;
$show['deadline']=TRUE;
$show['repeat']=TRUE;
$show['dateCompleted']=TRUE;
$show['checkbox']=FALSE;    }
    
//set query fragments based on filters
$values['childfilterquery'] = "";
$values['parentfilterquery'] = "";
$values['filterquery'] = "";

//type filter
$values['childfilterquery'] = " WHERE ".sqlparts("typefilter",$config,$values);

/*
Override all filter selections if $filter['everything'] is true
*/

if ($filter['everything']!="true") {

    //filter box filters
    if ($filter['categoryId'] != NULL && $filter['notcategory']!="true") $values['childfilterquery'] .= " AND ".sqlparts("categoryfilter",$config,$values);
    if ($filter['categoryId'] != NULL && $filter['notcategory']=="true") $values['childfilterquery'] .= " AND ".sqlparts("notcategoryfilter",$config,$values);
    
    if ($filter['contextId'] != NULL && $filter['notspacecontext']!="true") $values['childfilterquery'] .= " AND ".sqlparts("contextfilter",$config,$values);
    if ($filter['contextId'] != NULL && $filter['notspacecontext']=="true") $values['childfilterquery'] .= " AND ".sqlparts("notcontextfilter",$config,$values);
    
    if ($filter['timeframeId'] != NULL && $filter['nottimecontext']!="true") $values['childfilterquery'] .= " AND ".sqlparts("timeframefilter",$config,$values);
    if ($filter['timeframeId'] != NULL && $filter['nottimecontext']=="true") $values['childfilterquery'] .= " AND ".sqlparts("nottimeframefilter",$config,$values);
    
    if ($filter['completed']=="true") $values['childfilterquery'] .= " AND ".sqlparts("completeditems",$config,$values);
    else $values['childfilterquery'] .= " AND " .sqlparts("pendingitems",$config,$values);
    
    //problem with project somedays vs actions...want an OR, but across subqueries;
    if ($filter['someday']=="true") {
        $values['isSomeday']="y";
        $values['childfilterquery'] .= " AND " .sqlparts("issomeday",$config,$values);
    } else {
        $values['isSomeday']="n";
        $values['childfilterquery'] .= " AND ".sqlparts("issomeday",$config,$values);
    //    $values['filterquery'] .= " WHERE " .sqlparts("issomeday-parent",$config,$values);
    }
    
    //problem: need to get all items with suppressed parents(even if child is not marked suppressed), as well as all suppressed items
    if ($filter['tickler']=="true") {
        if ($values['parentId']!='') $values['filterquery'] .= " WHERE ".sqlparts("hasparent",$config,$values);
        $values['childfilterquery'] .= " AND ".sqlparts("suppresseditems",$config,$values);
    } else {
        $values['childfilterquery'] .= " AND ".sqlparts("activeitems",$config,$values);
        $values['filterquery'] .= " AND ".sqlparts("activeparents",$config,$values);
    }
    
    if ($filter['repeatingonly']=="true") $values['childfilterquery'] .= " AND " .sqlparts("repeating",$config,$values);
    
    if ($filter['dueonly']=="true") $values['childfilterquery'] .= " AND " .sqlparts("due",$config,$values);
    
    /*
    $filter['nextonly']
    */   
}

/*
Section Heading
*/

$link="item.php?type=".$values['type'];
if($filter['everything']=="true")
    $sectiontitle = '';
else {
    if ($filter['completed']=="true")
        $sectiontitle = 'Completed ';
    elseif ($filter['dueonly']=="true")
        $sectiontitle =  'Due ';
    else $sectiontitle ='';

    if ($filter['repeatingonly']=="true") {
        $sectiontitle .= 'Repeating ';
        $link.='&amp;repeat=true';
    }
    if ($filter['someday']=="true") {
        $sectiontitle .= 'Someday/Maybe ';
        $link.='&amp;someday=true';
    }
    if ($filter['nextonly']=="true") {
        $sectiontitle .= 'Next ';
        $link .='&amp;nextonly=true';
    }
}
$sectiontitle .= $typename;
/*
    ===================================================================
    main query: build array of items
    ===================================================================
*/
$result=query("getitemsandparent",$config,$values,$options,$sort);
// TOFIX - if an action has several project parents, it appears several times on the list
$maintable=array();
$thisrow=0;
if ($result!="-1") {
    $nonext=FALSE;
    $nochildren=FALSE;
    $wasNAonEntry=array();  // stash this in case we introduce marking actions as next actions onto this screen
    if ($filter['everything']=="true") $filter['nextonly']="false";
    foreach ($result as $row) if (($filter['nextonly']!="true")  || $nextactions[$row['itemId']]) {        
        //filter out all but nextactions if $filter['nextonly']==true
    
        $nochildren=false;
        $nonext=false;
        if ($checkchildren) {
            $values['parentId']=$row['itemId'];
            $nochildren=(query("countchildren",$config,$values)==="-1");
            if ($values['type']=="p") $nonext=(query("selectnextaction",$config,$values)=="-1");
        }
        
        $isNextAction = $nextactions[$row['itemId']]===true;
        if ($isNextAction) array_push($wasNAonEntry,$row['itemId']);
        
        $maintable[$thisrow]=array();
        $maintable[$thisrow]['id']=$row['itemId'];
        $maintable[$thisrow]['class'] = ($nonext || $nochildren)?'noNextAction':'';
        $maintable[$thisrow]['NA'] =$isNextAction;

        $maintable[$thisrow]['dateCreated'] = $row['dateCreated'];
        $maintable[$thisrow]['lastModified']= $row['lastModified'];
        $maintable[$thisrow]['dateCompleted']= $row['dateCompleted'];
        $maintable[$thisrow]['type'] =$row['type'];

        $maintable[$thisrow]['parent']=makeclean($row['ptitle']);
        $maintable[$thisrow]['parentid']=$row['parentId'];

        // add markers to indicate if this is a next action, or a project with no next actions, or an item with no childern
        if ($nochildren)
            $maintable[$thisrow]['flags'] = 'noChild';
        elseif ($nonext)
            $maintable[$thisrow]['flags'] = 'noNA';
        else
            $maintable[$thisrow]['flags'] = '';

        //item title
        $maintable[$thisrow]['doreport']=!($row['type']=="a" || $row['type']==="r" || $row['type']==="w" || $row['type']==="i");
        
        $cleantitle=makeclean($row['title']);
        $maintable[$thisrow]['title.class'] = 'maincolumn';
        $maintable[$thisrow]['title'] =$cleantitle;

        $maintable[$thisrow]['checkbox.title']='Complete '.$cleantitle;
        $maintable[$thisrow]['checkboxname']= 'isMarked[]';
        $maintable[$thisrow]['checkboxvalue']=$row['itemId'];

        $maintable[$thisrow]['description'] = $row['description'];
        $maintable[$thisrow]['outcome'] = $row['outcome'];

        $maintable[$thisrow]['category'] =makeclean($row['category']);
        $maintable[$thisrow]['categoryid'] =$row['categoryId'];

        $maintable[$thisrow]['context'] = makeclean($row['cname']);
        $maintable[$thisrow]['timeframe'] = makeclean($row['timeframe']);
        $maintable[$thisrow]['timeframeid'] = $row['timeframeId'];


        $childType=array();
        $childType=getChildType($row['type']);
        if (count($childType)) $maintable[$thisrow]['childtype'] =$childType[0];
        
        if($row['deadline']) {
            $deadline=prettyDueDate($row['deadline'],$config['datemask']);
            $maintable[$thisrow]['deadline'] =$deadline['date'];
            $maintable[$thisrow]['deadline.class']=$deadline['class'];
            $maintable[$thisrow]['deadline.title']=$deadline['title'];
        } else $maintable[$thisrow]['deadline']='';
             
        $maintable[$thisrow]['repeat'] =((($row['repeat'])=="0")?'&nbsp;':($row['repeat']));

        //tickler date - calculate reminder date as # suppress days prior to deadline
        if ($row['suppress']=="y") {
            $reminddate=getTickleDate($row['deadline'],$row['suppressUntil']);
            $maintable[$thisrow]['suppressUntil']=date($config['datemask'],$reminddate);
        } else
            $maintable[$thisrow]['suppressUntil']= '&nbsp;';
                    
        
        $thisrow++;
    } // end of: foreach ($result as $row) if (($filter['nextonly']!="true")
    
    $dispArray=array(
        'parent'=>$parentname
        ,'flags'=>'!'
        ,'NA'=>'NA'
        ,'title'=>$typename.'s'
        ,'description'=>'Description'
        ,'outcome'=>'Desired Outcome'
        ,'category'=>'Category'
        ,'context'=>'Space Context'
        ,'timeframe'=>'Time Context'
        ,'deadline'=>'Deadline'
        ,'repeat'=>'Repeat'
        ,'suppressUntil'=>'Reminder Date'
        ,'dateCreated'=>'Date Created'
        ,'lastModified'=>'Last Modified'
        ,'dateCompleted'=>'Date Completed'
        ,'checkbox'=>'Complete'
        );
    if ($config['debug'] & _GTD_DEBUG) echo '<pre>values to print:',print_r($maintable,true),'</pre>';
} // end of: if($result!="-1")
/*
    ===================================================================
    end of main query: finished building array of items
    ===================================================================
*/

$numrows=count($maintable);
if($numrows) {
    $endmsg='';
    $sectiontitle = $numrows.' '.$sectiontitle;
    if ($numrows!==1) $sectiontitle.='s';
    if($filter['everything']=="true") {
        if ($numrows!==1) $sectiontitle = 'All '.$sectiontitle;
    } elseif ($filter['tickler']=="true") {
        $sectiontitle .= ' in Tickler File';
        $link .='&amp;suppress=true';
    }
}else {
    $endmsg=array('header'=>"You have no {$typename}s remaining.");
    if ($filter['completed']!="true" && $values['type']!="t") {
        $endmsg['prompt']="Create a new {$typename}";
        $endmsg['link']=$link;
    }
}
if ($filter['completed']!="true" || $filter['everything']=="true")
    $sectiontitle = "<a title='Add new $sectiontitle' href='$link'>$sectiontitle</a>";

