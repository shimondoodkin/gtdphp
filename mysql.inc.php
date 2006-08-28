<?php

//query listing for MySQL database
//API Documentation available in __________
//NB: All queries written to operate on single table for compatibility with other databases
//MySQL extensions to standard SQL have been avoided where known
//Queries may be rewritten in future as SQL evolves and as other database types are supported
//Evential goal is less queries

/*To-Do:
Check and finish all backticking
Check and finish all variable substitution
Standardize variable names for query (standard input)
Update queries for flexible sorting
Ensure no multitable operations
review all queries-- multipart queries? vs define in values/options
Test each query in phpMyAdmin
Create new branch to fold in prior commits, work from there
rewrite display pages to work directly with returned data (clean/protect data in display code)
$options array -- initialize at top of page, overwrite as necessary with query (sort, etc).
possible to reduce number of display pages with switches again? (insert/delete/update backend pages; possibly view?)
sanitize input to function (likely at $values array)
sanitize database output ?in display code.... (to prevent faked database output)
hide data in forms, sanitize, post vs get, etc.
Send for beta test
Create documentation
eventually sessions/cookies
*/


$sql = array(
//SpaceContexts
        "getspacecontexts"              =>"SELECT contextId, name, description FROM context ORDER BY name ASC",
        "selectspacecontext"          =>"SELECT contextId, name, description FROM context WHERE contextId = '{$values['contextId']}'",
        "newspacecontext"           => "INSERT INTO context  VALUES (NULL, '{$values['name']}', '{$values['description']}')",
        "updatespacecontext"     =>  "UPDATE `context` SET `name` ='{$values['name']}', `description`='{$values['description']}' WHERE `contextId` ='{$values['contextId']}'",
        "reassignspacecontext"   =>  "UPDATE `itemattributes` SET `contextId`='{$values['newContextId']}' WHERE `contextId`='{$values['contextId']}`",
        "deletespacecontext"      =>  "DELETE FROM `context` WHERE `contextId`='{$values['contextId']}'",
//TimeContexts
        "gettimecontexts"              =>"SELECT timeframeId, timeframe, description FROM timeitems ORDER BY timeframe DESC",
        "selecttimecontext"          =>"SELECT timeframeId, timeframe, description FROM timeitems WHERE timeframeId = '{$values['tcId']}'",
        "newtimecontext"              =>"INSERT INTO timeitems  VALUES (NULL, '{$values['name']}', '{$values['description']}')",
        "updatetimecontext"         =>"",
        "reassigntimecontext"       => "",
        "deletetimecontext"          =>"",
//Categories
        "getcategories"              =>"SELECT `categories`.`categoryId`, `categories`.`category`, `categories`.`description` FROM `categories` ORDER BY `categories`.`category` ASC",
        "selectcategory"          =>"SELECT categoryId, category, description FROM categories WHERE categoryId = '{$values['categoryId']}'",
        "newcategory"                   =>"INSERT INTO categories VALUES (NULL, '{$values['name']}', '{$values['description']}')",
        "updatecategory"              =>"UPDATE categories SET category ='{$values['category']}', description='{$values['description']}' WHERE categoryId ='{$values['categoryId']}'",
        "reassigncategory"              =>"UPDATE projectattributes SET categoryId='$newCategoryId' WHERE categoryId='{$values['categoryId']}'",
        "deletecategory"              =>"DELETE FROM categories WHERE categoryId='{$values['categoryId']}'",
//Goals
        "getgoals"              =>"SELECT * FROM goals ORDER BY type ASC",
        "getactivegoals"              =>"SELECT * FROM goals WHERE completed IS NOT NULL AND completed != "0000-00-00" ORDER BY created DESC, type DESC",
        "selectgoal"              =>"SELECT * FROM goals WHERE id = '{$values['$goalId']}'",
        "newgoal"              =>"INSERT INTO goals  VALUES (NULL, '{$values['goal']}', '{$values['description']}','{$values['date']}', '{$values['deadline']}',  NULL, '{$values['type']}', '{$values['`projectId`']}')",
        "updategoal"              =>"UPDATE goals SET goal = '{$values['goal']}', description = '{$values['description']}', created = '{$values['created']}', deadline = '{$values['deadline']}', completed = ''{$values['completed']}'', type='{$values['type']}', `projectId` = '{$values['`projectId`']}' WHERE id = '{$values['gid']}'",
        "deletegoal"              =>"",
//Projects
        //Filtered queries??
        "getprojects"              =>"SELECT `projectId`, `name` FROM `projects` ORDER BY `name` ASC",
//SELECT `projects`.`projectId`, `projects`.`name`, `projects`.`description`, `projectattributes`.`categoryId`, `categories`.`category`, `projectattributes`.`deadline`, `projectattributes`.`repeat`, `projectattributes`.`suppress`, `projectattributes`.`suppressUntil` FROM projects, projectattributes, projectstatus, categories WHERE projectattributes`.``projectId`=`projects`.`projectId` AND `projectattributes`.`categoryId`=categories`.`categoryId AND projectstatus`.``projectId`=`projects`.`projectId` AND projectattributes`.`isSomeday = '{$values['isSomeday']}' AND "`.`$compq`.`" ORDER BY `categories`.`category`, `projects`.`name` ASC

        "getsuppressedprojects"              =>"SELECT `projects`.`projectId`, `projects`.`name`, `projects`.`description`, `projectstatus`.`dateCreated`, `categories`.`categoryId`, `categories`.`category` AS `cname`, `projectattributes`.`deadline`, `projectattributes`.`repeat`, `projectattributes`.`suppress`, `projectattributes`.`suppressUntil` FROM `projects`, `projectattributes`, `projectstatus`, `categories` WHERE `projectstatus`.`projectId` = `projects`.`projectId` AND `projectattributes`.`projectId` = `projects`.`projectId` AND `categories`.`categoryId`=`projectattributes`.`categoryId` AND (`projectstatus`.`dateCompleted` IS NULL OR `projectstatus`.`dateCompleted` = '0000-00-00') AND (`projectattributes`.`suppress`='y') ORDER BY `projectattributes`.`deadline`, `cname`, `projects`.`name`",
        "getactiveprojects"     =>"SELECT `projects`.`projectId`, `projects`.`name`, `projects`.`description`, `projectattributes`.`categoryId`, `categories`.`category` FROM `projects`, `projectattributes, projectstatus`, `categories` WHERE `projectattributes`.`projectId`=`projects`.`projectId` AND `projectattributes`.`categoryId`=`categories`.`categoryId` AND `projectstatus`.`projectId`=`projects`.`projectId` AND (`projectstatus`.`dateCompleted` IS NULL OR `projectstatus`.`dateCompleted` = '0000-00-00') AND `projectattributes`.`isSomeday`='n' ORDER BY `projects`.`name` ASC",
        "getsomeday"              =>"SELECT `projects`.`projectId`, `projects`.`name`, `projects`.`description`, `projectattributes`.`categoryId`, `categories`.`category` FROM `projects`, `projectattributes`, `projectstatus`, `categories` WHERE `projectattributes`.`projectId`=`projects`.`projectId` AND `projectattributes`.`categoryId`=`categories`.`categoryId` AND `projectstatus`.`projectId`=`projects`.`projectId` AND (`projectstatus`.`dateCompleted` IS NULL OR `projectstatus`.`dateCompleted` = '0000-00-00') AND `projectattributes`.`isSomeday`='y' ORDER BY `projects`.`name` ASC",
        // getprojects/someday        SELECT `projects`.`projectId`, `projects`.`name`, `projects`.`description` FROM projects, projectattributes, projectstatus WHERE projectattributes`.``projectId` = `projects`.`projectId` AND projectstatus`.``projectId`=`projects`.`projectId` AND (`projectstatus`.`dateCompleted` IS NULL OR `projectstatus`.`dateCompleted` = '0000-00-00') AND projectattributes`.`isSomeday ='{$values['isSomeday']}' ORDER BY `projects`.`name`
        "getcompletedprojects"              =>"",
/*
if ('{$values['completed']}'=="y") $compq => "`projectstatus`.`dateCompleted` > 0";
else $compq => "(`projectstatus`.`dateCompleted` IS NULL OR `projectstatus`.`dateCompleted` = '0000-00-00') AND (((CURDATE()>=DATE_ADD(`projectattributes`.`deadline`, INTERVAL -(`projectattributes`.`suppressUntil`) DAY)) OR `projectattributes`.`suppress`='n'))";
        $query=>"SELECT `projects`.`projectId`, `projects`.`name`, `projects`.`description`, `projectattributes`.`categoryId`, `categories`.`category`, `projectattributes`.`deadline`, `projectattributes`.`repeat`, `projectattributes`.`suppress`, `projectattributes`.`suppressUntil` FROM projects, projectattributes, projectstatus, categories WHERE projectattributes`.``projectId`=`projects`.`projectId` AND `projectattributes`.`categoryId`=categories`.`categoryId AND projectstatus`.``projectId`=`projects`.`projectId` AND ("`.`$compq`.`") AND `projectattributes`.`categoryId`='{$values['categoryId']}' AND projectattributes`.`isSomeday='{$values['isSomeday']}' ORDER BY `categories`.`category`, `projects`.`name` ASC";

$query => "SELECT projects`.`name, projects`.`description, projects`.`desiredOutcome, projectstatus`.`dateCreated, projectstatus`.`dateCompleted, projectstatus`.`lastModified, projectattributes`.`deadline, projectattributes`.`repeat, projectattributes`.`suppress, projectattributes`.`suppressUntil, projectattributes`.`isSomeday FROM projects,projectattributes, projectstatus WHERE projectstatus`.``projectId` = `projects`.`projectId` AND projectattributes`.``projectId` = `projects`.`projectId` AND `projects`.`projectId` = '{$values['pId']}'";
function doitemquery($projectId,$type,$completed='n') {
        if ('{$values['completed']}'=="y") $compq = "itemstatus`.`dateCompleted > 0";
        else $compq = "itemstatus`.`dateCompleted IS NULL OR itemstatus`.`dateCompleted = '0000-00-00'";

        $query => "SELECT `items`.`itemId`, `items`.`title`, `items`.`description`, `itemstatus`.`dateCreated`, `itemstatus`.`dateCompleted`, `context`.`contextId`, `context`.`name` AS `cname`, `itemattributes`.`deadline`, `itemattributes`.`repeat`, `itemattributes`.`suppress`, `itemattributes`.`suppressUntil` FROM `items`, `itemattributes`, `itemstatus`, `context` WHERE `itemstatus`.`itemId` = `items`.`itemId` AND `itemattributes`.`itemId` = `items`.`itemId` AND `itemattributes`.`contextId` = `context`.`contextId` AND `itemattributes`.`projectId` = '{$values['`projectId`']}' AND `itemattributes`.`type` = '{$values['type']}' AND ("`.`$compq`.`") ORDER BY `items`.`title` ASC, `cname` ASC";
        $result = mysql_query($query) OR die ("Error in query");
        return $result;
        }
$query => "SELECT `projectId`, `nextaction` FROM `nextactions`";
$compq => "(`projectstatus`.`dateCompleted` IS NULL OR `projectstatus`.`dateCompleted` = '0000-00-00') AND (((CURDATE()>=DATE_ADD(`projectattributes`.`deadline`, INTERVAL -(`projectattributes`.`suppressUntil`) DAY)) OR `projectattributes`.`suppress`='n'))";
$query=>"SELECT `projects`.`projectId`, `projects`.`name`, `projects`.`description`, `projectattributes`.`categoryId`, `categories`.`category`, `projectattributes`.`deadline`, `projectattributes`.`repeat`, `projectattributes`.`suppress`, `projectattributes`.`suppressUntil` FROM projects, projectattributes, projectstatus, categories WHERE projectattributes`.``projectId`=`projects`.`projectId` AND `projectattributes`.`categoryId`=categories`.`categoryId AND projectstatus`.``projectId`=`projects`.`projectId` AND projectattributes`.`isSomeday = '{$values['isSomeday']}' AND "`.`$compq`.`" ORDER BY `categories`.`category`, `projects`.`name` ASC";

*/
        "selectproject"              =>"SELECT `projects`.`projectId`, `projects`.`name`, `projects`.`description`, projects`.`desiredOutcome, `projectstatus`.`dateCreated`, `projectstatus`.`dateCompleted`, `projectattributes`.`categoryId`, `projectattributes`.`deadline`, `projectattributes`.`repeat`, `projectattributes`.`suppress`, `projectattributes`.`suppressUntil`, projectattributes`.`isSomeday FROM projects, projectattributes, projectstatus WHERE projectstatus`.``projectId`=`projects`.`projectId` AND projectattributes`.``projectId`=`projects`.`projectId` AND `projects`.`projectId` = '{$values['`projectId`']}'",
        "selectactiveprojects"              =>"SELECT `projects`.`projectId`, `projects`.`name`, `projects`.`description`, `projectattributes`.`categoryId`, `categories`.`category`, `projectattributes`.`deadline`, `projectattributes`.`repeat`, `projectattributes`.`suppress`, `projectattributes`.`suppressUntil` FROM `projects`, `projectattributes`, `projectstatus`, `categories` WHERE `projectattributes`.`projectId`=`projects`.`projectId` AND `projectattributes`.`categoryId`=`categories`.`categoryId` AND `projectstatus`.`projectId`=`projects`.`projectId` AND `projectattributes`.`isSomeday` = 'n'AND (`projectstatus`.`dateCompleted` IS NULL OR `projectstatus`.`dateCompleted` = '0000-00-00') AND (((CURDATE()>=DATE_ADD(`projectattributes`.`deadline`, INTERVAL -(`projectattributes`.`suppressUntil`) DAY)) OR `projectattributes`.`suppress`='n')) ORDER BY `categories`.`category`, `projectattributes`.`deadline`, `projects`.`name` ASC"`.`
        "newproject"              =>"INSERT INTO projects (name,description,desiredOutcome) VALUES ('{$values['name']}','{$values['description']}','{$values['desiredOutcome']}')",
        "newprojectattributes" =>"INSERT INTO projectattributes (`projectId`,categoryId,isSomeday,deadline,`repeat`,suppress,suppressUntil) VALUES ('{$values['`projectId`']}','{$values['categoryId']}','{$values['isSomeday']}','{$values['deadline']}','{$values['repeat']}','{$values['suppress']}','{$values['suppressUntil']}')",
        "newprojectstatus" => "INSERT INTO projectstatus (`projectId`,dateCreated) VALUES ('{$values['`projectId`']}',CURRENT_DATE)",
        "updateproject"              =>"UPDATE `projects`, `projectattributes`, `projectstatus` SET `projects`.`description` = '{$values['description']}', `projects`.`name` = '{$values['name']}', `projects`.`desiredOutcome` = '{$values['desiredOutcome']}',  `projectattributes`.`categoryId` = '{$values['categoryId']}', `projectattributes`.`isSomeday` = '{$values['isSomeday']}', `projectattributes`.`deadline` ='{$values['deadline']}', `projectattributes`.`repeat` = '{$values['repeat']}', `projectattributes`.`suppress`='{$values['suppress']}', `projectattributes`.`suppressUntil`='{$values['suppressUntil']}', `projectstatus`.`dateCompleted`='{$values['dateCompleted']}' WHERE `projectId` ='{$values['`projectId`']}'",
        "updateprojectattributes"       => "UPDATE `projectattributes` SET `projectattributes`.`categoryId` = '{$values['categoryId']}', `projectattributes`.`isSomeday` = '{$values['isSomeday']}', `projectattributes`.`deadline` ='{$values['deadline']}', `projectattributes`.`repeat` = '{$values['repeat']}', `projectattributes`.`suppress`='{$values['suppress']}', `projectattributes`.`suppressUntil`='{$values['suppressUntil']}'",
        "updateprojectstatus"              =>"UPDATE `projectstatus` SET  `projectstatus`.`dateCompleted`='{$values['dateCompleted']}' WHERE `projectstatus`.`projectId` ='{$values['`projectId`']}'",
        "deleteproject"              =>"DELETE FROM  `projects` WHERE `projects`.`projectId`='{$values['`projectId`']}'",
        "deleteprojectattributes"              =>"DELETE FROM  `projectattributes` WHERE `projectattributes`.`projectId`='{$values['`projectId`']}'",
        "deleteprojectstatus"              =>"DELETE FROM  `projectstatus` WHERE `projectstatus`.`projectId`='{$values['`projectId`']}'",
        "completeproject"              =>"UPDATE projectstatus SET dateCompleted='{$values['date']}' WHERE `projectId`='{$values['completedPr']}'",
        "doesprojectrepeat"              =>"SELECT `projectattributes`.`repeat` FROM projectattributes WHERE projectattributes`.``projectId`='{$values['completedPr']}'",
        "selectprojectbynextaction"              =>"SELECT `projectId`, nextaction FROM nextactions WHERE nextaction='{$values['itemId']}'",
        "selectprojectdetails"              =>"",
        "selectprojectname"              =>"SELECT name FROM projects WHERE `projectId`='{$values['`projectId`']}'",
        //can fold into  regular selectitem query for copying
        "selectprojectforcopy"    => "SELECT `projects`.`name`, `projects`.`description` FROM projects WHERE `projects`.`projectId`='{$values['completedPr']}'",
        "selectprojectattributesforcopy" => "SELECT projectattributes`.``projectId`, `projectattributes`.`categoryId`, projectattributes`.`isSomeday, `projectattributes`.`deadline`, `projectattributes`.`repeat`, `projectattributes`.`suppress`, `projectattributes`.`suppressUntil` FROM projectattributes WHERE projectattributes`.``projectId`='{$values['completedPr']}'",
        "selectprojectstatusforcopy" => "",

//nextactions
        "getnextactions"              =>"SELECT `projectId`, nextaction FROM nextactions",
        "selectnextaction"              =>"SELECT `projectId`, nextaction FROM nextactions WHERE `projectId`='{$values['`projectId`']}'",
        "newnextaction"              =>"INSERT INTO `nextactions` (`projectId`,`nextaction`) VALUES ('{$values['`projectId`']}','{$values['itemId']}')",
        "updatenextaction"              =>"INSERT INTO `nextactions` (`projectId`,`nextaction`) VALUES ('{$values['`projectId`']}','{$values['itemId']}') ON DUPLICATE KEY UPDATE `nextaction`='{$values['itemId']}'",
        "deletenextaction"              =>"DELETE FROM `nextactions` WHERE `nextAction`='{$values['itemId']}'",
        "removenextaction"              =>"",
        "completenextaction"        =>"DELETE FROM nextactions WHERE nextAction=''{$values['completedNa']}'",
//items
        "getitems"              =>"SELECT `itemattributes`.`projectId`, `projects`.`name` AS `pname`, `items`.`title`, `items`.`description`, `itemstatus`.`dateCreated`, `context`.`contextId, `context`.`name` AS `cname`, `items`.`itemId`, `itemstatus`.`dateCompleted`, `itemattributes`.`deadline`, `itemattributes`.`repeat`, `itemattributes`.`suppress`, `itemattributes`.`suppressUntil` FROM `items`, `itemattributes`, `itemstatus`, `projects`, `projectattributes`, `projectstatus`, `context` WHERE `itemstatus`.`itemId` = `items`.`itemId` AND `itemattributes`.`itemId` = `items`.`itemId` AND `itemattributes`.`contextId` = `context`.`contextId` AND `itemattributes`.`projectId` = `projects`.`projectId` AND `projectattributes`.`projectId`=`itemattributes`.`projectId` AND `projectstatus`.`projectId` = `itemattributes`.`projectId` AND `itemattributes`.`type = '$typequery' " `.`$catquery`.`$contextquery`.`$timequery`.` " AND `projectattributes`.`isSomeday='$ptypequery' AND (`itemstatus`.`dateCompleted` IS NULL OR `itemstatus`.`dateCompleted` = '0000-00-00') AND (`projectstatus`.`dateCompleted` IS NULL OR `projectstatus`.`dateCompleted` = '0000-00-00') AND ((CURDATE() >= DATE_ADD(`itemattributes`.`deadline`, INTERVAL -(`itemattributes`.`suppressUntil`) DAY)) OR `itemattributes`.`suppress`='n' OR ((CURDATE() >= DATE_ADD(`projectattributes`.`deadline`, INTERVAL -(`projectattributes`.`suppressUntil`) DAY)))) ORDER BY `projects`.`name`, `itemattributes`.`deadline`, items`.`title`",
/*
if ($contextId != NULL) $contextquery => "AND `itemattributes``.`contextId = '{$values['contextId']}'";
if ($categoryId != NULL) $catquery => " AND `projectattributes`.`categoryId` = '{$values['categoryId']}'";
if ($timeId !=NULL) $timequery => "AND `itemattributes``.`timeframeId ='$timeId'";
*/
        "selectitem"              =>"SELECT `items`.`itemId`, `itemattributes`.`projectId`, `itemattributes`.`contextId, `itemattributes`.`type`, `itemattributes`.`timeframeId, items`.`title`, `items`.`description`, `itemstatus`.`dateCreated`, `itemattributes`.`deadline`, `itemstatus`.`dateCompleted`, `itemstatus`.`lastModified`, `itemattributes`.`repeat`, `itemattributes`.`suppress`, `itemattributes`.`suppressUntil` FROM `items`, `itemattributes`, `itemstatus` WHERE `itemstatus`.`itemId`=`items`.`itemId` AND `itemattributes`.`itemId=`items`.`itemId` AND `items`.`itemId` = '{$values['itemId']}",
        "newitem"              =>"INSERT INTO `items` (title,description) VALUES ('{$values['title']}','{$values['description']}')",
        "newitemattributes"     => "INSERT INTO `itemattributes` (itemId,type,`projectId`,contextId,timeframeId,deadline,`repeat`,suppress,suppressUntil) VALUES ('{$values['itemId']}','{$values['type']}','{$values['`projectId`']}','{$values['contextId']}','{$values['timeframeId']}','{$values['deadline']}','{$values['repeat']}','{$values['suppress']}','{$values['suppressUntil']}')".
        //INSERT INTO `itemstatus` (itemId,dateCreated) VALUES ('$newitemId','{$values['date']}') for repeated  items to preserve date-- move date calculation to php
        "newitemstatus"         => "INSERT INTO `itemstatus` (itemId,dateCreated) VALUES ('{$values['itemId']}',CURRENT_DATE)";
        "updateitem"              =>"UPDATE items SET description = '{$values['description']}', title = '{$values['title']}'  WHERE itemId = '{$values['itemId']}'",
        "updateitemattributes"  =>"UPDATE `itemattributes` SET `type` = '{$values['type']}', `projectId` = '{$values['`projectId`']}', `contextId` = '{$values['contextId']}', timeframeId = '{$values['timeframeId']}', `deadline` ='{$values['deadline']}', `repeat` = '{$values['repeat']}', `suppress`='{$values['suppress']}', `suppressUntil`='{$values['suppressUntil']}' WHERE `itemId` = '{$values['itemId']}'";
        "updateitemstatus"      =>"UPDATE `itemstatus` SET `dateCompleted` = '{$values['dateCompleted']}' WHERE `itemId` = '{$values['itemId']}'",
        "deleteitem"              =>"DELETE FROM `items` WHERE `itemId`='{$values['itemId']}'",
        "deleteitemattributes"  =>"DELETE FROM `items` WHERE `itemId`='{$values['itemId']}'",
        "deleteitemstatus"      =>"DELETE FROM `itemattributes` WHERE `itemId`='{$values['itemId']}'",
        "completeitem"              =>"UPDATE itemstatus SET dateCompleted='{$values['date']}' WHERE itemId='{$values['completedNa']}'",
        "removeitems"              => "DELETE `items` FROM `items`, `itemattributes` WHERE `items`.`itemId`=`itemattributes`.`itemId` AND `itemattributes`.`projectId`='{$values['`projectId`']}'"`.`
        "removeitemstatus"              => "DELETE `itemstatus` FROM `items`, `itemstatus` WHERE `itemstatus`.`itemId`=`itemattributes`.`itemId` AND `itemattributes`.`projectId`='{$values['`projectId`']}'"`.`
        "removeitemattributes"              => "DELETE FROM `itemattributes` WHERE `itemattributes`.`projectId`='{$values['`projectId`']}'"`.`
        "getitemsbycontext"     => "SELECT `itemattributes`.`projectId`, `projects`.`name` AS `pname`, `items`.`title`, `items`.`description`, `itemstatus`.`dateCreated`, `items`.`itemId`, `itemstatus`.`dateCompleted`, `itemattributes`.`deadline`, `itemattributes`.`repeat`, `itemattributes`.`suppress`, `itemattributes`.`suppressUntil` FROM `items`, `itemattributes`, `itemstatus`, `projects`, `projectattributes`, `projectstatus`, `nextactions` WHERE `itemstatus`.`itemId` = `items`.`itemId` AND `itemattributes`.`itemId` = `items`.`itemId` AND `itemattributes`.`projectId` = `projects`.`projectId` AND `projectattributes`.`projectId`=`itemattributes`.`projectId` AND `projectstatus`.`projectId` = `itemattributes`.`projectId` AND `nextactions`.`nextaction`=`items`.`itemId` AND `itemattributes`.`type` = 'a' AND `itemattributes`.`timeframeId`='{$values['timeframeId']}' AND `projectattributes`.`isSomeday`='n' AND `itemattributes`.`contextId`='{$values['contextId']}' AND (`itemstatus`.`dateCompleted` IS NULL OR `itemstatus`.`dateCompleted` = '0000-00-00') AND (`projectstatus`.`dateCompleted` IS NULL OR `projectstatus`.`dateCompleted` = '0000-00-00') AND ((CURDATE() >= DATE_ADD(`itemattributes`.`deadline`, INTERVAL -(`itemattributes`.`suppressUntil`) DAY)) OR `projectattributes`.`suppress`='n' OR (CURDATE() >= DATE_ADD(`projectattributes`.`deadline`, INTERVAL -(`projectattributes`.`suppressUntil`) DAY))) ORDER BY `projects`.`name`",
        "getactiveitems"              =>"SELECT `itemattributes`.`contextId`, `itemattributes`.`timeframeId`, COUNT(*) AS `count`FROM `itemattributes`, `itemstatus`, `projectattributes`, `projectstatus`, `nextactions` WHERE `itemstatus`.`itemId`=`itemattributes`.`itemId` AND `projectattributes`.`projectId`=`itemattributes`.`projectId` AND `nextactions`.`nextaction` = `itemstatus`.`itemId` AND `projectstatus`.`projectId`=`projectattributes`.`projectId` AND `itemattributes`.`type`='a' AND `projectattributes`.`isSomeday`='n' AND (`itemstatus`.`dateCompleted` IS NULL OR `itemstatus`.`dateCompleted`='0000-00-00') AND (`projectstatus`.`dateCompleted` IS NULL OR `projectstatus`.`dateCompleted`='0000-00-00') AND ((CURDATE() >= DATE_ADD(`itemattributes`.`deadline`, INTERVAL -(`itemattributes`.`suppressUntil`) DAY)) OR `projectattributes`.`suppress`='n' OR (CURDATE() >= DATE_ADD(`projectattributes`.`deadline`, INTERVAL -(`projectattributes`.`suppressUntil`) DAY))) GROUP BY `itemattributes`.`contextId`, `itemattributes`.`timeframeId`",
        "getsuppresseditems"              =>"SELECT `itemattributes`.`projectId`, `projects`.`name` AS `pname`, `items`.`title`, `items`.`description`, `itemstatus`.`dateCreated`, `context`.`contextId`, `context`.`name` AS `cname`, `items`.`itemId`, `itemstatus`.`dateCompleted`, `itemattributes`.`deadline`, `itemattributes`.`repeat`, `itemattributes`.`suppress`, `itemattributes`.`suppressUntil`, `itemattributes`.`type` FROM `items`, `itemattributes`, `itemstatus`, `projects`, `projectstatus`, `context` WHERE `itemstatus`.`itemId` = `items`.`itemId` AND `itemattributes`.`itemId` = `items`.`itemId` AND `itemattributes`.`contextId` = `context`.`contextId` AND `itemattributes`.`projectId` = `projects`.`projectId` AND `projectstatus`.`projectId` = `itemattributes`.`projectId` AND (`itemstatus`.`dateCompleted` IS NULL OR `itemstatus`.`dateCompleted` = '0000-00-00') AND (`projectstatus`.`dateCompleted` IS NULL OR `projectstatus`.`dateCompleted` = '0000-00-00') AND (`itemattributes`.`suppress`='y') ORDER BY `itemattributes`.`deadline`, `cname`, `pname`",
        "getitemids"              =>"SELECT `itemattributes``.`itemid FROM `itemattributes` WHERE type = 'a'",
        //set type to variable
        "getactiveitemids"              =>"",
        "getitemsinproject"              =>"",
        "getcompleteditemsinproject"              =>"",
        "doesitemrepeat"              =>"SELECT `itemattributes``.`repeat FROM `itemattributes` WHERE `itemattributes``.`itemId=''{$values['completed']}'Na'",
        "getcompleteditemids"       => "SELECT itemstatus`.`itemId FROM itemstatus, `itemattributes` WHERE `itemattributes``.`itemId=itemstatus`.`itemId AND `itemattributes``.`type='a' AND dateCompleted >0",
        "copyitem"  =>"SELECT items`.`title, items`.`description FROM items WHERE `items`.`itemId`=''{$values['completed']}'Na'",
        "copyitemattributes" =>"SELECT `itemattributes``.``projectId`, `itemattributes``.`contextId, `itemattributes``.`timeframeId, `itemattributes``.`deadline, `itemattributes``.`repeat, `itemattributes``.`suppress, `itemattributes``.`suppressUntil FROM `itemattributes` WHERE `itemattributes``.`itemId=''{$values['completed']}'Na'",
        "copyitemstatus" =>"",

//lists
        //provide for name sort for dropdown list (categories not needed)
        "getlists"              =>"SELECT list`.`listId, list`.`title, list`.`description,list`.`categoryId, `categories`.`category` FROM list, categories WHERE list`.`categoryId=categories`.`categoryId ORDER BY `categories`.`category` ASC",
        //find  wayto merge  with above (generic problem)
        "getlistsincategory"              =>"SELECT list`.`listId, list`.`title, list`.`description, list`.`categoryId, `categories`.`category` FROM list, categories WHERE list`.`categoryId=categories`.`categoryId AND list`.`categoryId='{$values['categoryId']}' ORDER BY `categories`.`category` ASC",
        "selectlist"              =>"SELECT title, description, categoryId FROM list WHERE listId = '{$values['listId']}'",
        "newlist"              =>"",
        "updatelist"              =>"UPDATE `list` SET `title` = '{$values['newlistTitle']}', `description` = '{$values['newdescription']}', `categoryId` = '{$values['newcategoryId']}' WHERE `listId` ='{$values['listId']}'",
        "deletelist"              =>"DELETE FROM `list` WHERE `listId`='{$values['listId']}'",
        "getlistidfromitem"   =>"SELECT `listId` FROM `listItems` WHERE `listItemId`='{$values['completedLi']}'",
//listitems
        "getlistitems"              =>"SELECT listItems`.`listItemId, listItems`.`item, listItems`.`notes, listItems`.`listId FROM listItems LEFT JOIN list on listItems`.`listId = list`.`listId WHERE list`.`listId = '{$values['listId']}' AND (listItems`.`dateCompleted is not null AND listItems`.`dateCompleted='0000-00-00')",
        "selectlistitem"              =>"SELECT listItemId, item, notes, listId, dateCompleted FROM listItems WHERE listItemId = '{$values['$listItemId']}'",
        "newlistitem"              =>"INSERT INTO listItems VALUES (NULL, '{$values['item']}', '{$values['notes']}', '{$values['listId']}', 'n')",
        "updatelistitem"              =>"UPDATE `listItems` SET `notes` = '{$values['newnotes']}', `item` = '{$values['newitem']}', `listId` = '{$values['listId']}', `dateCompleted`='{$values['newdateCompleted']}' WHERE `listItemId` ='{$values['listItemId']}'",
        "deletelistitem"              =>"DELETE FROM `listItems` WHERE `listItemId`='{$values['listItemId']}'",
        "removelistitems"              =>"DELETE FROM `listItems` WHERE `listId`='{$values['listId']}'",
        "completelistitem"              =>"UPDATE `listItems` SET `dateCompleted`='{$values['date']}' WHERE `listItemId`='{$values['completedLi']}",
        "getcompletedlistitems"              =>"SELECT listItems`.`listItemId, listItems`.`item, listItems`.`notes, listItems`.`listId FROM listItems LEFT JOIN list on listItems`.`listId = list`.`listId WHERE list`.`listId = '{$values['listId']}' AND (listItems`.`dateCompleted!='0000-00-00' AND listItems`.`dateCompleted is not null)",
//checklists
        //"listchecklists"              =>"SELECT checklistId, title FROM checklist ORDER BY title",
        "getchecklists"            =>"SELECT checklist`.`checklistId, checklist`.`title, checklist`.`description, checklist`.`categoryId, `categories`.`category` FROM checklist, categories WHERE checklist`.`categoryId=categories`.`categoryId ORDER BY `categories`.`category` ASC",
//getchecklistsincategory        SELECT checklist`.`checklistId, checklist`.`title, checklist`.`description, checklist`.`categoryId, `categories`.`category` FROM checklist, categories WHERE checklist`.`categoryId=categories`.`categoryId AND checklist`.`categoryId='{$values['categoryId']}' ORDER BY `categories`.`category` ASC
        "selectchecklist"              =>"SELECT title,category, description FROM checklist WHERE checklistId='{$values['checklistId']}'",
        "newchecklist"              =>"INSERT INTO checklist VALUES (NULL, '{$values['title']}', '{$values['categoryId']}', '{$values['description']}')",
        "updatechecklist"              =>"UPDATE checklist SET title = '{$values['newchecklistTitle']}', description = '{$values['newdescription']}', categoryId = '{$values['newcategoryId']}' WHERE checklistId ='{$values['checklistId']}'",
        "deletechecklist"              =>"DELETE FROM checklist WHERE checklistId='{$values['checklistId']}'",
//checklistitems
        "getchecklistitems"              =>"SELECT checklistItems`.`checklistitemId, checklistItems`.`item, checklistItems`.`notes, checklistItems`.`checklistId, checklistItems`.`checked FROM checklistItems LEFT JOIN checklist on checklistItems`.`checklistId = checklist`.`checklistId WHERE checklist`.`checklistId = '{$values['checklistId']}' ORDER BY checklistItems`.`checked DESC, checklistItems`.`item ASC",
        "selectchecklistitem"              =>"SELECT checklistItemId, item, notes, checklistId, checked FROM checklistItems WHERE checklistItemId = '{$values['$checklistItemId']}'",
        "newchecklistitem"              =>"INSERT INTO checklistItems  VALUES (NULL, '{$values['item']}', '{$values['notes']}', '{$values['checklistId']}', 'n')",
        "updatechecklistitem"              =>"UPDATE checklistItems SET notes = '{$values['newnotes']}', item = '{$values['newitem']}', checklistId = '{$values['checklistId']}', checked='{$values['newchecked']}' WHERE checklistItemId ='{$values['checklistItemId']}'",
        "deletechecklistitem"              =>"DELETE FROM checklistItems WHERE checklistItemId='{$values['checklistItemId']}'",
        "removechecklistitems"              =>"DELETE FROM checklistItems WHERE checklistId='{$values['checklistId']}'",
        "checkchecklistitem"              =>"",
        "uncheckchecklistitem"              =>"UPDATE checklistItems SET checked='n' WHERE checklistId='{$values['checklistId']}'",
        //filtered queries?
        "getchecklistsincategory"              =>"",
//notes
        "getnotes"              =>"SELECT ticklerId, title, note, date FROM tickler WHERE (date IS NULL OR date = '0000-00-00') OR (CURDATE()<= date)",
        "selectnote"              =>"SELECT ticklerId, title, note, date FROM tickler WHERE ticklerId='{$values['noteId']}'",
        "newnote"              =>"INSERT INTO `tickler` (date,title,note) VALUES ('{$values['date']}','{$values['title']}','{$values['note']}')",
        "updatenote"              =>"UPDATE `tickler` SET `date` = '{$values['date']}', `note` = '{$values['note']}', `title` = '{$values['title']}' WHERE `ticklerId` = '{$values['noteId']}'",
        "deletenote"              =>"DELETE FROM `tickler` WHERE `ticklerId`='{$values['noteId']}'",
);
