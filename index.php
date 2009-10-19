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
require_once 'netflix.php';
require_once 'config.php';
require_once 'addict_database.php';
require_once 'facebook.php';

$facebook = new Facebook($appapikey, $appsecret);
$user = $facebook->require_login();
$KEY =  "jrd2pte858kkj8pg3v3ukw8t";
$SECRET = "XkWQEMsHEJ";

$url = "http://api.netflix.com/catalog/titles/";
$nf = new Netflix($KEY, $SECRET);
$ad = new AddictDatabase();

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


$list = $_GET['list'];
if($list == 'afi') {
	$list = 'afi';
	$tabvalue = 1;
	$total = '100';
	$list_movies = $ad->getAFIMovies();
} 
else {
	$list = 'imdb';
	$total = '250';
	$list_movies = $ad->getIMDBMovies();
}


$user_info = $ad->getUser($user);
$user_movies = $ad->getUserMovies($user);
$user_count = $user_info['count'];
$percent = $user_info['percent'];
$total_count = 274;

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
require_once("templates/layout_index.php"); 
ob_end_flush();


// Generate the page fbml
function generateFbml($percent, $user, $appurl){
	$fbml = '<div><h1 style="text-align: center; font-size: 32px;">'.$percent.'%<h3 style="text-align: center;">addicited to film</h3><a href="http://apps.facebook.com/'.$appurl.'view.php?user='.$user.'" style="text-align: center; display: block; margin: 5px;">See <fb:name uid="'.$user.'" useyou=false capitalize="true" possessive="true" /> list</a>
	<a href="http://apps.facebook.com/'.$appurl.'" style="text-align: center; display: block; margin: 5px;">How addicted are you?</a>';
	return $fbml;
}

?>