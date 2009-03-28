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

// Error Handling
$users = $_GET['user'];
if(count($users) < 2)
	die("Too few arguments");

$str = "";
foreach($users as $key => $value);
	$str .= "$key,";

try {
	$facebook->api_client->users_getInfo($str, "name");
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
	
//$users = array('3432235','639076927','30509961');
$allfilms = films($db, $con, NULL);

$usersdata = array();
foreach($users as $key => $value) {
		$userdata[] = userdata($db, $con, $key);
}

$seen = moviestatus($userdata, $allfilms);

// Update the userfilms
$unseen = array();
foreach($allfilms as &$film) {
	if(!$seen[$film["id"]]) {
		$film["link"] = movielink($film["title"], $film["id"]);
		$unseen[] =  $film;
	}
}
unset($film);
	
//Create the pageData object
$pageData = (object)(array());


// Save Information
$pageData->css = $cssurl;
$pageData->tabs = tabs(2, $appurl);
$pageData->users = $users;
$pageData->films = $unseen;

// Display the Page
ob_start(); 
require("layout_common.php"); 
ob_end_flush();


mysql_close();

/** OR the values of the given arrays **/
function moviestatus($users, $movies) {
	$result = array();
	foreach($movies as $value){
		$result[$value["id"]] = orarray($value["id"], $users);
	}
	return $result;
}

function orarray($index, $arrays) {
	$bool = FALSE;
	foreach($arrays as $array){
		$bool = $bool || $array[$index] > 0;
	}
	return $bool;
}

?>