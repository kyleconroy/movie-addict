<?php

// Get the configuration variables
require_once('config.php');

$con = mysql_connect("localhost",$dbuser,$dbpass);

if (!$con) {
  die('Could not connect: ' . mysql_error());
}

$db_selected = mysql_select_db($db, $con);

if (!$db_selected) {
    die ('Can\'t use ' . mysql_error());
}

$userid = $_POST['userid'];
$checked = $_POST['film'];

// Get top 250 list
$result = mysql_query("SELECT * FROM top250");
	if(!$result) {
	   		die ('Can\'t get top movies ' . mysql_error());
	}

$count = 0;	
// update BOOL values
while ($row = mysql_fetch_array($result)) {
	$imdbid = $row["imdbid"];
	if($checked["$imdbid"] == 'on') {
		mysql_query("UPDATE users SET `$imdbid` = 1 WHERE userid = '$userid'") or
			die ('Can\'t update value ' . mysql_error());
		$count += 1;
	} else {
		mysql_query("UPDATE users SET `$imdbid` = 0 WHERE userid = '$userid'") or
			die ('Can\'t update value ' . mysql_error());
	}
}

if($count == 0)
	$percent = $count;
else 
	$percent = $count / 250 * 100;

mysql_query("UPDATE users SET percent = '$percent' WHERE userid = '$userid'") or
		die ('Can\'t update value ' . mysql_error());

mysql_close(); 

echo '<fb:redirect url="http://apps.facebook.com/'.$appurl.'?msg=1" />';

?>
*/