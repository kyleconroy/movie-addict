<?php

require_once 'config.php';
require_once 'addict_database.php';

$list = "<table>";
$list .= "<thead><td>Rank</td><td>Title</td></thead>";
$ad = new AddictDatabase();

foreach($ad->getMovies() as $movie){
    if($movie['instant']){
       $list .= "<tr><td>".$movie['imdb_rank']."</td><td><a href=\"http://www.netflix.com/WiPlayer?movieid=".$movie['netflix_id']."\">".$movie['title']."</a></td></tr>";     
    }
}

$list .= "</table>";

?>

<html>
<body>
<h1>IMDB Top 250 + Netflix Watch Instantly = Crazy Delicious</h1>
<?php echo $list ?>
</body>
</html>