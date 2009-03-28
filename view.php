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
require('libfunction.php');

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

/*
// This page will go into a completley different page, called view
//If a user is defined in the url, see if that user added the app
$submit = true;
if($facebook->api_client->users_isAppUser($_GET['user']) && isset($_GET['user'])) {
	$user = $_GET['user'];
	$submit = false;
}
*/


$user = $_GET['user'];
if(count($users) > 1)
	die("Too many arguments");

try {
	$facebook->api_client->users_getInfo($user, "name");
} catch (Exception $ex) {
	die("invalid user id");
}

//Retrieve data from MySQL, first connecting to the databse
$con = mysql_connect("localhost",$dbuser,$dbpass);
if (!$con) {
  die('Could not connect: ' . mysql_error());
}
$db_selected = mysql_select_db($db, $con);
if (!$db_selected) {
    die ('Can\'t use ' . mysql_error());
}

$imdbfilms = userfilms($db, $con, $user, 'imdb');
$afifilms = userfilms($db, $con, $user, 'imdb');
$percent = userdata($db, $con, $user);
$percent = $percent["percent"];

//Create the pageData object
$pageData = (object)(array()); 

// Save Information
$pageData->css = $cssurl;
$pageData->tabs = tabs(2, $appurl);
$pageData->userid = $user;
$pageData->percent = $percent;
$pageData->films = $imdbfilms;

// Display the Page
ob_start(); 
require("layout_view.php"); 
ob_end_flush();


// Generate the page fbml
function generateFbml($percent, $user){
	$fbml = '<div><h1 style="text-align: center; font-size: 32px;">'.$percent.'%<h3 style="text-align: center;">addicited to film</h3><a href="http://apps.facebook.com/'.$appurl.'?user='.$user.'" style="text-align: center; display: block; margin: 5px;">See <fb:name uid="'.$user.'" useyou=false capitalize="true" possessive="true" /> list</a>
	<a href="http://apps.facebook.com/'.$appurl.'" style="text-align: center; display: block; margin: 5px;">How addicted are you?</a>';
	return $fbml;
}

mysql_close();

?>