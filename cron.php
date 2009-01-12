<?php 

require_once('config.php');

/** Update Everything **/
$top = top250();
updateTop250($top, $dbuser, $dbpassword, $db);
updateUsers($top, $dbuser, $dbpassword, $db);


function top250()
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
	return $match;
}

/** Update the top 250 table **/
function updateTop250($top250, $dbuser, $dbpassword, $db) {
	$con = mysql_connect("localhost",$dbuser,$dbpassword);
	if (!$con) {
	  die('Could not connect: ' . mysql_error());
	}
	$db_selected = mysql_select_db($db, $con);
	if (!$db_selected) {
	    die ('Can\'t use ' . mysql_error());
	}
	if(!mysql_query("TRUNCATE TABLE top250")) {
	    die ('Can\'t truncate table ' . mysql_error());
	}
	foreach ($top250 as $str) {
		$imdbid = substr($str, 9, 7);
		$title = substr($str, 19);
		if(!mysql_query("INSERT INTO top250 VALUES (\"$imdbid\", \"$title\")")) {
	   		die ('Can\'t insert into table ' . mysql_error());
	 	}
	}
	mysql_close();
}

/** Add any movies that weren't previously on the list to the user table **/
function updateUsers($top250, $dbuser, $dbpassword, $db) {
	$con = mysql_connect("localhost",$dbuser,$dbpassword);
	if (!$con) {
	  die('Could not connect: ' . mysql_error());
	}
	$db_selected = mysql_select_db($db, $con);
	if (!$db_selected) {
	    die ('Can\'t use ' . mysql_error());
	}
	$result = mysql_query("SELECT * FROM users WHERE userid = 0");
	$row = mysql_fetch_array($result, MYSQL_ASSOC);
	foreach ($top250 as $str) {
		$imdbid = substr($str, 9, 7);
		if($row[$imdbid] == NULL) {
   			if(!mysql_query("ALTER TABLE users ADD `$imdbid` BOOL")) {
    			die ('Can\'t alter table ' . mysql_error());
			}
			if(!mysql_query("ALTER TABLE users MODIFY `$imdbid` BOOL NOT NULL")) {
    			die ('Can\'t make not null ' . mysql_error());
			}
			if(!mysql_query("ALTER TABLE users ALTER `$imdbid` SET DEFAULT 0;")) {
    			die ('Can\'t set default ' . mysql_error());
			}
	 	}
	}
	mysql_close(); 
}

