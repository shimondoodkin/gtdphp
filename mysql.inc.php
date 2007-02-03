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
        
        "checkchecklistitem"        => 	"UPDATE `". $config['prefix'] ."checklistItems` 
        								SET `checked` = 'y' 
        								WHERE `checklistItemId`='{$values['Cli']}'",

        "checklistselectbox"        => "SELECT l.`checklistId`, l.`title`, l.`description`, l.`categoryId`, c.`category` 
        								FROM `". $config['prefix'] ."checklist` as l, 
        										`". $config['prefix'] ."categories` as c 
        								WHERE l.`categoryId`=c.`categoryId` 
        								ORDER BY {$sort['checklistselectbox']}",

        "clearchecklist"            => "UPDATE `". $config['prefix'] ."checklistItems` 
										SET `checked` = 'n' 
										WHERE `checklistId` = '{$values['checklistId']}'",
        
        "completeitem"              => "UPDATE `". $config['prefix'] ."itemstatus` 
										SET `dateCompleted`='{$values['date']}' 
										WHERE `itemId`='{$values['completedNa']}'",

        "completelistitem"          => "UPDATE `". $config['prefix'] ."listItems` 
										SET `dateCompleted`='{$values['date']}' 
										WHERE `listItemId`='{$values['completedLi']}'",

        "copynextaction"            => "INSERT INTO `". $config['prefix'] ."nextactions` (`parentId`,`nextaction`) 
										VALUES ('{$values['parentId']}','{$values['newitemId']}') 
										ON DUPLICATE KEY UPDATE `nextaction`='{$values['newitemId']}'",
        


        "countitems"                => "SELECT `type`, COUNT(*) AS nitems 
										FROM `". $config['prefix'] ."itemattributes` as ia, 
												`". $config['prefix'] ."itemstatus` as is 
										WHERE ia.`itemId`=is.`itemId` ".$values['filterquery']." 
										GROUP BY `type`",
        
        "countnextactions"          => "SELECT COUNT(`nextaction`) AS nnextactions 
										FROM `". $config['prefix'] ."nextactions`",
										
        "countcontextreport_naonly" => "SELECT ia.`contextId`, ia.`timeframeId`, 
										COUNT(*) AS count 
										FROM `". $config['prefix'] ."itemattributes` as ia, 
												`". $config['prefix'] ."itemstatus` as is, 
												`". $config['prefix'] ."nextactions` as na 
										WHERE is.`itemId`=ia.`itemId` 
											AND  na.`nextaction` = is.`itemId`
											AND ia.`isSomeday`='n' 
											AND (is.`dateCompleted` IS NULL 
											OR is.`dateCompleted`='0000-00-00') 
										GROUP BY ia.`contextId`, ia.`timeframeId`",
        
        "countcontextreport_all"    => "SELECT ia.`contextId`, ia.`timeframeId`, 
												COUNT(*) AS count 
										FROM `". $config['prefix'] ."itemattributes` as ia, 
												`". $config['prefix'] ."itemstatus` as is 
										WHERE is.`itemId`=ia.`itemId` 
											AND ia.`type`='a' 
											AND ia.`isSomeday`='n' 
											AND (is.`dateCompleted` IS NULL 
											OR is.`dateCompleted`='0000-00-00')  
										GROUP BY ia.`contextId`, ia.`timeframeId`",
										
        "countspacecontexts"        => "SELECT COUNT(`name`) AS ncontexts 
        								FROM `". $config['prefix'] ."context`",
        
        "deletecategory"            => "DELETE FROM `". $config['prefix'] ."categories` 
        								WHERE `categoryId`='{$values['categoryId']}'",
        "deletechecklist"           => "DELETE FROM `". $config['prefix'] ."checklist` 
        								WHERE `checklistId`='{$values['checklistId']}'",
        "deletechecklistitem"       => "DELETE FROM `". $config['prefix'] ."checklistItems` 
        								WHERE `checklistItemId`='{$values['checklistItemId']}'",
        "deleteitem"                => "DELETE FROM `". $config['prefix'] ."items` 
        								WHERE `itemId`='{$values['itemId']}'",
        "deleteitemattributes"      => "DELETE FROM `". $config['prefix'] ."itemattributes` 
        								WHERE `itemId`='{$values['itemId']}'",
        "deleteitemstatus"          => "DELETE FROM `". $config['prefix'] ."itemstatus` 
        								WHERE `itemId`='{$values['itemId']}'",
        "deletelist"                => "DELETE FROM `". $config['prefix'] ."list` 
        								WHERE `listId`='{$values['listId']}'",
        "deletelistitem"            => "DELETE FROM `". $config['prefix'] ."listItems` 
        								WHERE `listItemId`='{$values['listItemId']}'",
        "deletelookup"              => "DELETE FROM `". $config['prefix'] ."lookup` 
        								WHERE `itemId` ='{$values['itemId']}'",
        "deletenextaction"          => "DELETE FROM `". $config['prefix'] ."nextactions` 
        								WHERE `nextAction`='{$values['itemId']}'",
        "deletenote"                => "DELETE FROM `". $config['prefix'] ."tickler` 
        								WHERE `ticklerId`='{$values['noteId']}'",
        "deletespacecontext"        => "DELETE FROM `". $config['prefix'] ."context` 
        								WHERE `contextId`='{$values['contextId']}'",
        "deletetimecontext"         => "DELETE FROM `". $config['prefix'] ."timeitems` 
        								WHERE `timeframeId`='{$values['tcId']}'",
        
        
        "getchecklistitems"         => "SELECT cli.`checklistitemId`, cli.`item`, cli.`notes`, cli.`checklistId`, cli.`checked` 
        								FROM `". $config['prefix'] . "checklistItems` as cli 
        									LEFT JOIN `". $config['prefix'] ."checklist` as l on cli.`checklistId` = l.`checklistId` 
										WHERE l.`checklistId` = '{$values['checklistId']}' 
										ORDER BY {$sort['getchecklistitems']}",
        
        "getchecklists"		    => 	"SELECT l.`checklistId`, l.`title`, l.`description`, l.`categoryId`, c.`category` 
        							FROM `". $config['prefix'] ."checklist` as l, `". $config['prefix'] ."categories` as c 
        							WHERE l.`categoryId`=c.`categoryId` ".$values['filterquery']." 
        							ORDER BY {$sort['getchecklists']}",
        
        "getchildren"               => 	"SELECT i.`itemId`, i.`title`, i.`description`, i.`desiredOutcome`, ia.`type`, 
        									ia.`isSomeday`, ia.`deadline`, ia.`repeat`, ia.`suppress`, ia.`suppressUntil`, 
        									is.`dateCreated`, is.`dateCompleted`, is.`lastmodified`, ia.`categoryId`, 
        									c.`category`, ia.`contextId`, cn.`name` AS cname, ia.`timeframeId`, ti.`timeframe` 
										FROM (`". $config['prefix'] . "itemattributes`, `". $config['prefix'] . "lookup` as lu) as ia 
											JOIN `". $config['prefix'] . "items` ON (ia.`itemId` = i.`itemId`) as i 
											JOIN `". $config['prefix'] . "itemstatus` ON (ia.`itemId` = is.`itemId`) as is 
											LEFT OUTER JOIN `". $config['prefix'] . "context` ON (ia.`contextId` = cn.`contextId`) as cn 
											LEFT OUTER JOIN `". $config['prefix'] ."categories` ON (ia.`categoryId` = c.`categoryId`) as c 
											LEFT OUTER JOIN `". $config['prefix'] . "timeitems` ON (ia.`timeframeId` = ti.`timeframeId`) as ti 
										WHERE lu.`itemId`= ia.`itemId` and lu.`parentId`= '{$values['parentId']}' ".$values['filterquery']." 
										ORDER BY {$sort['getchildren']}",
        
        "getitems"                  => 	"SELECT i.`itemId`, i.`title`, i.`description` 
        								FROM `". $config['prefix'] . "itemattributes` as ia 
											JOIN `". $config['prefix'] . "items` as i ON (ia.`itemId` = i.`itemId`) 
											JOIN `". $config['prefix'] . "itemstatus` as is ON (ia.`itemId` = is.`itemId`) 
											LEFT OUTER JOIN `". $config['prefix'] . "context` as cn ON (ia.`contextId` = cn.`contextId`) 
											LEFT OUTER JOIN `". $config['prefix'] ."categories` as c ON (ia.`categoryId` = c.`categoryId`) 
											LEFT OUTER JOIN `". $config['prefix'] . "timeitems` as ti ON (ia.`timeframeId` = ti.`timeframeId`) ".$values['filterquery']." 
        								ORDER BY {$sort['getitems']}",
        
        "getitemsandparent"         => "SELECT 
        										x.`itemId`, x.`title`, x.`description`, x.`desiredOutcome`, x.`type`, x.`isSomeday`, 
        										x.`deadline`, x.`repeat`, x.`suppress`, x.`suppressUntil`, x.`dateCreated`, x.`dateCompleted`, 
        										x.`lastmodified`, x.`categoryId`, x.`category`, x.`contextId`, x.`cname`, x.`timeframeId`, 
        										x.`timeframe`, y.`parentId`, y.`ptitle`, y.`pdescription`, y.`pdesiredOutcome`, y.`ptype`, 
        										y.`pisSomeday`, y.`pdeadline`, y.`prepeat`, y.`psuppress`, y.`psuppressUntil`, y.`pdateCreated`, 
        										y.`pdateCompleted`, y.`plastmodified`, y.`pcategoryId`, y.`pcatname`, y.`pcontextId`, y.`pcname`, 
        										y.`ptimeframeId`, y.`ptimeframe` 
										FROM (
												SELECT 
														i.`itemId`, i.`title`, i.`description`, i.`desiredOutcome`, ia.`type`, ia.`isSomeday`, 
														ia.`deadline`, ia.`repeat`, ia.`suppress`, ia.`suppressUntil`,  is.`dateCreated`, 
														is.`dateCompleted`, is.`lastmodified`, ia.`categoryId`, c.`category`, ia.`contextId`, 
														cn.`name` AS cname, ia.`timeframeId`, ti.`timeframe`, lu.`parentId` 
												FROM 
														`". $config['prefix'] . "itemattributes` as ia 
													JOIN `". $config['prefix'] . "items` ON (ia.`itemId` = i.`itemId`) as i
													JOIN `". $config['prefix'] . "itemstatus` ON (ia.`itemId` = is.`itemId`) as is
													LEFT OUTER JOIN `". $config['prefix'] . "context` ON (ia.`contextId` = cn.`contextId`) as cn
													LEFT OUTER JOIN `". $config['prefix'] ."categories` ON (ia.`categoryId` = c.`categoryId`) as c
													LEFT OUTER JOIN `". $config['prefix'] . "timeitems` ON (ia.`timeframeId` = ti.`timeframeId`) as ti
													LEFT OUTER JOIN `". $config['prefix'] . "lookup` as lu ON (ia.`itemId` = lu.`itemId`)".$values['childfilterquery']."
										) as x 
											LEFT OUTER JOIN 
											(
												SELECT 
														i.`itemId` AS parentId, i.`title` AS ptitle, i.`description` AS pdescription, 
														i.`desiredOutcome` AS pdesiredOutcome, ia.`type` AS ptype, ia.`isSomeday` AS pisSomeday, 
														ia.`deadline` AS pdeadline, ia.`repeat` AS prepeat, ia.`suppress` AS psuppress, 
														ia.`suppressUntil` AS psuppressUntil,  is.`dateCreated` AS pdateCreated, 
														is.`dateCompleted` AS pdateCompleted, is.`lastmodified` AS plastmodified, 
														ia.`categoryId` AS pcategoryId, c.`category` as pcatname, ia.`contextId` AS pcontextId, 
														cn.`name` AS pcname, ia.`timeframeId` AS ptimeframeId, ti.`timeframe` AS ptimeframe 
												FROM 
														`". $config['prefix'] . "itemattributes` as i 
													JOIN `". $config['prefix'] . "items` ON (ia.`itemId` = i.`itemId`) as i
													JOIN `". $config['prefix'] . "itemstatus` ON (ia.`itemId` = is.`itemId`) as is
													LEFT OUTER JOIN `". $config['prefix'] . "context` ON (ia.`contextId` = cn.`contextId`) as cn
													LEFT OUTER JOIN `". $config['prefix'] ."categories` ON (ia.`categoryId` = c.`categoryId`) as c
													LEFT OUTER JOIN `". $config['prefix'] . "timeitems` as ti 
														ON (ia.`timeframeId` = ti.`timeframeId`)".$values['parentfilterquery']."
											) as y 
										ON (y.parentId = x.parentId) ".$values['filterquery']." 
										ORDER BY {$sort['getitemsandparent']}",
        
        "getlistitems"              => "SELECT `listItems`.`listItemId`, `listItems`.`item`, `listItems`.`notes`, `listItems`.`listId` 
        								FROM `listItems` 
        									LEFT JOIN `list` on `listItems`.`listId` = `list`.`listId` 
										WHERE `list`.`listId` = '{$values['listId']}' ".$values['filterquery']." 
										ORDER BY {$sort['getlistitems']}",
        
        "getlists"                  => "SELECT `list`.`listId`, `list`.`title`, `list`.`description`, `list`.`categoryId`, c.`category` 
        								FROM `list`, `". $config['prefix'] ."categories` as c 
        								WHERE `list`.`categoryId`=c.`categoryId` ".$values['filterquery']." 
        								ORDER BY {$sort['getlists']}",
        
        "getnotes"                  => "SELECT `ticklerId`, `title`, `note`, `date` 
        								FROM `tickler` ".$values['filterquery']." 
        								ORDER BY {$sort['getnotes']}",
        								
        "getnextactions"            => "SELECT `parentId`, `nextaction` 
        								FROM `nextactions`",
        
	"getorphaneditems"	    => 			"SELECT ia.`itemId`, ia.`type`, i.`title`, i.`description` 
										FROM `". $config['prefix'] . "itemattributes` as i, `". $config['prefix'] . "items` as i,`". $config['prefix'] . "itemstatus` as is 
										WHERE i.`itemId`=ia.`itemId` AND is.`itemId`=ia.`itemId` AND (is.`dateCompleted` IS NULL 
										OR is.`dateCompleted`='0000-00-00') 
										AND ia.`type`!='m' 
										AND ia.`type`!='i' 
										AND 
											(
											ia.`itemId` NOT IN 
												(
												SELECT lu.`itemId` 
												FROM `". $config['prefix'] . "lookup` as lu
												)
											) 
										ORDER BY {$sort['getorphaneditems']}",

        "getspacecontexts"          => "SELECT `contextId`, `name`
										FROM `". $config['prefix'] . "context` as cn",
        "gettimecontexts"           => "SELECT `timeframeId`, `timeframe`, `description`
										FROM `". $config['prefix'] . "timeitems` as ti",
        
        
        "listselectbox"             => "SELECT `list`.`listId`, `list`.`title`, `list`.`description`, `list`.`categoryId`, c.`category`
										FROM `list`, `". $config['prefix'] ."categories` as c 
										WHERE `list`.`categoryId`=c.`categoryId`
										ORDER BY {$sort['listselectbox']}",

        "lookupparent"              => "SELECT `parentId`, `itemId`
										FROM `lookup` 
										WHERE `itemId`='{$values['itemId']}'",

        "newcategory"               => "INSERT INTO `". $config['prefix'] ."categories` as c
										VALUES (NULL, '{$values['category']}', '{$values['description']}')",
										
        "newchecklist"              => "INSERT INTO `". $config['prefix'] ."checklist` as l
										VALUES (NULL, '{$values['title']}', '{$values['categoryId']}', '{$values['description']}')",
										
        "newchecklistitem"          => "INSERT INTO `checklistItems` 
										VALUES (NULL, '{$values['item']}', '{$values['notes']}', '{$values['checklistId']}', 'n')",

        "newitem"                   => "INSERT INTO `items` (`title`,`description`,`desiredOutcome`)
										VALUES ('{$values['title']}','{$values['description']}','{$values['desiredOutcome']}')",

        "newitemattributes"         => "INSERT INTO `itemattributes` (`itemId`,`type`,`isSomeday`,`categoryId`,`contextId`,
												`timeframeId`,`deadline`,`repeat`,`suppress`,`suppressUntil`)
										VALUES ('{$values['newitemId']}','{$values['type']}','{$values['isSomeday']}',
												{$values['categoryId']},'{$values['contextId']}','{$values['timeframeId']}',
												{$values['deadline']},'{$values['repeat']}','{$values['suppress']}',
												'{$values['suppressUntil']}')",

        "newitemstatus"             => "INSERT INTO `itemstatus` (`itemId`,`dateCreated`,`dateCompleted`)
										VALUES ('{$values['newitemId']}',CURRENT_DATE,{$values['dateCompleted']})",
										
        "newlist"                   => "INSERT INTO `list`
										VALUES (NULL, '{$values['title']}', '{$values['categoryId']}', '{$values['description']}')",

        "newlistitem"               => "INSERT INTO `listItems`
										VALUES (NULL, '{$values['item']}', '{$values['notes']}', '{$values['listId']}', 'n')",

        "newnextaction"             => "INSERT INTO `nextactions` (`parentId`,`nextaction`)
										VALUES ('{$values['parentId']}','{$values['newitemId']}')
										ON DUPLICATE KEY UPDATE `nextaction`='{$values['newitemId']}'",

        "newnote"                   => "INSERT INTO `tickler` (`date`,`title`,`note`,`repeat`,`suppressUntil`)
										VALUES ('{$values['date']}','{$values['title']}','{$values['note']}','{$values['repeat']}','{$values['suppressUntil']}')",

        "newparent"                 => "INSERT INTO `lookup` (`parentId`,`itemId`)
										VALUES ('{$values['parentId']}','{$values['newitemId']}')",

        "newspacecontext"           => "INSERT INTO `context`  (`name`,`description`)
										VALUES ('{$values['name']}', '{$values['description']}')",

        "newtimecontext"            => "INSERT INTO `timeitems` (`timeframe`,`description`,`type`)
										VALUES ('{$values['name']}', '{$values['description']}', '{$values['type']}')",

        "parentselectbox"           => "SELECT `items`.`itemId`, `items`.`title`, 
												`items`.`description`, `itemattributes`.`isSomeday`
										FROM `items`, `itemattributes`, `itemstatus` 
										WHERE `itemattributes`.`itemId`=`items`.`itemId`
											AND `itemstatus`.`itemId`=`items`.`itemId`
											AND `itemattributes`.`type`='{$values['ptype']}'
											AND (`itemstatus`.`dateCompleted` IS NULL
											OR `itemstatus`.`dateCompleted` = '0000-00-00')
										ORDER BY {$sort['parentselectbox']}",


        "reassigncategory"          => "UPDATE `itemattributes`
										SET `categoryId`='{$values['newCategoryId']}' 
										WHERE `categoryId`='{$values['categoryId']}'",

        "reassignspacecontext"      => "UPDATE `itemattributes`
										SET `contextId`='{$values['newContextId']}' 
										WHERE `contextId`='{$values['contextId']}'",

        "reassigntimecontext"       => "UPDATE `itemattributes`
										SET `timeframeId`='{$values['ntcId']}' 
										WHERE `timeframeId`='{$values['tcId']}'",


        "removechecklistitems"      => "DELETE
										FROM `checklistItems` 
										WHERE `checklistId`='{$values['checklistId']}'",

        "removelistitems"           => "DELETE
										FROM `listItems` 
										WHERE `listId`='{$values['listId']}'",

        "repeatnote"                => "UPDATE `tickler`
										SET `date` = DATE_ADD(`date`, INTERVAL ".$values['repeat']." DAY), 
											`note` = '{$values['note']}', `title` = '{$values['title']}', 
											`repeat` = '{$values['repeat']}', `suppressUntil` = '{$values['suppressUntil']}' 
										WHERE `ticklerId` = '{$values['noteId']}'",

        "selectcategory"            => "SELECT `categoryId`, `category`, `description`
										FROM `". $config['prefix'] ."categories` as c 
										WHERE `categoryId` = '{$values['categoryId']}'",

        "selectchecklist"           => "SELECT l.`checklistId`, l.`title`, l.`description`, l.`categoryId`, c.`category`
										FROM `". $config['prefix'] ."checklist` as l, `". $config['prefix'] ."categories` as c 
										WHERE l.`categoryId`=c.`categoryId`
											AND `checklistId`='{$values['checklistId']}' ".$values['filterquery']."
										ORDER BY {$sort['selectchecklist']}",

        "selectchecklistitem"       => "SELECT `checklistItems`.`checklistItemId`, `checklistItems`.`item`, `checklistItems`.`notes`, `checklistItems`.`checklistId`, `checklistItems`.`checked`
										FROM `checklistItems` 
										WHERE `checklistItemId` = '{$values['checklistItemId']}'",

        "selectcontext"             => "SELECT `context`.`contextId`, `context`.`name`, `context`.`description`
										FROM `context` 
										WHERE `context`.`contextId` = '{$values['contextId']}'",

        "selectitem"                => "SELECT `items`.`itemId`, `itemattributes`.`type`, `items`.`title`, 
        										`items`.`description`, `items`.`desiredOutcome`, 
        										`itemattributes`.`categoryId`, `itemattributes`.`contextId`, 
        										`itemattributes`.`timeframeId`, `itemattributes`.`isSomeday`, 
        										`itemattributes`.`deadline`, `itemattributes`.`repeat`, 
        										`itemattributes`.`suppress`, `itemattributes`.`suppressUntil`, 
        										`itemstatus`.`dateCreated`, `itemstatus`.`dateCompleted`, 
        										`itemstatus`.`lastModified`, c.`category`,`timeitems`.`timeframe`, 
        										`context`.`name` AS `cname` 
										FROM (`items`, `itemattributes`, `itemstatus`)
											LEFT OUTER JOIN `". $config['prefix'] ."categories` as c
											ON (c.`categoryId`=`itemattributes`.`categoryId`)
											LEFT OUTER JOIN `context`
											ON (`context`.`contextId` = `itemattributes`.`contextId`)
											LEFT OUTER JOIN `timeitems`
											ON (`timeitems`.`timeframeId` = `itemattributes`.`timeframeId`) 
										WHERE `itemstatus`.`itemId`=`items`.`itemId`
											AND `itemattributes`.`itemId`=`items`.`itemId`
											AND `items`.`itemId` = '{$values['itemId']}'",

        "selectlist"                => "SELECT `list`.`listId`, `list`.`title`, `list`.`description`, `list`.`categoryId`
										FROM `list` 
										WHERE `list`.`listId` = '{$values['listId']}'",

        "selectlistitem"            => "SELECT `listItems`.`listItemId`, `listItems`.`item`, `listItems`.`notes`, `listItems`.`listId`, `listItems`.`dateCompleted`
										FROM `listItems` 
										WHERE `listItems`.`listItemId` = {$values['listItemId']}",

        "selectnextaction"          => "SELECT `nextactions`.`parentId`, `nextactions`.`nextaction`
										FROM `nextactions` 
										WHERE `nextactions`.`parentId` = '{$values['parentId']}'",

        "selectnote"                => "SELECT `tickler`.`ticklerId`, `tickler`.`title`, `tickler`.`note`, `tickler`.`date`, `tickler`.`repeat`, `tickler`.`suppressUntil`
										FROM `tickler` 
										WHERE `tickler`.`ticklerId` = '{$values['noteId']}'",

        "selecttimecontext"         => "SELECT `timeitems`.`timeframeId`, `timeitems`.`timeframe`, `timeitems`.`description`, `timeitems`.`type`
										FROM `timeitems` 
										WHERE `timeitems`.`timeframeId` = '{$values['tcId']}'",

        "spacecontextselectbox"     => "SELECT `contextId`, `name`, `description`
										FROM `context`
										ORDER BY {$sort['spacecontextselectbox']}",

        "testitemrepeat"            => "SELECT `itemattributes`.`repeat`
										FROM `itemattributes` 
										WHERE `itemattributes`.`itemId`='{$values['completedNa']}'",

        "testnextaction"            => "SELECT `parentId`, `nextaction`
										FROM `nextactions` 
										WHERE `nextaction`='{$values['itemId']}'",

        "timecontextselectbox"      => "SELECT `timeframeId`, `timeframe`, `description`
										FROM `timeitems`".$values['timefilterquery']."ORDER BY {$sort['timecontextselectbox']}",

        "updatecategory"            => "UPDATE `". $config['prefix'] ."categories` as c
										SET `category` ='{$values['category']}', `description` ='{$values['description']}' 
										WHERE `categoryId` ='{$values['categoryId']}'",

        "updatechecklist"           => "UPDATE `". $config['prefix'] ."checklist` as l
										SET `title` = '{$values['newchecklistTitle']}', `description` = '{$values['newdescription']}', `categoryId` = '{$values['newcategoryId']}' 
										WHERE `checklistId` ='{$values['checklistId']}'",

        "updatechecklistitem"       => "UPDATE `checklistItems`
										SET `notes` = '{$values['newnotes']}', `item` = '{$values['newitem']}', `checklistId` = '{$values['checklistId']}', `checked`='{$values['newchecked']}' 
										WHERE `checklistItemId` ='{$values['checklistItemId']}'",

        "updatespacecontext"        => "UPDATE `context`
										SET `name` ='{$values['name']}', `description`='{$values['description']}' 
										WHERE `contextId` ='{$values['contextId']}'",

        "updateitem"                => "UPDATE `items`
										SET `description` = '{$values['description']}', `title` = '{$values['title']}', `desiredOutcome` = '{$values['desiredOutcome']}' 
										WHERE `itemId` = '{$values['itemId']}'",

        "updateitemattributes"      => "UPDATE `itemattributes`
										SET `type` = '{$values['type']}', `isSomeday`= '{$values['isSomeday']}', 
												`categoryId` = '{$values['categoryId']}', `contextId` = '{$values['contextId']}', 
												`timeframeId` = '{$values['timeframeId']}', `deadline` ={$values['deadline']}, 
												`repeat` = '{$values['repeat']}', `suppress`='{$values['suppress']}', 
												`suppressUntil`='{$values['suppressUntil']}' 
										WHERE `itemId` = '{$values['itemId']}'",

        "updateitemstatus"          => "UPDATE `itemstatus`
										SET `dateCompleted` = {$values['dateCompleted']} 
										WHERE `itemId` = '{$values['itemId']}'",

        "updatelist"                => "UPDATE `list`
										SET `title` = '{$values['newlistTitle']}', `description` = '{$values['newdescription']}', 
												`categoryId` = '{$values['newcategoryId']}' 
										WHERE `listId` ='{$values['listId']}'",

        "updatelistitem"            => "UPDATE `listItems`
										SET `notes` = '{$values['newnotes']}', `item` = '{$values['newitem']}', 
												`listId` = '{$values['listId']}', `dateCompleted`='{$values['newdateCompleted']}' 
										WHERE `listItemId` ='{$values['listItemId']}'",

        "updateparent"              => "INSERT INTO `lookup` (`parentId`,`itemId`)
										VALUES ('{$values['parentId']}','{$values['itemId']}')
										ON DUPLICATE KEY UPDATE `parentId`='{$values['parentId']}'",

        "updatenextaction"          => "INSERT INTO `nextactions` (`parentId`,`nextaction`)
										VALUES ('{$values['parentId']}','{$values['itemId']}')
										ON DUPLICATE KEY UPDATE `nextaction`='{$values['itemId']}'",

        "updatenote"                => "UPDATE `tickler`
										SET `date` = '{$values['date']}', `note` = '{$values['note']}', 
											`title` = '{$values['title']}', `repeat` = '{$values['repeat']}', 
											`suppressUntil` = '{$values['suppressUntil']}' 
										WHERE `ticklerId` = '{$values['noteId']}'",

        "updatetimecontext"         => "UPDATE `timeitems`
										SET `timeframe` ='{$values['name']}', `description`='{$values['description']}', `type`='{$values['type']}' 
										WHERE `timeframeId` ='{$values['tcId']}'",
    );
