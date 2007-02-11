<?php

$sqlparts = array(
    "activeitems"               =>  " ((CURDATE()>=DATE_ADD(ia.`deadline`, INTERVAL -(ia.`suppressUntil`) DAY)) OR ia.`suppress`!='y') ",
    "activelistitems"           =>  " (li.`dateCompleted` IS NOT NULL AND li.`dateCompleted` ='0000-00-00') ",
    "activeparents"             =>  " ((CURDATE()>=DATE_ADD(y.`pdeadline`, INTERVAL -(y.`psuppressUntil`) DAY)) OR y.`psuppress`!='y' OR y.`psuppress` IS NULL)",
    "categoryfilter"            =>  " ia.`categoryId` = '{$values['categoryId']}' ",
    "categoryfilter-parent"     =>  " y.`pcategoryId` = '{$values['categoryId']}' ",
    "checklistcategoryfilter"   =>  " cl.`categoryId`='{$values['categoryId']}' ",
    "completeditems"            =>  " (its.`dateCompleted` IS NOT NULL AND its.`dateCompleted` != '0000-00-00') ",
    "completedlistitems"        =>  " (li.`dateCompleted`!='0000-00-00' AND li.`dateCompleted` IS NOT NULL) ",
    "contextfilter"             =>  " ia.`contextId` = '{$values['contextId']}' ",
    "due"                       =>  " (CURDATE()>=ia.`deadline` AND ia.`deadline` IS NOT NULL AND ia.`deadline`!='0000-00-00') ",
    "isnotcompleteditem"        =>  " its.`dateCompleted` is  NULL",
    "issomeday"                 =>  " ia.`isSomeday` = '{$values['isSomeday']}' ",
    "issomeday-parent"          =>  " y.`pisSomeday` = '{$values['isSomeday']}' OR y.`pisSomeday` IS NULL",
    "listcategoryfilter"        =>  " l.`categoryId`='{$values['categoryId']}' ",
    "pendingitems"              =>  " (its.`dateCompleted` IS NULL OR its.`dateCompleted` = '0000-00-00') ",
    "notcategoryfilter"         =>  " ia.`categoryId` != '{$values['categoryId']}' ",
    "notcategoryfilter-parent"  =>  " y.`pcategoryId` != '{$values['categoryId']}' ",
    "notcontextfilter"          =>  " ia.`contextId` != '{$values['contextId']}' ",
    "notefilter"                =>  " (`date` IS NULL OR `date` = '0000-00-00') OR (CURDATE()>= `date`) ",
    "nottimeframefilter"        =>  " ia.`timeframeId` !='{$values['timeframeId']}' ",
    "repeating"                 =>  " ia.`repeat` >0 ",
    "suppresseditems"           =>  " ia.`suppress`='y' AND (CURDATE()<=DATE_ADD(ia.`deadline`, INTERVAL -(ia.`suppressUntil`) DAY)) ",
    "timeframefilter"           =>  " ia.`timeframeId` ='{$values['timeframeId']}' ",
    "timegoals"                 =>  " ti.`type` = 'g' ",
    "timeitems"                 =>  " ti.`type` !='g' ",
    "typefilter"                =>  " ia.`type` = '{$values['type']}' ",
/*
    "ptypefilter"         =>  " ia.`type` = '{$values['ptype']}' ", 
*/
    );
?>
