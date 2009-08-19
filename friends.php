<?php

// Get the configuration variables
require_once 'config.php';
require_once 'addict_database.php';
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
unset($_friends);

$ad = new AddictDatabase();

$filmfriends = array();
foreach($friends as $friend){
	$user = $ad->getUser($friend);
	if($user) {
		$filmfriends[] = $user;
	}	
}
unset($friends);

//Create the pageData object
$pageData = (object)(array()); 

// Save Information
$pageData->css = $cssurl;
$pageData->appurl = $appurl;
$pageData->tabs = 2;
$pageData->userid = $user;
$pageData->friends = $filmfriends;

// Display the Page
ob_start(); 
require_once("templates/layout_friends.php"); 
ob_end_flush();

mysql_close();