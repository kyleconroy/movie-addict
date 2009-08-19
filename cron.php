<?php

require_once "netflix.php";
require_once "scrape.php";
require_once "addict_database.php";
require_once "config.php";

//Title Change
$dictionary = array(
	'Il buono, il brutto, il cattivo.' => 'The Good, the Bad and the Ugly',
	'Shichinin no samurai' => 'The Seven Samurai',
	"C&#x27;era una volta il West" => 'Once Upon a Time in the West',
	'Cidade de Deus' => 'City of God',
	'L&#xE9;on' => 'The Professional',
	'Le fabuleux destin d&#x27;Am&#xE9;lie Poulain' => 'Am&#xE9;lie',
	'Das Leben der Anderen' => 'The Lives of Others',
	'Sen to Chihiro no kamikakushi' => 'Spirited Away',
	'El laberinto del fauno' => 'Pan\'s Labyrinth',
	'Der Untergang' => 'Downfall',
	'La vita &#xE8; bella' => 'Life Is Beautiful',
	'Nuovo cinema Paradiso' => 'Cinema Paradiso',
	'Ladri di biciclette' => 'The Bicycle Thief',
	'Det sjunde inseglet' => 'The Seventh Seal',
	'Per qualche dollaro in pi&#xF9;' => 'For a Few Dollars More',
	'Smultronst&#xE4;llet' => 'Wild Strawberries',
	'Les diaboliques' => 'Diabolique',
	'Le notti di Cabiria' => 'Nights of Cabiria',
	'Le salaire de la peur' => 'The Wages of Fear',
	'Amores perros' => 'Love\'s a Bitch',
	'L&#xE5;t den r&#xE4;tte komma in' => 'Let the Right One In',
	'Hotaru no haka' => 'Grave of the Fireflies',
	'La battaglia di Algeri' => 'The Battle of Algiers',
	'Mou gaan dou' => 'Infernal Affairs',
	'Nosferatu, eine Symphonie des Grauens' => 'Nosferatu the Vampire',
	'Wo hu cang long' => 'Crouching Tiger, Hidden Dragon',
	'Le scaphandre et le papillon' => 'The Diving Bell and the Butterfly',
	'Mononoke-hime' => 'Princess Mononoke',
);

$ad = new AddictDatabase("brokenva_addicttest");
$nf = new Netflix($netflixKey, $netflixSecret);
$top250 = getIMDB250();
updateTop250($top250);

/** Update the top 250 table **/

function updateTop250($top250) {
	global $ad;
	global $dictionary;
	$current = $ad->getMovies();
	$ad->resetIMDBRank();
	$ad->resetNetflixInstant();
	foreach ($top250 as $movie) {
	    $nftitle = $movie['title'];
		if (in_array($movie['id'], array_keys($current))) {
	   		$ad->updateIMDBRank($movie['id'], $movie['imdb_rank']);
	   		echo "Updated '" . $movie['title'] . "' to #" . $movie['imdb_rank'] . "<br>";
	 	} else {
			$ad->addMovie($movie['id'], $movie['title'], $movie['imdb_rank']);
			echo "Added '" . $movie['title'] . "' as #" . $movie['imdb_rank'] . "<br>";
	 	}
	 	if (in_array(utf8_decode($movie['title']), array_keys($dictionary))) {
	 		$ad->updateMovieTitle($movie['id'], $dictionary[$movie['title']]);
	 		$nftitle = $dictionary[$movie['title']];
	 		echo "Updated " . $movie['title'] . " to " . $dictionary[$movie['title']] . "<br>";
	 	}
        if($xml = netflixLookup($nftitle)) {
            if(canWatchInstant($xml)) {
                $ad->updateNetflixInstant($movie['id'], true);
                echo "You can now watch $nftitle instantly! <br>";
            }
            if($nfid = netflixId($xml))
                $ad->updateNetflixId($movie['id'], $nfid);
        }
        usleep(300);
	}
}


function netflixId($xml){
    $url = array_shift($xml->xpath("/catalog_titles/catalog_title[1]"))->id;
    if(preg_match('/\d+/', $url, $matches))
        return $matches[0];
    return false;
}

function canWatchInstant($xml){
    $format = array_shift($xml->xpath("/catalog_titles/catalog_title[1]/
                link[@title='formats']/delivery_formats/availability/category[@term='instant']"));
    if($format)
        return true;
    return false; 
}

function netflixLookup($title){
    global $nf;
    global $netflixUrl;
    $params = array("term" => $title, "expand"=>"formats");
    $result = $nf->request($netflixUrl, "GET", $params);
    if(!$result)
        return false;
    return simplexml_load_string($result);
}

?>