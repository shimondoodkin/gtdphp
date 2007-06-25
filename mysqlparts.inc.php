<?php

$sqlparts = array(
    "activeitems"               =>  " ((CURDATE()>=DATE_ADD(ia.`deadline`, INTERVAL -(ia.`suppressUntil`) DAY)) OR ia.`suppress`!='y') ",
    "activelistitems"           =>  " li.`dateCompleted` IS NULL ",
    "activeparents"             =>  " ((CURDATE()>=DATE_ADD(y.`pdeadline`, INTERVAL -(y.`psuppressUntil`) DAY)) OR y.`psuppress`!='y' OR y.`psuppress` IS NULL)",
    "categoryfilter"            =>  " ia.`categoryId` = '{$values['categoryId']}' ",
    "categoryfilter-parent"     =>  " y.`pcategoryId` = '{$values['categoryId']}' ",
    "checklistcategoryfilter"   =>  " cl.`categoryId`='{$values['categoryId']}' ",
    "completeditems"            =>  " its.`dateCompleted` IS NOT NULL ",
    "completedlistitems"        =>  " li.`dateCompleted` IS NOT NULL ",
    "contextfilter"             =>  " ia.`contextId` = '{$values['contextId']}' ",
    "due"                       =>  " (CURDATE()>=ia.`deadline` AND ia.`deadline` IS NOT NULL) ",
    "hasparent"                 =>  " y.`parentId` = '{$values['parentId']}' ",
    "issomeday"                 =>  " ia.`isSomeday` = '{$values['isSomeday']}' ",
    "issomeday-parent"          =>  " y.`pisSomeday` = '{$values['isSomeday']}' OR y.`pisSomeday` IS NULL",
    "listcategoryfilter"        =>  " l.`categoryId`='{$values['categoryId']}' ",
    "pendingitems"              =>  " its.`dateCompleted` IS NULL ",
    "pendingparents"            =>  " y.`pdatecompleted` IS NULL ",
    "notcategoryfilter"         =>  " ia.`categoryId` != '{$values['categoryId']}' ",
    "notcategoryfilter-parent"  =>  " y.`pcategoryId` != '{$values['categoryId']}' ",
    "notcontextfilter"          =>  " ia.`contextId` != '{$values['contextId']}' ",
    "notefilter"                =>  " (`date` IS NULL) OR (CURDATE()>= `date`) ",
    "nottimeframefilter"        =>  " ia.`timeframeId` !='{$values['timeframeId']}' ",
    "repeating"                 =>  " ia.`repeat` >0 ",
    "suppresseditems"           =>  " ia.`suppress`='y' AND (CURDATE()<=DATE_ADD(ia.`deadline`, INTERVAL -(ia.`suppressUntil`) DAY)) ",
    "timeframefilter"           =>  " ia.`timeframeId` ='{$values['timeframeId']}' ",
    "timetype"                  =>  " ti.`type` = '{$values['type']}' ",
    "typefilter"                =>  " ia.`type` = '{$values['type']}' ",
/*
    "ptypefilter"         =>  " ia.`type` = '{$values['ptype']}' ", 
*/
    );
// php closing tag has been omitted deliberately, to avoid unwanted blank lines being sent to the browser
