<?php

// Database

/** Return an array of all the film ids **/
/* An associative array of the Top 250 Films */
function filmids($db, $con) {
	$db_selected = mysql_select_db($db, $con);
	if (!$db_selected) {
	    die ('filmids: Can\'t use ' . mysql_error());
	}
	$result = mysql_query("SELECT * FROM top250");
	if(!$result) {
	   		die ('filmids: Can\'t get top movies ' . mysql_error());
	}
	$films = array();
	while ($row = mysql_fetch_array($result)){
		$films[] = intval($row[0]);
	}
	return $films;
}

/** Return an array of the all the films, or the films on a specific list **/
function films($db, $con, $list) {
	$db_selected = mysql_select_db($db, $con);
	if (!$db_selected) {
	    die ('films: Can\'t use ' . mysql_error());
	}
	if($list == NULL)
		$result = mysql_query("SELECT * FROM top250");
	else
		$result = mysql_query("SELECT * FROM top250 WHERE $list IS NOT NULL ORDER BY '$list'");
	if(!$result) {
	   		die ('films: Can\'t get top movies ' . mysql_error());
	}
	$films = array();
	while ($row = mysql_fetch_array($result)){
		$films[] = array('id'=>$row[0], 'title'=>$row[1], 'imdb'=>$row[2], 'afi'=>$row[3]);
	}
	return $films;
}

/** Return an array of the current top 250 films, and a BOOlEAN value if they have seen the movie **/
function userfilms($db, $con, $userid, $list) {
	$db_selected = mysql_select_db($db, $con);
	if (!$db_selected) {
	    die ('userfilms: Can\'t use ' . mysql_error());
	}
	if($list == NULL)
		$result = mysql_query("SELECT * FROM top250");
	else
		$result = mysql_query("SELECT * FROM top250 WHERE $list IS NOT NULL ORDER BY $list");
	if(!$result) {
	   		die ('userfilms: Can\'t get top movies ' . mysql_error());
	}
	$films = array();
	$userValues = userdata($db, $con, $userid);
	while ($row = mysql_fetch_array($result)){
		$films[] = array('id'=>$row[0], 'title'=>$row[1], 'seenit'=>$userValues[$row[0]], 'imdb'=>$row[2], 'afi'=>$row[3]);
	}
	return $films;
}

/** Return an associative array of the films seen by the user, with boolean values**/
function userdata($db, $con, $userid) {
	$db_selected = mysql_select_db($db, $con);
	$result = mysql_query("SELECT * FROM users WHERE userid = $userid");
	if(!$result) {
	   		die ('userdata: Can\'t get top movies ' . mysql_error());
	}
	return mysql_fetch_array($result, MYSQL_ASSOC);
}

function percentage($films){
	$counter = 0;
	foreach ($films as $film) {
		if($film['seenit'] == 1) {
			$counter += 1;
			}
	}
	if($counter == 0)
		return $counter;
	else 
		return $counter / 250 * 100;
	
}

/* Calculate the percentage for the user, based on two array */
function calculatepercent($imdbfilms, $afifilms){
	$imdb = 0;
	$afi = 0;
	//IMDB score
	foreach ($imdbfilms as $film) {
		if($film['seenit'] == 1) {
			$imdb += 1;
			}
	}
	if($imdb == 0)
		$imdbpercent = 0;
	else 
		$imdbpercent = $imdb / 250;
		
	foreach ($afifilms as $film) {
		if($film['seenit'] == 1) {
			$afi += 1;
			}
	}
	if($afi == 0)
		$afipercent = 0;
	else 
		$afipercent = $afi / 100;

	return (($afipercent * .5) + ($imdbpercent * .5)) * 100;
	
}

// Curling

/* Return the current top250 list in an assoc array, in order. imdbid => title 
   Uses curl, so only use this in a cron job */
function scrapetop250()
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
	$top250 = array();
	foreach ($match as $str) {
		$imdbid = substr($str, 9, 7);
		$title = substr($str, 19);
		$top250[$imdbid] = $title;
	}
	return $top250;
}

/* Returns the imdbid for the given movie title 
   Uses curl, so only use this in a cron job, as it is pretty slow */
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

// Layout

/* Returns the correct tabs given the $id of the user, and the $appurl */
function tabs($id, $appurl) {
	switch($id){
	case 1:
		$index = 'selected="true"';
		$afi = 'selected="true"';
		break;
	case 2;
		$friends = 'selected="true"';
		break;
	case 4:
		$invite = 'selected="true"';
		break;
	default:
		$index = 'selected="true"';
		$imdb = 'selected="true"';
		break;
	}	
	$tabs = '<fb:tabs>  
		  <fb:tab-item align="left" href="http://apps.facebook.com/'.$appurl.'" title="IMDB 250" '.$imdb.' />  	
		  <fb:tab-item align="left" href="http://apps.facebook.com/'.$appurl.'?list=afi" title="AFI 100" '.$afi.' />	
		  <fb:tab-item align="right" href="http://apps.facebook.com/'.$appurl.'friends.php" title="My Friends\' Results" '.$friends.' />
		  <fb:tab-item align="right" href="http://apps.facebook.com/'.$appurl.'invite.php" title="Invite People" '.$invite.' />
		  </fb:tabs>';
	return $tabs;
}

/* Returns the link to the IMDB page for the selected movie */
function movielink($title, $id) {
	$link = '<a target="_blank" href="http://www.imdb.com/title/tt' . $id .'/">' . $title .'</a>';
	return $link;
}

?>