<?php

/*
	Movie Addict - IMDB Top 250 List - Kyle Conroy
*/

/*
	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

require('config.php');

// Use the Facebook platform libraries
require_once 'facebook.php';

// Create the Facebook application
$facebook = new Facebook($appapikey, $appsecret);
$user = $facebook->require_login();

// Catch the exception that gets thrown if the cookie has an invalid session_key in it
try {
	if (!$facebook->api_client->users_isAppUser()) {
		$facebook->redirect($facebook->get_add_url());
	}
} catch (Exception $ex) {
	// this will clear cookies for your application and redirect them to a login prompt
	$facebook->set_user(null, null);
	$facebook->redirect($appcallbackurl);
}

//If a user is defined in the url, see if that user added the app
$submit = true;
if($facebook->api_client->users_isAppUser($_GET['user']) && isset($_GET['user'])) {
	$user = $_GET['user'];
	$submit = false;
}


//Retrieve data from MySQL
$filmValues = filmValues($user);
$percent = percentage($filmValues);

//Determine what message to show
$msg = $_GET['msg'];

//Generate the FBML for the profile
$fbml = generateFbml($percent, $user);
$facebook->api_client->call_method('facebook.profile.setFBML', array('uid' => $user, 'profile' => $fbml, 'profile_main' => $fbml));

//Print the page
printPage($filmValues, $percent, $user, $msg, $submit, $appurl, $cssurl);


// Functions 

function printPage($filmValues, $percent, $user, $msg, $submit, $appurl, $cssurl) {
	echo '    
	<link rel="stylesheet" type="text/css" media="screen" href="'.$cssurl.'?ver=1.8" />
	<SCRIPT type="text/javascript" src="http://ws.amazon.com/widgets/q?ServiceVersion=20070822&MarketPlace=US&ID=V20070822/US/	additofilm-20/8005/8a0479c0-7551-45e2-9593-7837a9e0b5f3"> </SCRIPT>';

	// Print Tabs
	if($submit)
		$selected = 'selected="true"';
	echo '<fb:tabs>  
		  <fb:tab-item align="right" href="http://apps.facebook.com/'.$appurl.'" title="My List" '.$selected.' />  	
		  <fb:tab-item align="right" href="http://apps.facebook.com/'.$appurl.'friends.php" title="My Friends\' Results" />
		  <fb:tab-item align="right" href="http://apps.facebook.com/'.$appurl.'invite.php" title="Invite People" />
		  </fb:tabs>';
		  
	//Print Title for User Page	   
	echo "<h1 class=\"settings\"> <fb:name uid=\"$user\" useyou=\"false\" capitalize=\"true\" /> is $percent% addicted to film </h1>";
	
	//Print Message 
	switch($msg) {
	case 1:
		echo '<h1 class="status">Your movie list has been updated</h1>';
		break;
	case 2:
		echo '<h1 class="status">Welcome to Movie Addict! Check off the movies you have seen, and then press the update button</h1>';
		break;
	default:
		break;
		}

	// Print Movie List
	echo '<fb:if-section-not-added section="profile"> 
			<div class="addmsg"><fb:add-section-button section="profile" />Your profile currently lacks a certain something</div>
		  </fb:if-section-not-added>';
	echo '<form action="updateUser.php" method="POST">';
	echo '<table>';
	foreach ($filmValues as $film) {
		if($film[2] == 1)
			$checked = 'checked="checked"';
		else 
			$checked = "";
		echo '<tr><td><input type="checkbox" ' . $checked .' name="film[' . $film[0] .']"></td>
				  <td><a target="_blank" href="http://www.amazon.com/gp/search?ie=UTF8&keywords=' . $film[1] .'&tag=additofilm-20&index=blended&linkCode=ur2&camp=1789&creative=9325">' . $film[1] .'</a><img src="http://www.assoc-amazon.com/e/ir?t=additofilm-20&l=ur2&o=1" width="1" height="1" border="0" alt="" style="border:none !important; margin:0px !important;" /></td></tr>';
	}
	echo '</table>';
	echo '<input type="hidden" name="userid" value="'. $user.'"  />';
	if($submit)
		echo '<input type="submit" value="Update Movies" />';
	echo '</form>';
	
}

function percentage($filmValues){
	$counter = 0;
	foreach ($filmValues as $film) {
		if($film[2] == 1) {
			$counter += 1;
			}
	}
	if($counter == 0)
		return $counter;
	else 
		return $counter / 250 * 100;
	
}

function generateFbml($percent, $user){
	$fbml = '<div><h1 style="text-align: center; font-size: 32px;">'.$percent.'%<h3 style="text-align: center;">addicited to film</h3><a href="http://apps.facebook.com/'.$appurl.'?user='.$user.'" style="text-align: center; display: block; margin: 5px;">See <fb:name uid="'.$user.'" useyou=false capitalize="true" possessive="true" /> list</a>
	<a href="http://apps.facebook.com/'.$appurl.'" style="text-align: center; display: block; margin: 5px;">How addicted are you?</a>';
	return $fbml;
}

/** Return an array of the current top 250 films, and a BOOlEAN value if they have seen the movie **/
function filmValues($userid) {
	
	require('config.php');
	
	$con = mysql_connect("localhost",$dbuser,$dbpass);
	if (!$con) {
	  die('Could not connect: ' . mysql_error());
	}
	$db_selected = mysql_select_db($db, $con);
	if (!$db_selected) {
	    die ('Can\'t use ' . mysql_error());
	}
	$result = mysql_query("SELECT * FROM top250");
	if(!$result) {
	   		die ('Can\'t get top movies ' . mysql_error());
	}
	
	$films = array();
	$userValues = userValues($db, $con, $userid);
	while ($row = mysql_fetch_array($result)){
		$films[] = array($row[0], $row[1], $userValues[$row[0]]);
	}
	mysql_close();
	return $films;
}

/** Return anassociative array of the films seen by the user, with booleans **/
function userValues($db, $con, $userid) {
	$db_selected = mysql_select_db($db, $con);
	$result = mysql_query("SELECT * FROM users WHERE userid = $userid");
	if(!$result) {
	   		die ('Can\'t get top movies ' . mysql_error());
	}
	return mysql_fetch_array($result, MYSQL_ASSOC);
}

?>