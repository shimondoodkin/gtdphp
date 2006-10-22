<?php
// turn into a $connect array
        $theme = "default";



//Configuration settings

        $config = array(

            //connection information
                "host" = 'localhost';
                "db" = 'testgtd';
                "user" = 'taskman';
                "pass" = '90qaXPQ1Z71';

            //database information
                "dbtype"                  => "mysql",

            //user preferences
                "debug"               => 'developer',  // false | true |  developer
                "theme" = 'default',
                "contextsummary"      => 'all',  //all | nextaction
                );


//Default sort order for each query (can be easily overridden within each page...)
//Once all built, can be either (a) simplified for user-editing, (b) create an options page that alters the config file, or (c) placed in the database and options page employed [best option?]

//simplify all options down to a few...

        $sort = array(

            "projectssummary"       => "`projects`.`name` ASC",
            "spacecontextselectbox"          => "`context`.`name` ASC",
            "categoryselectbox"             => "`categories`.`category` ASC",
            "projectselectbox"          => "`projects`.`name` ASC",
            "timecontextselectbox"       => "`timeitems`.`timeframe` DESC",
            "selectactiveprojects"      => "`categories`.`category`, `projectattributes`.`deadline`, `projects`.`name` ASC",
            "getlistitems"                  => "`listItems`.`item` ASC",
            "getcompletedlistitems" => "`listItems`.`dateCompleted` ASC",

                );
?>