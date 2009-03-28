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


//Retrieve data from MySQL, first connecting to the databse
$con = mysql_connect("localhost",$dbuser,$dbpass);
if (!$con) {
  die('Could not connect: ' . mysql_error());
}
$db_selected = mysql_select_db($db, $con);
if (!$db_selected) {
    die ('Can\'t use ' . mysql_error());
}

$list = $_GET['list'];
if($list == 'afi') {
	$list = 'afi';
	$tabvalue = 1;
	$total = '100';
} 
else {
	$list = 'imdb';
	$total = '250';
}
$userfilms = userfilms($db, $con, $user, $list);
$percent = userdata($db, $con, $user);
$percent = $percent["percent"];

//Determine what message to show
$msg = $_GET['msg'];
switch($msg) {
	case 1:
		$msg = '<h1 class="status">Your movie list has been updated</h1>';
		break;
	case 2:
		$msg = '<h1 class="status">Welcome to Movie Addict! Check off the movies you have seen, and then press the update button</h1>';
		break;
	default:
		break;
}

//Generate the FBML for the profile
$fbml = generateFbml($percent, $user, $appurl);
$facebook->api_client->call_method('facebook.profile.setFBML', array('uid' => $user, 'profile' => $fbml, 'profile_main' => $fbml));

// Update the userfilms
$count = 0;
foreach($userfilms as &$film) {
	if($film['seenit'] == 1) {
		$film["checked"] =  'checked="checked"';
		$count += 1;
	}
	$film["link"] = movielink($film["title"], $film["id"]);
}
unset($film);

//Create the pageData object
$pageData = (object)(array()); 

// Save Information
$pageData->css = $cssurl;
$pageData->tabs = tabs($tabvalue, $appurl);
$pageData->msg = $msg;
$pageData->userid = $user;
$pageData->percent = $percent;
$pageData->films = $userfilms;
$pageData->movielist = $list;
$pageData->moviecount = $count;
$pageData->totalcount = $total;

// Display the Page
ob_start(); 
require("layout_index.php"); 
ob_end_flush();


// Generate the page fbml
function generateFbml($percent, $user, $appurl){
	$fbml = '<div><h1 style="text-align: center; font-size: 32px;">'.$percent.'%<h3 style="text-align: center;">addicited to film</h3><a href="http://apps.facebook.com/'.$appurl.'view.php?user='.$user.'" style="text-align: center; display: block; margin: 5px;">See <fb:name uid="'.$user.'" useyou=false capitalize="true" possessive="true" /> list</a>
	<a href="http://apps.facebook.com/'.$appurl.'" style="text-align: center; display: block; margin: 5px;">How addicted are you?</a>';
	return $fbml;
}

mysql_close();

?>