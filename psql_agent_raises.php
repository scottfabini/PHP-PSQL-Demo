<?php  
/**
 * File: psql_agent_raises.php
 * Author: Scott Fabini
 * Version: 0.1
 * Date: 4/1/2015
 */

include ('./.dbinfo/dbinfo.php');
#include file dbinfo.php specifies veriables $host, $port, $dbname, $credentials, $password.
#it must be chmod to be readable (e.g. chmod 644), so the webserver's PHP can read it.
#but making the file readable also exposes your password to anyone who can access the file.  
#further precautions may be required to protect your password. 
#basic format/syntax of dbinfo.php:
/*
<?php
$host = "host=<enter_db_host_name>.cat.pdx.edu";
$port = "port=5432";
$dbname      = "dbname=<enter_username>";
$credentials = "user=<enter_username>";
$password = "password=<enter_db_password>";
?>
*/

# HTML for the min/max entry forms
echo <<< _END
<html>
<head>
<link type="text/css" rel="stylesheet" href="stylesheet.css"/>
<title>PSQL, PHP</title>
</head>
<body>
<h2>Demo of HTML Forms, PHP, and PSQL ALTER and SELECT-FROM-WHERE queries</h2>
<p>Below, you see a database table of agents from a spy database.  We will give a range of agents a $1000 raise
<br>
Enter a minimum and maximum agent_id that you would like to give a $1000 raise to.</p>
<form method="post" action="psql_agent_raises.php">
	<p>Enter a minimum agent_id:
	<input type="text" name="min" size="6" maxlength="4">
	</p>
	<p>Enter a maximum agent_id:
	<input type="text" name="max" size="6" maxlength="4">
	</p>	
	<input type="submit">
</form>
<form action="init_agent.php">
    <input type="submit" value="(Re)initialize agent database">
</form>
<p>Please feel free to use the underlying code to create your own interactive websites!</p>
</body>
</body>
</html>
_END;
#end of HTML section

#
#Begin PHP section.  
#

#enable error reporting
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);
date_default_timezone_set('America/Los_Angeles');

#initialize database connection
#variables host, port, dbname, credentials, password must be specified
#in 'include' file above (default: .dbinfo/dbinfo.php)
$db = pg_connect( "$host $port $dbname $credentials $password"  );


# Get the submitted form data. Sanitize the input and assign to min/max variables.  Display the values entered
if (isset($_POST['min']) && is_numeric($_POST['min'])) 
{
	$min = sanitizeString($_POST['min']);
	echo "Minimum value entered: "; 
	echo $min;
	echo "<br>";
}
else 
{	
	$min = "0";
}
if (isset($_POST['max']) && is_numeric($_POST['max'])) 
{
	$max = sanitizeString($_POST['max']);
	echo "Maximum value entered: "; 
	echo $max;
	echo "<br>";
}
else 
{
	$max = "0";
}

#call the functions to give agents raises and display the table
giveAgentsRaises($min, $max);
getDatabaseEntries($min, $max);
#end PHP 'main'

#
#PHP functions
#

#connect to the database
function connectToDatabase(){
   if(!$GLOBALS['db']){
      echo "Error : Unable to open database\n";
   } 
   else {
      echo "Opened database successfully\n";
   }
   
}

#sanitize input strings
function sanitizeString($var)
{
	$var = stripslashes($var);
	$var = strip_tags($var);
	$var = htmlentities($var);
	return $var;
}

#further sanitize for postgres
function sanitizePSQL($connection, $var)
{
	$var = pg_escape_string($connection, $var);
	$var = sanitizeString($var);
	return $var;
}

#give the agents their raises.  
#note that this function is only called if we received $_POST for submissions
#for min/max values
function giveAgentsRaises($minimum, $maximum){
	
	if (isset($_POST['min']) && isset($_POST['max']))
	{ 
		connectToDatabase();
		$update =<<<EOF2
		UPDATE agent SET salary=salary+1000 WHERE agent_id >= '$minimum' AND agent_id <= '$maximum';
EOF2;
   		$ret = pg_query($GLOBALS['db'], $update);
   		if(!$ret){
      	echo pg_last_error($GLOBALS['db']);
      	exit;
   		} 
	}
}

#display the table, highlighting agents that changed with cyan background
function getDatabaseEntries($minimum, $maximum){  
   connectToDatabase();
   $sql =<<<EOF
	  SELECT * from agent
	  ORDER BY agent_id;
EOF;
   echo "<table border='1'> <tr>
   <th>ID</th>
   <th>First name</th>
   <th>Last name</th>
   <th>Salary</th>
  </tr>";

   $ret = pg_query($GLOBALS['db'], $sql);
   if(!$ret){
      echo pg_last_error($GLOBALS['db']);
      exit;
   } 
   while($row = pg_fetch_row($ret)){
    if($row[0] >= $minimum && $row[0] <= $maximum){
		echo "<tr style = 'background-color: cyan; border: 5px solid red;'>\n<td>" . $row[0] .  "</td>
		<td>" . $row[1] . "</td>
		<td>". $row[3] . "</td>
		<td>". $row[7] . "</td></tr>";
		}else{		
		echo "<tr>\n<td>" . $row[0] .  "</td>
		<td>" . $row[1] . "</td>
		<td>". $row[3] . "</td>
		<td>". $row[7] . "</td></tr>";
		}
     
   }
   pg_close($GLOBALS['db']);
}
?>



