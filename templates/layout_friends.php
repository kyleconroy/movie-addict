<?php require_once 'layout_tabs.php'; ?>
<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $pageData->css; ?>" />

<?php echo tabs($pageData->tabs); ?>
	  
<h1 class="settings">
	<fb:name uid="<?php echo $pageData->userid; ?>" useyou="false" capitalize="true" possessive="true"/> Friends
</h1>

<fb:if-section-not-added section="profile"> 
	<div class="addmsg"><fb:add-section-button section="profile" />Your profile currently lacks a certain something</div>
</fb:if-section-not-added>

<form action="common.php" method="GET">
	<table cellpadding="0" cellspacing="1" class="table friends">
		<thead>
		<tr>
		<th>Compare</th>
		<th colspan="2">Friend</th>
		<th>Percent</th>
		<th>List</th>
		</tr>
		</thead>
		<tbody>
		<?php foreach($pageData->friends as $friend) { 
			if($count++ % 2 == 0)
				$pageData->row = "even";
			else 
				$pageData->row = "odd"; 
		?>
		<tr class="<?php echo $pageData->row; ?>">
			<td class="check"><input type="checkbox" name="user[<?php echo $friend["id"]; ?>]"></td>
			<td class="img"><fb:profile-pic uid="<?php echo $friend["id"] ?>" linked="true" size="q" /></td>
			<td class="name"><fb:name uid="<?php echo $friend["id"] ?>" capitalize="true" /></td>
			<td class="percent"><?php echo $friend["percent"] ?>%</td>
			<td class="list"><a href="http://apps.facebook.com/<?php echo $pageData->appurl ?>view.php?user=<?php echo $friend["id"] ?>">
				See <fb:name uid="<?php echo $friend["id"] ?>" capitalize="true" possessive="true"/> list</a>
			</td>
		</tr>
		
		<?php }	?>
		</tbody>
	</table>
	<input type="hidden" name="user[<?php echo $facebook->user; ?>]" value="checked" />
	<input class="inputsubmit submit" type="submit" value="Compare Lists" />
</form>