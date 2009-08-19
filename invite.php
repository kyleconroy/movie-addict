<? 

// Get these from http://developers.facebook.com 
require_once('config.php');
require_once 'templates/layout_tabs.php';

// Names and images 
$app_image = "Application image URL"; 
$invite_href = "invite.php"; // Rename this as needed 

require_once 'facebook.php';

$facebook = new Facebook($appapikey, $appsecret ); 
$facebook->require_frame(); 
$user = $facebook->require_login(); 

//Create the pageData object
$pageData = (object)(array()); 

// Save Information
$pageData->css = $cssurl;
$pageData->tabs = 4;
$pageData->userid = $user;
$pageData->percent = $percent;
$pageData->sentmsg = isset($_POST["ids"]);
$pageData->fcount = sizeof($_POST["ids"]);


if(!isset($_POST["ids"])) { 
	// Retrieve array of friends who've already added the app. 
	$fql = 'SELECT uid FROM user WHERE uid IN (SELECT uid2 FROM friend WHERE uid1='.$user.') AND is_app_user = 1'; 
	$_friends = $facebook->api_client->fql_query($fql); 
	
	// Extract the user ID's returned in the FQL request into a new array. 
	$friends = array(); 
	if (is_array($_friends) && count($_friends)) { 
		foreach ($_friends as $friend) { 
			$friends[] = $friend['uid']; 
			}
	} 
	
	// Convert the array of friends into a comma-delimeted string. 
	$friends = implode(',', $friends); 
	
	// Prepare the invitation text that all invited users will receive. 
	$content = 
			"<fb:name uid=\"".$user."\" firstnameonly=\"true\" shownetwork=\"false\"/> has started using <a href=\"http://apps.facebook.com/".$appurl."/\">".$appname."</a>. See how many of the IMDB Top 250 you have seen so you can brag about your high score.</u>!\n". 
			"<fb:req-choice url=\"".$facebook->get_add_url()."\" label=\"Add ".$appname." to your profile\"/>"; 

}

// Template
if($pageData->sentmsg) { ?>
	<center>
		Thank you for inviting <?php $pageData->fcount; ?> of your friends on 
		<b><a href=\"http://apps.facebook.com/<?php $appurl; ?>/"><?php $appname; ?></a></b>
		<br><br> 
		<h2>
			<a href=\"http://apps.facebook.com/<?php $appurl; ?>">Click here to return to <?php $appname; ?></a>
		</h2>
	</center> 
	
<?php } else { ?>

	<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $pageData->css; ?>?ver=2.4" />

	<?php echo tabs($pageData->tabs); ?>

	<fb:request-form 
			action="<? echo $invite_href; ?>" 
			method="post" 
			type="<? echo $appname; ?>" 
			content="<? echo htmlentities($content); ?>" 
			image="<? echo $app_image; ?>"> 
			
			<fb:multi-friend-selector 
				actiontext="Here are your friends who don't have <? echo $appname; ?> yet. Invite whoever you want" 
				exclude_ids="<? echo $friends; ?>" /> 
	</fb:request-form> 

<?php 

} 

?>