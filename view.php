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

$user = $_GET['user'];
if(preg_match('/\d+/', $user))
	$user = (int) $user;
else {
	echo '<h1 class="error">Invalid User ID</h1>';
	die();
}

$ad = new AddictDatabase();


/*
// This page will go into a completley different page, called view
//If a user is defined in the url, see if that user added the app
$submit = true;
if($facebook->api_client->users_isAppUser($_GET['user']) && isset($_GET['user'])) {
	$user = $_GET['user'];
	$submit = false;
}
*/

$list = $_GET['list'];
if($list == 'afi') {
	$list = 'afi';
	$tabvalue = 8;
	$total = '100';
	$list_movies = $ad->getAFIMovies();
} 
else {
	$list = 'imdb';
	$total = '250';
	$tabvalue = 9;
	$list_movies = $ad->getIMDBMovies();
}


$user_info = $ad->getUser($user);
$user_movies = $ad->getUserMovies($user);
$user_count = $user_info['count'];
$total_count = 274;
$percent = $user_info['percent'];

//Determine what message to show


//Create the pageData object
$pageData = (object)(array()); 

// Save Information
$pageData->css = $cssurl;
$pageData->tabs = $tabvalue;
$pageData->appurl =  $appurl;
$pageData->msg = $msg;
$pageData->userid = (int) $user;
$pageData->percent = $percent;
$pageData->movielist = $list;
$pageData->movies = $list_movies;
$pageData->seen_movies = $user_movies;
$pageData->moviecount = $user_count;
$pageData->totalcount = $total_count;

// Display the Page
ob_start(); 
require_once("templates/layout_view.php"); 
ob_end_flush();


?>