<?php

/** Use this file to add another list into the database. 
	The given filename is assumed to be in the correct order
	$listname should be the organzation that produced the list
	such as afi, or imdb **/
	

require_once('libfunction.php');

addList('afi');
	
function addList($listname) {
	require_once('config.php');
	
	$con = mysql_connect("localhost",$dbuser,$dbpass);
	if (!$con) {
	  die('Could not connect: ' . mysql_error());
	}
	$db_selected = mysql_select_db($db, $con);
	if (!$db_selected) {
	    die ('Can\'t use ' . mysql_error());
	}
	
	$movienames = $listname . '_names.txt';
	$movieids = $listname . '_ids.txt';
	
	echo "Reading in list...";
	$newlist = file('lists/' . $movienames);
	echo "Done<br>";
	echo "Get ids...";
	if(file_exists('lists/' . $movieids)){
		$ids = file('lists/' . $movieids);
		$newlist = getidsFile($newlist, $ids);
	} else
		$newlist = getidsGoogle($newlist, $movieids);
	echo "Done<br>";
	$films = filmids($db, $con);	
	
	if(!mysql_query("ALTER TABLE top250 ADD $listname INT"))
		echo "Column already exists, continuing...<br>";

	$counter = 1;
	foreach ($newlist as $id => $name) {
		if(!in_array($id, $films)) {
			if(mysql_query("INSERT INTO top250 (imdbid, title, $listname) VALUES (\"$id\", \"$name\", \"$counter\")"))
				printf("Added $id, $name, as the $counter entry in $listname<br>");
			else
				die ("Can\'t add $id, $name, " . mysql_error());
			if(!mysql_query("ALTER TABLE users ADD UNIQUE $id BOOL")) {
    			die ('Can\'t alter table ' . mysql_error());
			}
			if(!mysql_query("ALTER TABLE users MODIFY $id BOOL NOT NULL")) {
    			die ('Can\'t make not null ' . mysql_error());
			}
			if(!mysql_query("ALTER TABLE users ALTER $id SET DEFAULT 0;")) {
    			die ('Can\'t set default ' . mysql_error());
			}	
		} else if(mysql_query("UPDATE top250 SET $listname = $counter WHERE imdbid = '$id'")){
			printf("Updated $id, $name, as the $counter entry in $listname<br>");  
		} else {
		  	die("Error $id, $name, not added as the $counter entry in $listname". mysql_error(). '<br>');
		}
	 	$counter++;
	}
}

function getidsFile($movies, $movieids){
	$ids = array();
	$counter = 0;
	foreach($movies as $movie) {
		$key = trim($movieids[$counter++]);
		$ids[$key] = $movie;
	}
	return $ids;
}

function getidsGoogle($movies, $file){
	$ids = array();
	foreach($movies as $movie) {
		$imdbid = findimdbid($movie);
		$ids[$imdbid] = $movie;
	}
	$fp=fopen($file,"w+");
	foreach($ids as $key => $value){
		fwrite($fp,$key."\n");
	}
	fclose($fp);
	return $ids;
}
?>