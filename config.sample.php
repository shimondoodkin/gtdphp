<?php
	$host = 'localhost';
	$db = 'gtd';
	$user = '<change>';
	$pass = '<change>';
	$title = 'GTD';

        $theme = "default";



//Configuration settings

        $config = array(
                "db"                  => "mysql",
                "debug"               => "true",
                "contextsummary"      => "all"  //all | nextaction

//Other configuration settings....
                );



//Default sort order for each query (can be easily overridden within each page...)
//Once all built, can be either (a) simplified for user-editing, (b) create an options page that alters the config file, or (c) placed in the database and options page employed [best option?]

//simplify all options down to a few...

        $sort = array(

            "projectssummary"       => "`projects`.`name` ASC",
            "spacecontextselectbox"          => "`context`.`name` ASC",
            "categoryselectbox"             => "`categories`.`category` ASC",
            "projectselectbox"          => "`projects`.`name` ASC",
            "timecontextselectbox"       => "`timeitems`.`timeframe` DESC"


                );
?> 

