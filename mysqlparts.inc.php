<?php

$sqlparts = array(
    
    "activeitems"           =>  " AND (`itemstatus`.`dateCompleted` IS NULL OR `itemstatus`.`dateCompleted` = '0000-00-00') AND ((CURDATE()>=DATE_ADD(`itemattributes`.`deadline`, INTERVAL -(`itemattributes`.`suppressUntil`) DAY)) OR `itemattributes`.`suppress`='n') ",
    "activelistitems"       =>  " AND (`listItems`.`dateCompleted` IS NOT NULL AND `listItems`.`dateCompleted` ='0000-00-00') ",
    "categoryfilter"        =>  " AND `projectattributes`.`categoryId` = '{$values['categoryId']}' ",
    "contextfilter"         =>  " AND `itemattributes`.`contextId` = '{$values['contextId']}' ",
    "completeditems"        =>  " AND  `itemstatus`.`dateCompleted` > 0 ",
    "completedlistitems"    =>  " AND (`listItems`.`dateCompleted`!='0000-00-00' AND `listItems`.`dateCompleted` IS NOT NULL) ",
    "getchecklists"         =>  " AND `checklist`.`categoryId`='{$values['categoryId']}' ",
    "getlists"              =>  " AND `list`.`categoryId`='{$values['categoryId']}' ",
    "issomeday"             =>  " AND `itemattributes`.`isSomeday` = '{$values['isSomeday']}' ",
    "notcategoryfilter"     =>  " AND `itemattributes`.`categoryId` != '{$values['categoryId']}' ",
    "notcontextfilter"      =>  " AND `itemattributes`.`contextId` != '{$values['contextId']}' ",
    "nottimeframefilter"    =>  " AND `itemattributes`.`timeframeId` !='{$values['timeframeId']}' ",
    "suppresseditems"       =>  " AND (`itemstatus`.`dateCompleted` IS NULL OR `itemstatus`.`dateCompleted` = '0000-00-00') AND (`projectstatus`.`dateCompleted` IS NULL OR `projectstatus`.`dateCompleted` = '0000-00-00') AND (`itemattributes`.`suppress`='y') ",
    "timeframefilter"       =>  " AND `itemattributes`.`timeframeId` ='{$values['timeframeId']}' ",
    "timegoals"             =>  " WHERE `timeitems`.`type` = 'g' ",
    "timeitems"             =>  " WHERE `timeitems`.`type` !='g' ",
    "ptypefilter"           =>  " WHERE `itemattributes`.`type` = '{$values['ptype']}' ", //PLACE FIRST IN FILTER STRING
    "typefilter"            =>  " WHERE `itemattributes`.`type` = '{$values['type']}' ",  //PLACE FIRST IN FILTER STRING
    "repeats"               =>  " AND `itemattributes`.`repeat`>'0' ",
    "doesnotrepeat"         =>  " AND `itemattributes`.`repeat`='0' ",
    "deadline"              =>  " AND `itemattributes`.`deadline` IS NOT NULL ",
    "nodeadline"            =>  " AND `itemattributes`.`deadline` IS NULL OR `itemattributes`.`deadline` = '0000-00-00' ",
    "duetoday"              =>  " AND `itemattributes`.`deadline` = '{$values['today']}' ",
    "neglected"             =>  " AND CURDATE()>DATE_ADD(`itemstatus`.`lastModified`,INTERVAL {$values['neglected']} DAY) ",
    );

//parentfilterquery: typefilter, issomeday, activeitems
//childfilterquery: typefilter, issomeday, activeitems
?>
