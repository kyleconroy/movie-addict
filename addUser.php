<?php

// Get the configuration variables
require_once('config.php');

// Use the Facebook platform libraries
require_once 'facebook.php';

// Create the Facebook application
$facebook = new Facebook($appapikey, $appsecret);
$user = $facebook->require_login();

$con = mysql_connect("localhost",$dbuser,$dbpass);

if (!$con) {
  die('Could not connect: ' . mysql_error());
}

$db_selected = mysql_select_db($db, $con);

if (!$db_selected) {
    die ('Can\'t use ' . mysql_error());
}

// Insert the user into the table
$result = mysql_query("SELECT * FROM users WHERE userid = '$user'") or
		die ('Can\'t select ' . mysql_error());
$row = mysql_fetch_array($result);		
		
if(!$row) {
	mysql_query("INSERT INTO users (userid,percent) VALUES('$user',0)") or
		die ('Can\'t insert ' . mysql_error());
}

// Check if any movies are from the user's profile are in the top 250 and update them
$user_details = $facebook->api_client->users_getInfo($user, array('movies'));
$user_movies = explode(', ', $user_details[0]['movies']);

$result = mysql_query("SELECT * FROM top250") or
		die ('Can\'t select ' . mysql_error());;

while($row = mysql_fetch_array($result)) {
	if(in_array($row['title'], $user_movies)) {
		$imdbid = $row['imdbid'];
		mysql_query("UPDATE users SET `$imdbid` = 1 WHERE userid = '$user'") or
			die ('Can\'t update value ' . mysql_error());
	}
}

mysql_close(); 

echo '<fb:redirect url="http://apps.facebook.com/'.$appurl.'?msg=2" />';

?>