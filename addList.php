<?php

/** Use this file to add another list into the database. 
	The given filename is assumed to be in the correct order
	$listname should be the organzation that produced the list
	such as afi, or imdb **/

require_once "scrape.php";
require_once "addict_database.php";

$ad = new AddictDatabase();
$afi100 = readList("lists/afi_ids.txt");
addList($afi100);
	
function addList($list) {
	global $ad;
	$current = $ad->getMovies();
	$ad->resetAFIRank();
	foreach ($list as $movie) {
		if (in_array($movie['id'], array_keys($current))) {
	   		$ad->updateAFIRank($movie['id'], $movie['rank']);
	   		echo "Updated " . $movie['id'] . " to #" . $movie['rank'] . "<br>";
	 	} else {
			$ad->addMovie($movie['id'], $movie['title'], NULL, $movie['rank']);
			echo "Added " . $movie['id'] . " as #" . $movie['rank'] . "<br>";
	 	}
	}
}

function readList($filename){
	$fp = fopen($filename, "r");
	$movies = array();
	$count = 1;
	while($line = fgets($fp)){
		$movie = array();
		$movie['id'] = (int) $line;
		$movie['rank'] = $count++;
		$movies[] = $movie;
	}
	return $movies;
}
?>