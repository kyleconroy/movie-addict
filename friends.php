<?php

// Get the configuration variables
require_once('config.php');

// Use the Facebook platform libraries
require_once 'facebook.php';

// Create the Facebook application
$facebook = new Facebook($appapikey, $appsecret);
$user = $facebook->require_login();
echo '<link rel="stylesheet" type="text/css" media="screen" href="http://apps.facebook.com/'.$appurl.'top250.css?ver=2.0" />';
echo '<fb:tabs>  
	  <fb:tab-item align="right" href="http://apps.facebook.com/'.$appurl.'" title="My List" />  	
	  <fb:tab-item align="right" href="http://apps.facebook.com/'.$appurl.'friends.php" title="My Friends\' Results" selected="true"/>
	  <fb:tab-item align="right" href="http://apps.facebook.com/'.$appurl.'invite.php" title="Invite People" />
	  </fb:tabs>';

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
print_r($row);

echo '<table cellpadding="0" cellspacing="0" class="friends">';
foreach($friends as $friend){
	$result = mysql_query("SELECT percent FROM users WHERE userid = '$friend'") or
		die ('Can\'t update value ' . mysql_error());;
	$row = mysql_fetch_assoc($result);
	if($row) {
	echo '<tr>
			<td class="img"><fb:profile-pic uid="'.$friend.'" linked="true" size="q" /></td>
			<td class="percent">'.$row["percent"].'%</td>
			<td class="name"><fb:name uid="'.$friend.'" capitalize="true" /></td>
			<td class="list"><a href="http://apps.facebook.com/'.$appurl.'?user='.$friend.'">
				See <fb:name uid="'.$friend.'" capitalize="true" possessive="true"/> list</a>
			</td>
		  </tr>';
	}	
}
echo '</table>';