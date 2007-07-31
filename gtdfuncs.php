<?php

function makeClean($textIn) {
	$cleaned=htmlspecialchars(stripslashes($textIn),ENT_QUOTES);
	if ($cleaned==='') return '&nbsp;'; else return $cleaned;
}

function trimTaggedString($inStr,$inLength=0,$keepTags=TRUE) { // Ensure the visible part of a string, excluding html tags, is no longer than specified) 	// TOFIX -  we don't handle "%XX" strings yet.
	// constants - might move permittedTags to config file
	$permittedTags=array(
		 array('/^<a ((href)|(file))=[^>]+>/i','</a>')
		,array('/^<b>/i','</b>')
		,array('/^<i>/i','</i>')
		,array('/^<ul>/i','</ul>')
		,array('/^<ol>/i','</ol>')
		,array('/^<li>/i','</li>')
		);
	$ellipsis='&hellip;';
	$ampStrings='/^&[#a-zA-Z0-9]+;/';
	
	// initialise variables
	if ($inLength==0) $inLength=strlen($inStr)+1;
	$outStr='';
	$visibleLength=0;
	$thisChar=0;
	$keepGoing=TRUE;
	$tagsOpen=array();
	// main processing here
	while ($keepGoing) {
		$stillHere = TRUE;
		$tagToClose=end($tagsOpen);
		if ($tagToClose && strtolower(substr($inStr,$thisChar,strlen($tagToClose)))===strtolower($tagToClose) ) {
			$stillHere=FALSE;
			$thisChar+=strlen($tagToClose);
			if ($keepTags) $outStr.=array_pop($tagsOpen);
		} else foreach ($permittedTags as $thisTag) {
			if ($stillHere && ($inStr[$thisChar]==='<') && (preg_match($thisTag[0],substr($inStr,$thisChar),$matches)>0)) {
				$thisChar+=strlen($matches[0]);
				$stillHere=FALSE;
				if ($keepTags) {
					array_push($tagsOpen,$thisTag[1]);
					$outStr.=$matches[0];
				}
			} // end of if
		} // end of else foreach
		// now check for & ... control characters
		if ($stillHere && ($inStr[$thisChar]==='&') && (preg_match($ampStrings,substr($inStr,$thisChar),$matches)>0)) {
			if (strlen(html_entity_decode($matches[0]))==1) {
				$visibleLength++;
				$outStr.=$matches[0];
				$thisChar+=strlen($matches[0]);
				$stillHere=FALSE;
			}
		}
		// just a normal character, so add it to the string
		if ($stillHere) {
			$visibleLength++;
			$outStr.=$inStr[$thisChar];
			$thisChar++;
		} // end of if
		$keepGoing= (($thisChar<strlen($inStr)) && ($visibleLength<$inLength));
	} // end of while ($keepGoing)
	// add ellipsis if we have trimmed some text
	if ($thisChar<strlen($inStr) && $visibleLength>=$inLength) $outStr.=$ellipsis;
	// got the string - now close any open tags
	if ($keepTags) while (count($tagsOpen))
		$outStr.=array_pop($tagsOpen);
	$outStr=nl2br(escapeChars($outStr));
	return($outStr);
}

function getTickleDate($deadline,$days) { // returns unix timestamp of date when tickle becomes active
	$dm=(int)substr($deadline,5,2);
	$dd=(int)substr($deadline,8,2);
	$dy=(int)substr($deadline,0,4);
	// relies on PHP to sanely and clevery handle dates like "the -5th of March" or "the 50th of April"
	$remind=mktime(0,0,0,$dm,($dd-$days),$dy);
	return $remind;
}

function nothingFound($message, $prompt=NULL, $yeslink=NULL, $nolink="index.php"){
        //Give user ability to create a new entry, or go back to the index.
        echo "<h4>$message</h4>";
        if($prompt)
            echo "<p>$prompt;<a href='$yeslink'> Yes </a><a href='$nolink'>No</a></p>\n";
}

function sqlparts($part,$config,$values)  {
    //include correct SQL parts query library as chosen in config
    switch ($config['dbtype']) {
        case "frontbase":require("frontbaseparts.inc.php");
        break;
        case "msql":require("msqlparts.inc.php");
        break;
        case "mysql":
   			require_once("mysql.funcs.inc.php");
			foreach ($values as $key=>$value) $values[$key] = safeIntoDB($value, $key);
		    if ($config['debug'] & _GTD_DEBUG)
		        echo '<pre>Sanitised values in sqlparts: ',print_r($values,true),'</pre>';
			require("mysqlparts.inc.php");
        	break;
        case "mssql":require("mssqlparts.inc.php");
        break;
        case "postgres":require("postgresparts.inc.php");
        break;
        case "sqlite":require("sqliteparts.inc.php");
        break;
        }
    $queryfragment = $sqlparts[$part];
    return $queryfragment;
    }

function categoryselectbox($config,$values,$options,$sort) {
    $result = query("categoryselectbox",$config,$values,$options,$sort);
    $cashtml='<option value="0">--</option>'."\n";
    if ($result!="-1") {
        foreach($result as $row) {
            $cashtml .= '   <option value="'.$row['categoryId'].'" title="'.makeclean($row['description']).'"';
            if($row['categoryId']==$values['categoryId']) $cashtml .= ' selected="selected"';
            $cashtml .= '>'.makeclean($row['category'])."</option>\n";
            }
        }
    return $cashtml;
    }

function contextselectbox($config,$values,$options,$sort) {
    $result = query("spacecontextselectbox",$config,$values,$options,$sort);
    $cshtml='<option value="0">--</option>'."\n";
    if ($result!="-1") {
            foreach($result as $row) {
            $cshtml .= '                    <option value="'.$row['contextId'].'" title="'.makeclean($row['description']).'"';
            if($row['contextId']==$values['contextId']) $cshtml .= ' selected="selected"';
            $cshtml .= '>'.makeclean($row['name'])."</option>\n";
            }
        }
    return $cshtml;
    }

function timecontextselectbox($config,$values,$options,$sort) {
    $result = query("timecontextselectbox",$config,$values,$options,$sort);
    $tshtml='<option value="0">--</option>'."\n";
    if ($result!="-1") {
        foreach($result as $row) {
            $tshtml .= '                    <option value="'.$row['timeframeId'].'" title="'.makeclean($row['description']).'"';
            if($row['timeframeId']==$values['timeframeId']) $tshtml .= ' selected="selected"';
            $tshtml .= '>'.makeclean($row['timeframe'])."</option>\n";
            }
        }
    return $tshtml;
    }

function makeOption($row,$selected) {
    $cleandesc=makeclean($row['description']);
    $cleantitle=makeclean($row['title']);
    if ($row['isSomeday']==="y") {
        $cleandesc.=' (Someday)';
        $cleantitle.=' (S)';
    }
    $seltext = ($selected[$row['itemId']])?' selected="selected"':'';
    $out = "<option value='{$row['itemId']}' title='$cleandesc' $seltext>$cleantitle</option>";
    return $out;
}

function parentselectbox($config,$values,$options,$sort) {
    $result = query("parentselectbox",$config,$values,$options,$sort);
    $pshtml='';
    $parents=array();
    if (is_array($values['parentId']))
        foreach ($values['parentId'] as $key) $parents[$key]=true;
    else
        $parents[$values['parentId']]=true;
    if ($config['debug'] & _GTD_DEBUG) echo '<pre>parents:',print_r($parents,true),'</pre>';
    if ($result!="-1")
        foreach($result as $row) {
            $thisOpt= makeOption($row,$parents)."\n";
            if($parents[$row['itemId']]) {
                $pshtml =$thisOpt.$pshtml;
                $parents[$row['itemId']]=false;
            } else
                $pshtml .=$thisOpt;
        }
    foreach ($parents as $key=>$val) if ($val) {
        // $key is a parentId which wasn't found for the drop-down box, so need to add it in
        $values['itemId']=$key;
        $row=query('selectitemshort',$config,$values,$options,$sort);
        if ($row!='-1') $pshtml = makeOption($row[0],$parents)."\n".$pshtml;
    }
    $pshtml="<option value='0'>--</option>\n".$pshtml;
    return $pshtml;
}

function checklistselectbox($config,$values,$options,$sort) {
    $result = query("checklistselectbox",$config,$values,$options,$sort);
    $cshtml='<option value="0">--</option>'."\n";
    if ($result!="-1") {
        foreach($result as $row) {
            $cshtml .= '                    <option value="'.$row['checklistId'].'" title="'.makeclean($row['description']).'"';
            if($row['checklistId']==$values['checklistId']) $cshtml .= ' selected="selected"';
            $cshtml .= '>'.makeclean($row['title'])."</option>\n";
            }
        }
    return $cshtml;
    }

function listselectbox($config,$values,$options,$sort) {
    $result = query("listselectbox",$config,$values,$options,$sort);
    $lshtml='<option value="0">--</option>'."\n";
    if ($result!="-1") {
        foreach($result as $row) {
            $lshtml .= '                    <option value="'.$row['listId'].'" title="'.makeclean($row['description']).'"';
            if($row['listId']==$values['listId']) $lshtml .= ' selected="selected"';
            $lshtml .= '>'.makeclean($row['title'])."</option>\n";
            }
        }
    return $lshtml;
    }

function prettyDueDate($dateToShow,$thismask) {
	$retval=array();
    if(trim($dateToShow)!='') {
        $retval['date'] = date($thismask,strtotime($dateToShow) );
        if ($dateToShow<date("Y-m-d")) {
            $retval['class']='overdue';
            $retval['title']='Overdue';
        } elseif($dateToShow===date("Y-m-d")) {
            $retval['class']='due';
            $retval['title']='Due today';
        }
    } else
        $retval['date'] ='&nbsp;';
	return $retval;
}

function getVarFromGetPost($varName,$default='') {
	$retval=(isset($_GET[$varName]))?$_GET[$varName]:( (isset($_POST[$varName]))?$_POST[$varName]:$default );
	return $retval;
}

function getNextActionsArray($config,$values,$options,$sort) {
	$result= query("getnextactions",$config,$values,$options,$sort);
	$nextactions=array();
	if(is_array($result))foreach ($result as $row) $nextactions[(int) $row['nextaction']]=true;
	return $nextactions;
}

function nextScreen($url) {
    global $config;
    $cleanurl=htmlspecialchars($url);
    if ($config['debug']) {
        echo "<p>Next screen is <a href='$cleanurl'>$cleanurl</a> - would be auto-refresh in non-debug mode</p>";
    }elseif (headers_sent()) {
        echo "<META HTTP-EQUIV='Refresh' CONTENT='0;url=$cleanurl' />\n"
            ,"<script type='text/javascript'>window.location.replace('$cleanurl');</script>\n"
            ,"</head><body><a href='$cleanurl'>Click here to continue on to $cleanurl</a>\n";
    }else{
        $header="Location: http"
                .(($_SERVER['HTTPS']!='')?'s':'')
                ."://"
                .$_SERVER['HTTP_HOST']
                .rtrim(dirname($_SERVER['PHP_SELF']), '/\\')
                .'/'.$url;
        header($header);
        exit;
    }
}

function getChildType($parentType) {
$childtype=array();
switch ($parentType) {
    case "m" : $childtype=array("v"); break;
    case "v" : $childtype=array("o"); break;
    case "o" : $childtype=array("g"); break;
    case "g" : $childtype=array("p","s"); break;
    case "p" : $childtype=array("a","w","r","p","s"); break;
    case "s" : $childtype=array("a","w","r","s"); break;
    case "a" : $childtype=NULL; break;
    case "w" : $childtype=NULL; break;
    case "r" : $childtype=NULL; break;
    case "i" : $childtype=NULL; break;
    default  : $childtype=NULL;
    }
return $childtype;
}

function getTypes($type=false) {
$types=array("m" => "Value",
            "v" => "Vision",
            "o" => "Role",
            "g" => "Goal",
            "p" => "Project",
            "a" => "Action",
            "i" => "Inbox Item",
            "s" => "Someday/Maybe",
            "r" => "Reference",
            "w" => "Waiting On"
        );
if ($type===false)
    return $types;
else
    return $types[$type];
}

function escapeChars($str) {
    $outStr=str_replace(array('&','�'),array('&amp;','&hellip'),$str);
    $outStr=str_replace(array('&amp;amp;','&amp;hellip;'),array('&amp;','&hellip;'),$outStr);
	return $outStr;
}

function getShow($where,$type) {
    global $config;
    $show=array(
        'title'         => true,
        'description'   => true,

        // only show if editing, not creating
        'lastModified'  =>($where==='edit'),
        'dateCreated'   =>($where==='edit'),
        'type'          =>($where==='edit' && $type==='i'),

        // fields suppressed on certain types
        'desiredOutcome'=>($type!=='r'),
        'category'      =>($type!=='m'),
        'ptitle'        =>($type!=='m' && $type!=='i'),
        'dateCompleted' =>($type!=='m'),
        'complete'      =>($type!=='m' && $type!=='r'),
        'timeframe'     =>($type!=='m' && $type!=='w' && $type!=='r' && $type!=='i'),
        'context'       =>($type!=='m' && $type!=='v' && $type!=='o' && $type!=='g'),

        // fields only shown for certain types
        'deadline'      =>($type==='p' || $type==='a' || $type==='w' || $type==='i'),
        'suppress'      =>($type==='p' || $type==='a' || $type==='w'),
        'suppressUntil' =>($type==='p' || $type==='a' || $type==='w'),
        'repeat'        =>($type==='p' || $type==='a'),
        'NA'            =>($type==='a' || $type==='w'),
        'isSomeday'     =>($type==='p'),

        // fields never shown on item.php
        'checkbox'      => false,
        'flags'         => false
        );

    if ($config['forceAllFields'])
        foreach ($show as $key=>$value)
            $show[$key]=true;
                
    return $show;
}
// php closing tag has been omitted deliberately, to avoid unwanted blank lines being sent to the browser
