<?php

// Get the configuration variables
require_once('config.php');
require_once('libfunction.php');

// Use the Facebook platform libraries
require_once 'facebook.php';

// Create the Facebook application
$facebook = new Facebook($appapikey, $appsecret);
$user = $facebook->require_login();

// Retrieve array of friends who've already added the app. 
$fql = 'SELECT uid FROM user WHERE uid IN (SELECT uid2 FROM friend WHERE uid1='.$user.') AND is_app_user = 1'; 
$_friends = $facebook->api_client->fql_query($fql); 

// Extract the user ID's returned in the FQL request into a new array. 
$friends = array(); 
if (is_array($_friends) && count($_friends)) { 
	foreach ($_friends as $friend) { 
		$friends[] = $friend['uid']; 
		}
} 

$con = mysql_connect("localhost",$dbuser,$dbpass);

if (!$con) {
  die('Could not connect: ' . mysql_error());
}

$db_selected = mysql_select_db($db, $con);

if (!$db_selected) {
    die ('Can\'t use ' . mysql_error());
}

$filmfriends = array();
foreach($friends as $friend){
	$result = mysql_query("SELECT percent FROM users WHERE userid = '$friend'") or
		die ('Can\'t update value ' . mysql_error());;
	$row = mysql_fetch_assoc($result);
	if($row) {
		$filmfriends[] = array("userid" => $friend, "percent" => $row["percent"]);
	}	
}

//Create the pageData object
$pageData = (object)(array()); 

// Save Information
$pageData->css = $cssurl;
$pageData->appurl = $appurl;
$pageData->tabs = tabs(2, $appurl);
$pageData->userid = $user;
$pageData->friends = $filmfriends;

// Display the Page
ob_start(); 
require("layout_friends.php"); 
ob_end_flush();

mysql_close();