<?php

//query listing for MySQL database
//API Documentation available in __________
//NB: All queries written to operate on single table for compatibility with other databases
//MySQL extensions to standard SQL have been avoided where known
//Queries may be rewritten in future as SQL evolves and as other database types are supported

//GENERAL RULES:
//"select" = query for something by its id; a single-row result
//"get" = query for something of a particular type; a multi-row result
//"new", "update", "delete" are self-explanatory
//"check"="complete" for checklistselectbox
//"complete" = set status to completed
//"remove" = remove by association Id (items associated with a project, etc)
//"Count" = # of a particular type in table
//"selectbox" = get results to create a selectbox- for assignment or filter

$sql = array(
        "categoryselectbox"         => 	"SELECT c.`categoryId`, c.`category`, c.`description` 
        								FROM `". $config['prefix'] ."categories` as c 
        								ORDER BY {$sort['categoryselectbox']}",
        
        "checkchecklistitem"        => 	"UPDATE `". $config['prefix'] ."checklistitems` 
        								SET `checked` = 'y' 
        								WHERE `checklistItemId`='{$values['Cli']}'",

        "checklistselectbox"        => "SELECT cl.`checklistId`, cl.`title`, 
        										cl.`description`, cl.`categoryId`, c.`category` 
        								FROM `". $config['prefix'] ."checklist` as cl
        								LEFT OUTER JOIN `{$config['prefix']}categories` as c USING (`categoryId`)
        								ORDER BY {$sort['checklistselectbox']}",

        "clearchecklist"            => "UPDATE `". $config['prefix'] ."checklistitems` 
										SET `checked` = 'n' 
										WHERE `checklistId` = '{$values['checklistId']}'",
        
        "completeitem"              => "UPDATE `". $config['prefix'] ."itemstatus` 
										SET `dateCompleted`=" . $values['dateCompleted'].
                                        ", `lastModified` = NULL
										WHERE `itemId`=" . $values['itemId'],

        "completelistitem"          => "UPDATE `". $config['prefix'] ."listitems` 
										SET `dateCompleted`='{$values['date']}' 
										WHERE `listItemId`='{$values['completedLi']}'",

        "copynextaction"            => "INSERT INTO `". $config['prefix'] ."nextactions` (`parentId`,`nextaction`) 
										VALUES ('{$values['parentId']}','{$values['newitemId']}') 
										ON DUPLICATE KEY UPDATE `nextaction`='{$values['newitemId']}'",
        
        "countchildren"             => "SELECT il.`itemId`
										FROM `". $config['prefix'] ."lookup` as il, 
												`". $config['prefix'] ."itemstatus` as its 
										WHERE il.`itemId`=its.`itemId` 
										    AND il.`parentId`=". $values['parentId'] . " 
											AND its.`dateCompleted` IS NULL",

        "countitems"                => "SELECT COUNT(*)
										FROM `". $config['prefix'] ."itemattributes` as ia, 
												`". $config['prefix'] ."itemstatus` as its 
										WHERE ia.`itemId`=its.`itemId` ".$values['filterquery'],
        
        "countnextactions"          => "SELECT COUNT(DISTINCT `nextaction`) AS nnextactions 
										FROM `". $config['prefix'] ."nextactions` as na
											JOIN `". $config['prefix'] . "itemattributes` as ia 
												ON (ia.`itemId` = na.`nextaction`)
											JOIN `". $config['prefix'] . "itemstatus` as its
												ON (ia.`itemId` = its.`itemId`) ".									
										$values['filterquery'],
										
        "countcontextreport_naonly" => "SELECT ia.`contextId`, ia.`timeframeId`, 
										COUNT(*) AS count 
										FROM `". $config['prefix'] ."itemattributes` as ia, 
												`". $config['prefix'] ."itemstatus` as its, 
												`". $config['prefix'] ."nextactions` as na 
										WHERE its.`itemId`=ia.`itemId` 
											AND  na.`nextaction` = its.`itemId`
											AND ia.`isSomeday`='n' 
											AND (its.`dateCompleted` IS NULL) 
											AND {$values['filterquery']}
										GROUP BY ia.`contextId`, ia.`timeframeId`",
        
        "countcontextreport_all"    => "SELECT ia.`contextId`, ia.`timeframeId`, 
												COUNT(*) AS count 
										FROM `". $config['prefix'] ."itemattributes` as ia, 
												`". $config['prefix'] ."itemstatus` as its 
										WHERE its.`itemId`=ia.`itemId` 
											AND ia.`type`='a' 
											AND ia.`isSomeday`='n' 
											AND (its.`dateCompleted` IS NULL)
                                            AND {$values['filterquery']}
										GROUP BY ia.`contextId`, ia.`timeframeId`",

		"countselected"				=> "SELECT FOUND_ROWS()",

        "countspacecontexts"        => "SELECT COUNT(*)
        								FROM `". $config['prefix'] ."context`",

        "deletecategory"            => "DELETE FROM `". $config['prefix'] ."categories` 
        								WHERE `categoryId`='{$values['id']}'",
        "deletechecklist"           => "DELETE FROM `". $config['prefix'] ."checklist` 
        								WHERE `checklistId`='{$values['checklistId']}'",
        "deletechecklistitem"       => "DELETE FROM `". $config['prefix'] ."checklistitems` 
        								WHERE `checklistItemId`='{$values['checklistItemId']}'",
        "deleteitem"                => "DELETE FROM `". $config['prefix'] ."items` 
        								WHERE `itemId`='{$values['itemId']}'",
        "deleteitemattributes"      => "DELETE FROM `". $config['prefix'] ."itemattributes` 
        								WHERE `itemId`='{$values['itemId']}'",
        "deleteitemstatus"          => "DELETE FROM `". $config['prefix'] ."itemstatus` 
        								WHERE `itemId`='{$values['itemId']}'",
        "deletelist"                => "DELETE FROM `". $config['prefix'] ."list` 
        								WHERE `listId`='{$values['listId']}'",
        "deletelistitem"            => "DELETE FROM `". $config['prefix'] ."listitems` 
        								WHERE `listItemId`='{$values['listItemId']}'",
        "deletelookup"              => "DELETE FROM `". $config['prefix'] ."lookup` 
        								WHERE `itemId` ='{$values['itemId']}'",
        "deletelookupparents"       => "DELETE FROM `". $config['prefix'] ."lookup` 
        								WHERE `parentId` ='{$values['itemId']}'",
        "deletenextaction"          => "DELETE FROM `". $config['prefix'] ."nextactions` 
        								WHERE `nextAction`='{$values['itemId']}'",
        "deletenextactionparents"   => "DELETE FROM `". $config['prefix'] ."nextactions` 
        								WHERE `parentId` ='{$values['itemId']}'",
        "deletenote"                => "DELETE FROM `". $config['prefix'] ."tickler` 
        								WHERE `ticklerId`='{$values['noteId']}'",
        "deletespacecontext"        => "DELETE FROM `". $config['prefix'] ."context` 
        								WHERE `contextId`='{$values['id']}'",
        "deletetimecontext"         => "DELETE FROM `". $config['prefix'] ."timeitems` 
        								WHERE `timeframeId`='{$values['id']}'",
        
        
        "getchecklistitems"         => "SELECT cli.`checklistitemId`, cli.`item`, cli.`notes`, 
        										cli.`checklistId`, cli.`checked` 
        								FROM `". $config['prefix'] . "checklistitems` as cli 
        									LEFT JOIN `". $config['prefix'] ."checklist` as cl 
        										ON cli.`checklistId` = cl.`checklistId` 
										WHERE cl.`checklistId` = '{$values['checklistId']}' 
										ORDER BY {$sort['getchecklistitems']}",
        
		"getchecklists"			    => 	"SELECT cl.`checklistId`, cl.`title`, 
												cl.`description`, cl.`categoryId`, c.`category` 
										FROM `". $config['prefix'] ."checklist` as cl
										LEFT OUTER JOIN `{$config['prefix']}categories` as c USING (`categoryId`) "
										.$values['filterquery']." ORDER BY {$sort['getchecklists']}",
			
        "getchildren"               => 	"SELECT "
                                        .(($values['limitfilterquery']=='')?'':' SQL_CALC_FOUND_ROWS ').
                                        "i.`itemId`, i.`title`, i.`description`,
        									i.`desiredOutcome`, ia.`type`, 
        									ia.`isSomeday`, ia.`deadline`, ia.`repeat`, 
        									ia.`suppress`, ia.`suppressUntil`, 
        									its.`dateCreated`, its.`dateCompleted`, 
        									its.`lastModified`, ia.`categoryId`,
        									c.`category`, ia.`contextId`, 
        									cn.`name` AS cname, ia.`timeframeId`, ti.`timeframe`
                                            , na.nextaction as NA
										FROM `". $config['prefix'] . "itemattributes` as ia
											JOIN `{$config['prefix']}lookup` AS lu USING (`itemId`)
											JOIN `". $config['prefix'] . "items` AS i USING (`itemId`)
											JOIN `". $config['prefix'] . "itemstatus` AS its USING (`itemId`)
											LEFT OUTER JOIN `". $config['prefix'] . "context` AS cn
												ON (ia.`contextId` = cn.`contextId`) 
											LEFT OUTER JOIN `". $config['prefix'] ."categories` AS c
												ON (ia.`categoryId` = c.`categoryId`) 
											LEFT OUTER JOIN `". $config['prefix'] . "timeitems` AS ti
												ON (ia.`timeframeId` = ti.`timeframeId`)
										LEFT JOIN (
                                                SELECT DISTINCT nextaction FROM {$config['prefix']}nextactions
                                            ) AS na ON(na.nextaction=i.itemId)
										WHERE lu.`parentId`= '{$values['parentId']}' {$values['filterquery']}
										ORDER BY {$sort['getchildren']} {$values['limitfilterquery']}",

        "getgtdphpversion"         =>  "SELECT `version` FROM `{$config['prefix']}version`",
        
        "getitems"                  => 	"SELECT i.`itemId`, i.`title`, i.`description` 
        								FROM `". $config['prefix'] . "itemattributes` as ia 
											JOIN `". $config['prefix'] . "items` as i 
												ON (ia.`itemId` = i.`itemId`) 
											JOIN `". $config['prefix'] . "itemstatus` as its 
												ON (ia.`itemId` = its.`itemId`) 
											LEFT OUTER JOIN `". $config['prefix'] . "context` as cn 
												ON (ia.`contextId` = cn.`contextId`) 
											LEFT OUTER JOIN `". $config['prefix'] ."categories` as c 
												ON (ia.`categoryId` = c.`categoryId`) 
											LEFT OUTER JOIN `". $config['prefix'] . "timeitems` as ti 
												ON (ia.`timeframeId` = ti.`timeframeId`) ".$values['filterquery']." 
        								ORDER BY {$sort['getitems']}",
        
        "getitemsandparent"         => "SELECT 
        										x.`itemId`, x.`title`, x.`description`, 
        										x.`desiredOutcome`, x.`type`, x.`isSomeday`, 
        										x.`deadline`, x.`repeat`, x.`suppress`, 
        										x.`suppressUntil`, x.`dateCreated`, x.`dateCompleted`, 
        										x.`lastModified`, x.`categoryId`, x.`category`,
        										x.`contextId`, x.`cname`, x.`timeframeId`, 
        										x.`timeframe`,
                                                GROUP_CONCAT(DISTINCT y.`parentId` ORDER BY y.`ptitle`) as `parentId`,
                                                GROUP_CONCAT(DISTINCT y.`ptitle` ORDER BY y.`ptitle` SEPARATOR '{$config['separator']}') AS `ptitle`
                                                {$values['extravarsfilterquery']}
										FROM (
												SELECT 
														i.`itemId`, i.`title`, i.`description`, 
														i.`desiredOutcome`, ia.`type`, ia.`isSomeday`, 
														ia.`deadline`, ia.`repeat`, ia.`suppress`, 
														ia.`suppressUntil`, its.`dateCreated`, 
														its.`dateCompleted`, its.`lastModified`,
														ia.`categoryId`, c.`category`, ia.`contextId`, 
														cn.`name` AS cname, ia.`timeframeId`, 
														ti.`timeframe`, lu.`parentId` 
												FROM 
														`". $config['prefix'] . "itemattributes` as ia 
													JOIN `". $config['prefix'] . "items` as i
														ON (ia.`itemId` = i.`itemId`) 
													JOIN `". $config['prefix'] . "itemstatus` as its
														ON (ia.`itemId` = its.`itemId`)
													LEFT OUTER JOIN `". $config['prefix'] . "context` as cn
														ON (ia.`contextId` = cn.`contextId`) 
													LEFT OUTER JOIN `". $config['prefix'] ."categories` as c
														ON (ia.`categoryId` = c.`categoryId`)
													LEFT OUTER JOIN `". $config['prefix'] . "timeitems` as ti
														ON (ia.`timeframeId` = ti.`timeframeId`)
													LEFT OUTER JOIN `". $config['prefix'] . "lookup` as lu 
														ON (ia.`itemId` = lu.`itemId`)".$values['childfilterquery']."
										) as x 
											LEFT OUTER JOIN 
											(
												SELECT 
														i.`itemId` AS parentId, i.`title` AS ptitle, 
														i.`description` AS pdescription, 
														i.`desiredOutcome` AS pdesiredOutcome, 
														ia.`type` AS ptype, ia.`isSomeday` AS pisSomeday, 
														ia.`deadline` AS pdeadline, ia.`repeat` AS prepeat, 
														ia.`suppress` AS psuppress, 
														ia.`suppressUntil` AS psuppressUntil,  
														its.`dateCompleted` AS pdateCompleted
												FROM 
														`". $config['prefix'] . "itemattributes` as ia 
													JOIN `". $config['prefix'] . "items` as i
														ON (ia.`itemId` = i.`itemId`)
													JOIN `". $config['prefix'] . "itemstatus` as its
														ON (ia.`itemId` = its.`itemId`)
										        {$values['parentfilterquery']}
                                            ) as y ON (y.parentId = x.parentId)
                                        {$values['filterquery']} GROUP BY x.`itemId`
                                        ORDER BY {$sort['getitemsandparent']}",


        "getitembrief"              => 	"SELECT `title`, `description`
        								FROM  `". $config['prefix'] . "items`
										WHERE `itemId` = {$values['itemId']}",
												
        "getlistitems"              => "SELECT li.`listItemId`, li.`item`, li.`notes`, li.`listId` 
        								FROM `". $config['prefix'] . "listitems` as li 
        									LEFT JOIN `". $config['prefix'] . "list` as l
        										on li.`listId` = l.`listId`
										WHERE l.`listId` = '{$values['listId']}' ".$values['filterquery']." 
										ORDER BY {$sort['getlistitems']}",
        
        "getlists"                  => "SELECT l.`listId`, l.`title`, l.`description`, l.`categoryId`, c.`category` 
        								FROM `". $config['prefix'] . "list` as l
                                        LEFT OUTER JOIN `{$config['prefix']}categories` as c USING (`categoryId`) "
                                        .$values['filterquery']." ORDER BY {$sort['getlists']}",
        
        "getnotes"                  => "SELECT `ticklerId`, `title`, `note`, `date` 
        								FROM `". $config['prefix'] . "tickler`  as tk".$values['filterquery']."
        								ORDER BY {$sort['getnotes']}",
        								
		"getorphaneditems"	  		=> "SELECT ia.`itemId`, ia.`type`, i.`title`, i.`description`, ia.`isSomeday`
										FROM `{$config['prefix']}itemattributes` AS ia
										JOIN `{$config['prefix']}items`          AS i   USING (itemId)
										JOIN `{$config['prefix']}itemstatus`     AS its USING (itemId)
										WHERE (its.`dateCompleted` IS NULL)
											AND ia.`type` NOT IN ({$values['notOrphansfilterquery']})
											AND ia.`itemId` NOT IN 
												(SELECT lu.`itemId` FROM `". $config['prefix'] . "lookup` as lu)
										ORDER BY {$sort['getorphaneditems']}",

        "getspacecontexts"          => "SELECT `contextId`, `name`
										FROM `". $config['prefix'] . "context`",

        "gettimecontexts"           => "SELECT `timeframeId`, `timeframe`, `description`
										FROM `". $config['prefix'] . "timeitems` AS ti
                                        {$values['timefilterquery']}",
        
        
        "listselectbox"             => "SELECT l.`listId`, l.`title`, l.`description`, 
        										l.`categoryId`, c.`category`
										FROM `". $config['prefix'] . "list` as l
        								LEFT OUTER JOIN `{$config['prefix']}categories` as c USING (`categoryId`)
										ORDER BY {$sort['listselectbox']}",

        "lookupparent"              => "SELECT `parentId`,`title` AS `ptitle`,`type` AS `ptype`
										FROM `". $config['prefix'] . "lookup` AS lu
                                        JOIN `{$config['prefix']}items` AS i ON (lu.`parentId` = i.`itemId`)
                                        JOIN `{$config['prefix']}itemattributes` AS ia ON (lu.`parentId` = ia.`itemId`)
										WHERE lu.`itemId`='{$values['itemId']}'",

        "newcategory"               => "INSERT INTO `". $config['prefix'] ."categories`
										VALUES (NULL, '{$values['name']}', '{$values['description']}')",
										
        "newchecklist"              => "INSERT INTO `". $config['prefix'] ."checklist`
										VALUES (NULL, '{$values['title']}', 
												'{$values['categoryId']}', '{$values['description']}')",
										
        "newchecklistitem"          => "INSERT INTO `". $config['prefix'] . "checklistitems` 
										VALUES (NULL, '{$values['item']}', 
												'{$values['notes']}', '{$values['checklistId']}', 'n')",

        "newitem"                   => "INSERT INTO `". $config['prefix'] . "items` 
        										(`title`,`description`,`desiredOutcome`)
										VALUES ('{$values['title']}',
												'{$values['description']}','{$values['desiredOutcome']}')",

        "newitemattributes"         => "INSERT INTO `". $config['prefix'] . "itemattributes` 
        										(`itemId`,`type`,`isSomeday`,`categoryId`,`contextId`,
												`timeframeId`,`deadline`,`repeat`,`suppress`,`suppressUntil`)
										VALUES ('{$values['newitemId']}','{$values['type']}','{$values['isSomeday']}',
												'{$values['categoryId']}','{$values['contextId']}','{$values['timeframeId']}',
												{$values['deadline']},'{$values['repeat']}','{$values['suppress']}',
												'{$values['suppressUntil']}')",

        "newitemstatus"             => "INSERT INTO `". $config['prefix'] . "itemstatus` 
        										(`itemId`,`dateCreated`,`lastModified`,`dateCompleted`)
										VALUES ('{$values['newitemId']}',
												CURRENT_DATE,NULL,{$values['dateCompleted']})",
										
        "newlist"                   => "INSERT INTO `". $config['prefix'] . "list`
										VALUES (NULL, '{$values['title']}', 
												'{$values['categoryId']}', '{$values['description']}')",

        "newlistitem"               => "INSERT INTO `". $config['prefix'] . "listitems`
										VALUES (NULL, '{$values['item']}', 
												'{$values['notes']}', '{$values['listId']}', NULL)",

        "newnextaction"             => "INSERT INTO `". $config['prefix'] . "nextactions` 
        										(`parentId`,`nextaction`)
										VALUES ('{$values['parentId']}','{$values['newitemId']}')
										ON DUPLICATE KEY UPDATE `nextaction`='{$values['newitemId']}'",

        "newnote"                   => "INSERT INTO `". $config['prefix'] . "tickler` 
        										(`date`,`title`,`note`,`repeat`,`suppressUntil`)
										VALUES ('{$values['date']}','{$values['title']}',
												'{$values['note']}','{$values['repeat']}',
												'{$values['suppressUntil']}')",

        "newparent"                 => "INSERT INTO `". $config['prefix'] . "lookup` 
        										(`parentId`,`itemId`)
										VALUES ('{$values['parentId']}','{$values['newitemId']}')",

        "newspacecontext"           => "INSERT INTO `". $config['prefix'] . "context`  
        										(`name`,`description`)
										VALUES ('{$values['name']}', '{$values['description']}')",

        "newtimecontext"            => "INSERT INTO `". $config['prefix'] . "timeitems` 
        										(`timeframe`,`description`,`type`)
										VALUES ('{$values['name']}', '{$values['description']}', '{$values['type']}')",

        "parentselectbox"           => "SELECT i.`itemId`, i.`title`, 
												i.`description`, ia.`isSomeday`,ia.`type`
										FROM `". $config['prefix'] . "items` as i
										JOIN `{$config['prefix']}itemattributes` as ia USING (`itemId`)
										JOIN `{$config['prefix']}itemstatus` as its USING (`itemId`)
										WHERE (its.`dateCompleted` IS NULL) {$values['ptypefilterquery']}
										ORDER BY ia.`type`,i.`title`",
										#ORDER BY {$sort['parentselectbox']}",


        "reassigncategory"          => "UPDATE `". $config['prefix'] . "itemattributes`
										SET `categoryId`='{$values['newId']}'
										WHERE `categoryId`='{$values['id']}'",

        "reassignspacecontext"      => "UPDATE `". $config['prefix'] . "itemattributes`
										SET `contextId`='{$values['newId']}'
										WHERE `contextId`='{$values['id']}'",

        "reassigntimecontext"       => "UPDATE `". $config['prefix'] . "itemattributes`
										SET `timeframeId`='{$values['newId']}'
										WHERE `timeframeId`='{$values['id']}'",


        "removechecklistitems"      => "DELETE
										FROM `". $config['prefix'] . "checklistitems` 
										WHERE `checklistId`='{$values['checklistId']}'",

        "removelistitems"           => "DELETE
										FROM `". $config['prefix'] . "listitems` 
										WHERE `listId`='{$values['listId']}'",

        "repeatnote"                => "UPDATE `". $config['prefix'] . "tickler`
										SET `date` = DATE_ADD(`date`, INTERVAL ".$values['repeat']." DAY), 
											`note` = '{$values['note']}', `title` = '{$values['title']}', 
											`repeat` = '{$values['repeat']}', 
											`suppressUntil` = '{$values['suppressUntil']}' 
										WHERE `ticklerId` = '{$values['noteId']}'",

        "selectcategory"            => "SELECT `categoryId`, `category`, `description`
										FROM `". $config['prefix'] ."categories`
										WHERE `categoryId` = '{$values['categoryId']}'",

        "selectchecklist"           => "SELECT cl.`checklistId`, cl.`title`, 
        										cl.`description`, cl.`categoryId`, c.`category`
										FROM `". $config['prefix'] ."checklist` as cl, 
												`". $config['prefix'] ."categories` as c 
										WHERE cl.`categoryId`=c.`categoryId`
											AND cl.`checklistId`='{$values['checklistId']}' ".$values['filterquery']."
										ORDER BY {$sort['selectchecklist']}",

        "selectchecklistitem"       => "SELECT `checklistItemId`, 
												`item`, 
												`notes`, 
												`checklistId`, 
												`checked`
										FROM `". $config['prefix'] . "checklistitems`
										WHERE `checklistItemId` = '{$values['checklistItemId']}'",

        "selectcontext"             => "SELECT `contextId`, `name`, `description`
										FROM `". $config['prefix'] . "context` 
										WHERE `contextId` = '{$values['contextId']}'",

        "selectitem"                => "SELECT i.`itemId`, ia.`type`, i.`title`, 
        										i.`description`, i.`desiredOutcome`, 
        										ia.`categoryId`, ia.`contextId`, 
        										ia.`timeframeId`, ia.`isSomeday`, 
        										ia.`deadline`, ia.`repeat`, 
        										ia.`suppress`, ia.`suppressUntil`, 
        										its.`dateCreated`, its.`dateCompleted`, 
        										its.`lastModified`, c.`category`, ti.`timeframe`, 
        										cn.`name` AS `cname`
										FROM (`". $config['prefix'] . "items` as i,
												 `". $config['prefix'] . "itemattributes` as ia, 
												 `". $config['prefix'] . "itemstatus` as its)
											LEFT OUTER JOIN `". $config['prefix'] ."categories` as c
												ON (c.`categoryId` = ia.`categoryId`)
											LEFT OUTER JOIN `". $config['prefix'] . "context` as cn
												ON (cn.`contextId` = ia.`contextId`)
											LEFT OUTER JOIN `". $config['prefix'] . "timeitems` as ti
												ON (ti.`timeframeId` = ia.`timeframeId`)
										WHERE its.`itemId`=i.`itemId`
											AND ia.`itemId`=i.`itemId`
											AND i.`itemId` = '{$values['itemId']}'",

        "selectitemshort"           => "SELECT i.`itemId`, i.`title`,
												i.`description`, ia.`isSomeday`,ia.`type`
										FROM `". $config['prefix'] . "items` as i
                                        JOIN `{$config['prefix']}itemattributes` AS ia USING (`itemId`)
										JOIN `{$config['prefix']}itemstatus` AS its USING (`itemId`)
										WHERE i.`itemId` = '{$values['itemId']}'",

        "selectlist"                => "SELECT `listId`, `title`, `description`, `categoryId`
										FROM `". $config['prefix'] . "list` 
										WHERE `listId` = '{$values['listId']}'",

        "selectlistitem"            => "SELECT `listItemId`, `item`, 
        										`notes`, `listId`, `dateCompleted`
										FROM `". $config['prefix'] . "listitems` 
										WHERE `listItemId` = {$values['listItemId']}",

        "selectnote"                => "SELECT `ticklerId`, `title`, `note`, 
        										`date`, `repeat`, `suppressUntil`
										FROM `". $config['prefix'] . "tickler` 
										WHERE `ticklerId` = '{$values['noteId']}'",

        "selecttimecontext"         => "SELECT `timeframeId`, `timeframe`, `description`, `type`
										FROM `". $config['prefix'] . "timeitems` 
										WHERE `timeframeId` = '{$values['tcId']}'",

        "spacecontextselectbox"     => "SELECT `contextId`, `name`, `description`
										FROM `". $config['prefix'] . "context` as cn
										ORDER BY {$sort['spacecontextselectbox']}",

        "testitemrepeat"            => "SELECT ia.`repeat`
										FROM `". $config['prefix'] . "itemattributes` as ia
										WHERE ia.`itemId`='{$values['itemId']}'",

        "testnextaction"            => "SELECT `parentId`, `nextaction`
										FROM `". $config['prefix'] . "nextactions` 
										WHERE `nextaction`='{$values['itemId']}'",

        "timecontextselectbox"      => "SELECT `timeframeId`, `timeframe`, `description`, `type`
										FROM `". $config['prefix'] . "timeitems` as ti".$values['timefilterquery']."
										ORDER BY {$sort['timecontextselectbox']}",

        "touchitem"                 => "UPDATE `". $config['prefix'] . "itemstatus`
										SET `lastModified` = NULL
										WHERE `itemId` = '{$values['itemId']}'",

        "updatecategory"            => "UPDATE `". $config['prefix'] ."categories`
										SET `category` ='{$values['name']}',
												`description` ='{$values['description']}' 
										WHERE `categoryId` ='{$values['id']}'",

        "updatechecklist"           => "UPDATE `". $config['prefix'] ."checklist`
										SET `title` = '{$values['newchecklistTitle']}', 
												`description` = '{$values['newdescription']}', 
												`categoryId` = '{$values['newcategoryId']}' 
										WHERE `checklistId` ='{$values['checklistId']}'",

        "updatechecklistitem"       => "UPDATE `". $config['prefix'] . "checklistitems`
										SET `notes` = '{$values['newnotes']}', `item` = '{$values['newitem']}', 
												`checklistId` = '{$values['checklistId']}', 
												`checked`='{$values['newchecked']}' 
										WHERE `checklistItemId` ='{$values['checklistItemId']}'",

        "updatespacecontext"        => "UPDATE `". $config['prefix'] . "context`
										SET `name` ='{$values['name']}', 
												`description`='{$values['description']}' 
										WHERE `contextId` ='{$values['id']}'",

        "updateitem"                => "UPDATE `". $config['prefix'] . "items`
										SET `description` = '{$values['description']}', 
												`title` = '{$values['title']}', 
												`desiredOutcome` = '{$values['desiredOutcome']}' 
										WHERE `itemId` = '{$values['itemId']}'",

        "updateitemattributes"      => "UPDATE `". $config['prefix'] . "itemattributes`
										SET `type` = '{$values['type']}', 
												`isSomeday`= '{$values['isSomeday']}', 
												`categoryId` = '{$values['categoryId']}', 
												`contextId` = '{$values['contextId']}', 
												`timeframeId` = '{$values['timeframeId']}', 
												`deadline` ={$values['deadline']}, 
												`repeat` = '{$values['repeat']}', 
												`suppress`='{$values['suppress']}', 
												`suppressUntil`='{$values['suppressUntil']}' 
										WHERE `itemId` = '{$values['itemId']}'",

        "updateitemtype"            => "UPDATE `{$config['prefix']}itemattributes`
										SET `type` = '{$values['type']}',
                                            `isSomeday`= '{$values['isSomeday']}'
										WHERE `itemId` = '{$values['itemId']}'",

        "updatelist"                => "UPDATE `". $config['prefix'] . "list`
										SET `title` = '{$values['newlistTitle']}', 
												`description` = '{$values['newdescription']}', 
												`categoryId` = '{$values['newcategoryId']}' 
										WHERE `listId` ='{$values['listId']}'",

        "updatelistitem"            => "UPDATE `". $config['prefix'] . "listitems`
										SET `notes` = '{$values['newnotes']}', `item` = '{$values['newitem']}', 
												`listId` = '{$values['listId']}', 
												`dateCompleted`={$values['newdateCompleted']}
										WHERE `listItemId` ='{$values['listItemId']}'",

        "updateparent"              => "INSERT INTO `". $config['prefix'] . "lookup` 
        										(`parentId`,`itemId`)
										VALUES ('{$values['parentId']}','{$values['itemId']}')
										ON DUPLICATE KEY UPDATE `parentId`='{$values['parentId']}'",

        "updatenextaction"          => "INSERT INTO `". $config['prefix'] . "nextactions` 
        										(`parentId`,`nextaction`)
										VALUES ('{$values['parentId']}','{$values['itemId']}')
										ON DUPLICATE KEY UPDATE `nextaction`='{$values['itemId']}'",

        "updatenote"                => "UPDATE `". $config['prefix'] . "tickler`
										SET `date` = '{$values['date']}', 
											`note` = '{$values['note']}', 
											`title` = '{$values['title']}', 
											`repeat` = '{$values['repeat']}', 
											`suppressUntil` = '{$values['suppressUntil']}' 
										WHERE `ticklerId` = '{$values['noteId']}'",

        "updatetimecontext"         => "UPDATE `". $config['prefix'] . "timeitems`
										SET `timeframe` ='{$values['name']}', 
												`description`='{$values['description']}',
												`type`='{$values['type']}'
										WHERE `timeframeId` ='{$values['id']}'",
    );
// php closing tag has been omitted deliberately, to avoid unwanted blank lines being sent to the browser
