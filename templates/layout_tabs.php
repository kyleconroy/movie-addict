<?php 

require_once('config.php');


function tabs($id, $user="?") {
	global $appurl;
	$link = 'index.php?';
	switch($id){
	case 1:
		$afi = 'selected="true"';
		break;
	case 2;
		$friends = 'selected="true"';
		break;
	case 4:
		$invite = 'selected="true"';
		break;
	case 8:
		$afi = 'selected="true"';
		$link = 'view.php?user=' . $user . '&';
		break;
	case 9:
		$link = 'view.php?user=' . $user . '&';
		$imdb = 'selected="true"';
		break;
	default:
		$imdb = 'selected="true"';
		break;
	}	
	$tabs = '<fb:tabs>  
		  <fb:tab-item align="left" href="http://apps.facebook.com/'. $appurl . $link . '" title="IMDB 250" '.$imdb.' />  	
		  <fb:tab-item align="left" href="http://apps.facebook.com/'. $appurl . $link .'list=afi" title="AFI 100" '.$afi.' />	
		  <fb:tab-item align="right" href="http://apps.facebook.com/'.$appurl.'friends.php" title="My Friends\' Results" '.$friends.' />
		  <fb:tab-item align="right" href="http://apps.facebook.com/'.$appurl.'invite.php" title="Invite People" '.$invite.' />
		  </fb:tabs>';
	return $tabs;
}

?>