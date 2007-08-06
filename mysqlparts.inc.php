<?php

$sqlparts = array(
    "activeitems"               =>  " ((CURDATE()>=DATE_ADD(ia.`deadline`, INTERVAL -(ia.`suppressUntil`) DAY)) OR ia.`suppress`!='y') ",
    "activelistitems"           =>  " li.`dateCompleted` IS NULL ",
    "activeparents"             =>  " ((CURDATE()>=DATE_ADD(y.`pdeadline`, INTERVAL -(y.`psuppressUntil`) DAY)) OR y.`psuppress`!='y' OR y.`psuppress` IS NULL)",
    "categoryfilter"            =>  " ia.`categoryId` = '{$values['categoryId']}' ",
    "categoryfilter-parent"     =>  " y.`pcategoryId` = '{$values['categoryId']}' ",

    "checkchildren"             =>  " LEFT JOIN (
                                        SELECT parentId as itemId,COUNT(DISTINCT nextaction) AS numNA
                                            FROM {$config['prefix']}nextactions GROUP BY nextaction
                                        ) AS na ON(na.itemId=x.itemId)
                                      
                                      LEFT JOIN (
                                        SELECT cl.parentId AS itemId,count(DISTINCT cl.itemId) as numChildren
                                            FROM {$config['prefix']}lookup         AS cl
                                            JOIN {$config['prefix']}itemstatus     AS chis ON (cl.itemId=chis.itemId)
                                            JOIN {$config['prefix']}itemattributes AS chia ON (cl.itemId=chia.itemId)
                                            WHERE chis.dateCompleted IS NULL AND chia.type IN ('a','p','g','m','v','o','i','w')
                                            GROUP BY cl.parentId
                                        ) AS act ON (act.itemId=x.itemId) ",

    "checklistcategoryfilter"   =>  " cl.`categoryId`='{$values['categoryId']}' ",
    "completeditems"            =>  " its.`dateCompleted` IS NOT NULL ",
    "completedlistitems"        =>  " li.`dateCompleted` IS NOT NULL ",
    "completedparents"          =>  " y.`pdatecompleted` IS NOT NULL ",
    "contextfilter"             =>  " ia.`contextId` = '{$values['contextId']}' ",
    "countchildren"             =>  " ,na.numNA, act.numChildren",
    "due"                       =>  " (CURDATE()>=ia.`deadline` AND ia.`deadline` IS NOT NULL) ",
    "getNA"                     =>  " , COUNT(DISTINCT na.nextaction) as NA ",
    "hasparent"                 =>  " y.`parentId` = '{$values['parentId']}' ",
    "isNA"                      =>  " LEFT JOIN (
                                        SELECT nextaction FROM {$config['prefix']}nextactions
                                        ) AS na ON(na.nextaction=x.itemId) ",
    "issomeday"                 =>  " ia.`isSomeday` = '{$values['isSomeday']}' ",
    "issomeday-parent"          =>  " y.`pisSomeday` = '{$values['isSomeday']}' OR y.`pisSomeday` IS NULL",
    "limit"                     =>  " LIMIT {$values['maxItemsToSelect']} ",
    "listcategoryfilter"        =>  " l.`categoryId`='{$values['categoryId']}' ",
    "matchall"                  =>  " (i.`title` LIKE '%{$values['needle']}%'
                                      OR i.`description` LIKE '%{$values['needle']}%'
                                      OR i.`desiredOutcome` LIKE '%{$values['needle']}%' )",
    "notcategoryfilter"         =>  " ia.`categoryId` != '{$values['categoryId']}' ",
    "notcategoryfilter-parent"  =>  " y.`pcategoryId` != '{$values['categoryId']}' ",
    "notcontextfilter"          =>  " ia.`contextId` != '{$values['contextId']}' ",
    "notefilter"                =>  " (`date` IS NULL) OR (CURDATE()>= `date`) ",
    "nottimeframefilter"        =>  " ia.`timeframeId` !='{$values['timeframeId']}' ",
    "onlynextactions"           =>  " INNER JOIN {$config['prefix']}nextactions AS na ON(na.nextaction=x.itemId) ",
    "pendingitems"              =>  " its.`dateCompleted` IS NULL ",
    "pendingparents"            =>  " y.`pdatecompleted` IS NULL ",
    "repeating"                 =>  " ia.`repeat` >0 ",
    "singleitem"                =>  " i.`itemId`='{$values['itemId']}' ",
    "suppresseditems"           =>  " ia.`suppress`='y' AND (CURDATE()<=DATE_ADD(ia.`deadline`, INTERVAL -(ia.`suppressUntil`) DAY)) ",
    "timeframefilter"           =>  " ia.`timeframeId` ='{$values['timeframeId']}' ",
    "timetype"                  =>  " ti.`type` = '{$values['type']}' ",
    "typefilter"                =>  " ia.`type` = '{$values['type']}' ",
/*
    "ptypefilter"         =>  " ia.`type` = '{$values['ptype']}' ", 
*/
    );
// php closing tag has been omitted deliberately, to avoid unwanted blank lines being sent to the browser
