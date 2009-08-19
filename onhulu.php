<?php

require 'hulu.php';
require 'addict_database.php';

$hulu = new Hulu();
$ad = new AddictDatabase();

$movies = $ad->getMovies();
foreach($movies as $movie){
    if($hulu->available($movie['title']))
        echo $movie['title'] . "<br>";
}

?>