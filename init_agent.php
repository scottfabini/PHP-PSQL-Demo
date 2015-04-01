<?php  
#include file dbinfo.php specifies veriables $host, $port, $dbname, $credentials, $password.
#it must be chmod to be readable (e.g. chmod 644), so the webserver's PHP can read it.
#but making the file readable also exposes your password to anyone who can access the file.  
#further precautions may be required to protect your password. 
include ('./.dbinfo/dbinfo.php');

#HTML for the min/max entry forms
echo <<< _END
<html>
<head>
<link type="text/css" rel="stylesheet" href="stylesheet.css"/>
<title>PSQL, PHP</title>
</head>
<body>
<br>
<form action="psql_agent_raises.php">
    <input type="submit" value="Back to PSQL + PHP Interactive Website">
	</form>
<br>
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



#call the functions to give agents raises and display the table
#giveAgentsRaises($min, $max);
#getDatabaseEntries($min, $max);
initAgent();
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
      echo "Database (re)initialized successfully\n";
   }
   
}


#display the table, highlighting agents that changed with cyan background
function initAgent() {
   connectToDatabase();
   $sql =<<<EOF
	DROP TABLE agent;

	CREATE TABLE agent (agent_id Integer NOT NULL,
	first varchar(20), 
	middle varchar(20), 
	last varchar(20),
	address varchar(50),
	city varchar(20),
	country  varchar(20), 
	salary  Integer, 
	clearance_id Integer,
	PRIMARY KEY (agent_id));

	INSERT INTO Agent VALUES (1, 'Nick', 'Jim', 'Black', '44 1st Avenue', 'Athens', 'USA', 50553, 5);
	INSERT INTO Agent VALUES (2, 'Bill', NULL, 'Bundt', '34 2nd Avenue', 'Paris', 'France', 50955, 6);
	INSERT INTO Agent VALUES (3, 'Mathew', NULL, 'Cohen', '45 3rd Avenue', 'New York', 'USA', 55920, 5);
	INSERT INTO Agent VALUES (4, 'Jim', NULL, 'Cowan', '1 4th Avenue', 'Athens', 'USA', 66554, 6);
	INSERT INTO Agent VALUES (5, 'George', NULL, 'Fairley', '17 5th Avenue', 'New York', 'USA', 76396, 5);
	INSERT INTO Agent VALUES (7, 'Bill', NULL, 'Heeman', '54 6th Avenue', 'San Francisco', 'USA', 51564, 4);
	INSERT INTO Agent VALUES (8, 'Andrew', NULL, 'James', '3 7th Avenue', 'Paris', 'France', 53357, 3);
	INSERT INTO Agent VALUES (12, 'Kristin', NULL, 'Delcambre', '2-6 8th Avenue', 'Athens', 'USA', 50503, 5);
	INSERT INTO Agent VALUES (14, 'John', NULL, 'Johnston', '8 9th Avenue', 'Seattle', 'USA', 54479, 4);
	INSERT INTO Agent VALUES (20, 'George', NULL, 'Jones', '8 10th Avenue', 'Paris', 'France', 50171, 6);
	INSERT INTO Agent VALUES (21, 'Jim', NULL, 'Kieburtz', '55 11th Avenue', 'Baghdad', 'Iraq', 54492, 6);
	INSERT INTO Agent VALUES (22, 'George', NULL, 'Launchbury', '44 12th Avenue', 'Hong Kong', 'China', 54453, 2);
	INSERT INTO Agent VALUES (24, 'Chris', NULL, 'Leen', '7 13th Avenue', 'Athens', 'USA', 56719, 2);
	INSERT INTO Agent VALUES (25, 'Jim', NULL, 'Maier', '92-94 14th Avenue', 'Hong Kong', 'China', 50662, 5);
	INSERT INTO Agent VALUES (27, 'George', NULL, 'McNamee', '44 15th Avenue', 'Warsaw', 'Poland', 54453, 2);
	INSERT INTO Agent VALUES (30, 'Kristin', NULL, 'Moody', '34 16th Avenue', 'Milan', 'Italy', 54803, 5);
	INSERT INTO Agent VALUES (31, 'Lois', NULL, 'Oviat', '33 17th Avenue', 'Seattle', 'USA', 54802, 3);
	INSERT INTO Agent VALUES (33, 'Mathew', NULL, 'Pu', '18 18th Avenue', 'Athens', 'USA', 54266, 3);
	INSERT INTO Agent VALUES (35, 'Jonathan', NULL, 'Sheard', '24 19th Avenue', 'Warsaw', 'Poland', 52297, 2);
	INSERT INTO Agent VALUES (36, 'Nick', NULL, 'Steere', '15 20th Avenue', 'San Francisco', 'USA', 56702, 5);
	INSERT INTO Agent VALUES (37, 'John', NULL, 'Walpole', '4 21st Avenue', 'New York', 'USA', 54519, 6);
	INSERT INTO Agent VALUES (39, 'Nicolas', NULL, 'Barnard', '17 22nd Avenue', 'Seattle', 'USA', 55622, 2);
	INSERT INTO Agent VALUES (43, 'Jim', NULL, 'Novick', '33 23rd Avenue', 'Athens', 'USA', 54803, 6);
	INSERT INTO Agent VALUES (45, 'Pete', NULL, 'Consel', '42 24th Avenue', 'Athens', 'USA', 53612, 5);
	INSERT INTO Agent VALUES (48, 'Bill', NULL, 'Bellegarde', '27 25th Avenue', 'Seattle', 'USA', 54512, 6);
	INSERT INTO Agent VALUES (49, 'Jonathan', NULL, 'Hammerstrom', '89 26th Avenue', 'Paris', 'France', 58864, 5);
	INSERT INTO Agent VALUES (50, 'Helen', NULL, 'Hermansky', '74 27th Avenue', 'Athens', 'USA', 57574, 5);

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
   
		echo "<tr>\n<td>" . $row[0] .  "</td>
		<td>" . $row[1] . "</td>
		<td>". $row[3] . "</td>
		<td>". $row[7] . "</td></tr>";
   }
   pg_close($GLOBALS['db']);
}
?>
