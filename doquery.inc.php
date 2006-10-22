<?php

//doquery function
//SQL abstraction layer

//for testing only; duplicated in page display code
include_once("config.php");
include_once("header.php");

$connection = mysql_connect($host, $user, $pass) or die ("Unable to connect");
mysql_select_db($db) or die ("Unable to select database!");

//doquery function, final file

function doquery($querylabel,$options=NULL,$values=NULL) {

    //testing passed variables
    echo "<p>Query label: ".$querylabel."<br />";
    echo "Options: ";
    print_r($options);
    echo "<br />Values: ";
    print_r($values);
    echo "</p>";

    //sanitize input variables
    echo "<p>Sanitizing...</p>\n";

    //testing after sanitization
    echo "<p>Query label: ".$querylabel."<br />";
    echo "Options: ";
    print_r($options);
    echo "<br />Values: ";
    print_r($values);
    echo "</p>";

    //parse options array (logic)
        //sort order
        //single NA or not?
        //others

    //include correct SQL query library as chosen in config
    switch ($options['db']) {
        case "frontbase":include_once("frontbase.inc.php");
        break;
        case "msql":include_once("msql.inc.php");
        break;
        case "mysql":include_once("mysql.inc.php");
        break;
        case "mssql":include_once("mssql.inc.php");
        break;
        case "postgres":include_once("postgres.inc.php");
        break;
        case "sqlite":include_once("sqlite.inc.php");
        break;
        }


    //grab additional query strings required by options
    //?requires a lot of logic-- need to switch/automate


    //construct SQL query from parts plus values
    //automatic?

    //grab correct query string from query library array
    //values automatically inserted into array

    $query = $sql[$querylabel];
    echo "<p>Query: ".$query."</p>";

    //perform query
    switch($options['db']){
        case "frontbase":$reply = fbsql_query($query) or die ($options['debug']==1 ? "Error in query: ". $querylabel."<br />".mysql_error():"Error in query");
        break;
        case "msql":$reply = msql_query($query) or die ($options['debug']==1 ? "Error in query: ". $querylabel."<br />".mysql_error():"Error in query");
        break;
        case "mysql":$reply = mysql_query($query) or die ($options['debug']==1 ? "Error in query: ". $querylabel."<br />".mysql_error():"Error in query");
        break;
        case "mssql":$reply = mssql_query($query) or die ($options['debug']==1 ? "Error in query: ". $querylabel."<br />".mysql_error():"Error in query");
        break;
        case "postgres":$reply = pg_query($query) or die ($options['debug']==1 ? "Error in query: ". $querylabel."<br />".mysql_error():"Error in query");
        break;
        case "sqlite":$reply = sqllite_query($query)  or die ($debug==1 ? "Error in query: ". $querylabel."<br />".mysql_error():"Error in query");
        break;
        }

    //parse result into multitdimensional array $result[row#][field name] = field value
    if (mysql_num_rows($reply)>0) {
        $i = 0;
       while ($field = mysql_fetch_field($reply)) {
            /* Create an array $fields which contains all of the column names */
            $fields[$i] = $field->name;
            $i++;
            }
        $ii = 0;
        while ($mysql_result = mysql_fetch_array($reply)) {
            /*populate array with result data */
            foreach ($fields as $value) {
                $result[$ii][$value] = $mysql_result[$value];
                }
            $ii++;
            }

        }

    else $result=-1;

    //need to return error-handler if query doesn't work, main script has to know and be able to adjust
    //error codes:
    //-1: empty result set
    return $result;
    }


//test function
//sample code for pages

//set options; some  from config file, can dynamically set $options[sort] as needed
//also could split into $config[db], $config[debug] and $options[sort]
$options=array(
"db" => "mysql",
"debug" => 1,
"sort" => "category ASC"
);

//set values for query
$values=array(
"itemId" => 2
);

//perform query
$result = doquery("selectitem",$options,$values);


//testing code
echo "<hr /><p>Result Array: <br />";
print_r($result);
echo "</p><hr />";


//page display code with empty result handler
//need error handler? (vs in function)

if ($result!=-1) {
    //replace while (mysql_fetch_assoc) statement with foreach
    foreach ($result as $row) {

        //use expected field names (no changes needed to current code)
        echo $row['itemId']."->".$row['title']." (".$row['description'].")<br />";
        }
    }
else echo "Nothing found.";

?>
