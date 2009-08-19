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

require_once 'config.php';
require_once 'addict_database.php';

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
$users = array_keys($_GET['user']);
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

$ad = new AddictDatabase();


// Update the userfilms
$movies = $ad->getRankedMovies();

$unseenids = array();
foreach($users as $user){
	$unseenids = array_merge($ad->getUserMovies($user), $unseenids);
}
$unseen = array();
$unseenids = array_unique($unseenids);
foreach($movies as $key => $value){
	if(!in_array($key, $unseenids))
		$unseen[] = $value;
}
	
//Create the pageData object
$pageData = (object)(array());


// Save Information
$pageData->css = $cssurl;
$pageData->tabs = 2;
$pageData->users = $users;
$pageData->films = $unseen;

// Display the Page
ob_start(); 
require_once("templates/layout_common.php"); 
ob_end_flush();

?>