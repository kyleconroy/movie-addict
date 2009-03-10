<?php 

/** Return anassociative array of the films seen by the user, with booleans **/
function userValues($db, $con, $userid) {
	$db_selected = mysql_select_db($db, $con);
	$result = mysql_query("SELECT * FROM users WHERE userid = $userid");
	if(!$result) {
	   		die ('Can\'t get top movies ' . mysql_error());
	}
	return mysql_fetch_array($result, MYSQL_ASSOC);
}

function printLink($title, $id) {
	echo '<a target="_blank" href="http://www.imdb.com/title/tt' . $id .'/">' . $title .'</a>';
}

function printTabs($id, $submit, $appurl) {
	switch($id){
	case 1:
		if($submit)
			$index = 'selected="true"';
		break;
	case 2;
		$friends = 'selected="true"';
		break;
	case 3;
		$watch = 'selected="true"';
		break;
	case 4;
		$invite = 'selected="true"';
		break;
	}	
	echo '<fb:tabs>  
		  <fb:tab-item align="right" href="http://apps.facebook.com/'.$appurl.'" title="My List" '.$index.' />  	
		  <fb:tab-item align="right" href="http://apps.facebook.com/'.$appurl.'friends.php" title="My Friends\' Results" '.$friends.' />
		  <fb:tab-item align="right" href="http://apps.facebook.com/'.$appurl.'invite.php" title="Invite People" '.$invite.' />
		  </fb:tabs>';
}

?>