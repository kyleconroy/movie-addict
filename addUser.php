<?php

// Get the configuration variables
require_once 'config.php';

// Use the Facebook platform libraries
require_once 'facebook.php';

// Create the Facebook application
$facebook = new Facebook($appapikey, $appsecret);
$user = $facebook->require_login();

// Connect to the database
$ad = new AddictDatabase();

// Insert the user into the table only if he / she isn't already there
if(!$ad->getUser($user))
	$ad->addUser($user);

// Check if any movies are from the user's profile are in the top 250 and update them
$user_details = $facebook->api_client->users_getInfo($user, array('movies'));
$user_movies = explode(', ', $user_details[0]['movies']);

$allmovies = $ad->getMovies();

foreach($allmovies as $movie)
	if(in_array($movie['title'], $user_movies)) {
		$ad->addUserMovie($user, $movie['movie_id']);
	}
}

$ad->updateUserMovieCount($user);

mysql_close(); 

echo '<fb:redirect url="http://apps.facebook.com/'.$appurl.'?msg=2" />';

?>