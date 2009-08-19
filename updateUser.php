<?php

// Get the configuration variables
require_once 'addict_database.php';
require_once 'config.php';

$ad = new AddictDatabase();

$user_id = $_POST['userid'];
$on_movies = $_POST['film'];
$checked_movies = array();
$list = $_POST['list'];
$previous_movies = $ad->getUserMovies($user_id);

if($list == "imdb"){
	$list_films = array_keys($ad->getIMDBMovies());
} else {
	$list_films = array_keys($ad->getAFIMovies());
}

foreach($on_movies as $key => $value)
	$checked_movies[] = (int) $key;

foreach($checked_movies as $movie_id){
	if(!in_array($movie_id, $previous_movies))
		$ad->addUserMovie($user_id, $movie_id);
}

foreach($previous_movies as $movie_id){
	if(!is_null($checked_movies) && !in_array($movie_id, $checked_movies) && in_array($movie_id, $list_films))
		$ad->deleteUserMovie($user_id, $movie_id);
}

$ad->updateUserMovieCount($user_id);


echo '<fb:redirect url="http://apps.facebook.com/'.$appurl.'?msg=1" />';

?>