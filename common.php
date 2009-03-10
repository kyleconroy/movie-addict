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
require('functions.php');

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

echo '<link rel="stylesheet" type="text/css" media="screen" href="'.$cssurl.'?ver=2.4" />';

// Print the
printTabs(-1, false, $appurl);
if (preg_match('/\d+/', $_GET['friend'])) {
	$friend = $_GET['friend'];
	echo "<h1 class=\"settings\"> Movies both You and  <fb:name uid=\"$friend\" useyou=\"false\" capitalize=\"true\" /> haven't seen </h1>";
	echo '<table>';
	printList($user, $friend);
} else
	echo 'Invalid friend ID';
echo '</table>';
	  


/** Return an array of the current top 250 films, and a BOOlEAN value if they have seen the movie **/
function printList($userid1, $userid2) {
	
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
	$userValues1 = userValues($db, $con, $userid1);
	$userValues2 = userValues($db, $con, $userid2);
	while ($row = mysql_fetch_array($result)){
		if($userValues1[$row[0]] + $userValues2[$row[0]] == 0) {
			echo '<tr><td>';
			printLink($row[1]);
			echo '</td></tr>';
		};
	}
	mysql_close();
	return $films;
}

?>