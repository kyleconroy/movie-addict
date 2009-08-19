<?php

/* Gets the Top IMDB Movies */
function getIMDB250()
{
	$url = 'http://www.imdb.com/chart/top';
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_FAILONERROR, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 3);
	$result = curl_exec($ch);
	curl_close($ch); 
	preg_match_all('/\/title\/\w+\/">[^<>]+/', $result, $match);
	$match = $match[0];
	$movies = array();
	$counter = 1;
	foreach($match as $str){
		$movie_id = (int) substr($str, 9, 7);
		$movies[$movie_id] = array('id' => $movie_id, 'title' => substr($str, 19), 'imdb_rank' => $counter++);
	}
	return $movies;
}

/* Return on array of movies from a file list */
function getMoviesFile($file){
	$movies = array();
	$fp = fopen($file,"r");
	$counter = 1;
	while(($title = fgets($fp)) && $counter < 2){
		$movie_id = (int) findimdbid($title);
		$movies[$movie_id] = array('id' => $movie_id, 'title' => trim($title), 'rank' => $counter++);
	}
	fclose($fp);
	return $movies;
}

function findimdbid($search) {	
	$search = str_replace(" ", "+", $search);
	$url = "http://www.google.com/search?hl=en&q=site%3Aimdb.com+$search";
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_FAILONERROR, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 3);
	$result = curl_exec($ch);
	curl_close($ch); 
	preg_match('/\/title\/\w+\//', $result, $match);
	$match = substr($match[0], 9, 7);
	return $match;
}
