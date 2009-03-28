<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $pageData->css; ?>" />

<?php echo $pageData->tabs; ?>
	  
<h1 class="settings">
	<fb:name uid="<?php echo $pageData->userid; ?>" useyou="false" capitalize="true" /> is <?php echo $pageData->percent; ?>% addicted to film.
	<fb:pronoun useyou="false" capitalize="true" uid="<?php echo $pageData->userid; ?>" /> 
	has seen <?php echo $pageData->moviecount; ?> out of <?php echo $pageData->totalcount; ?> movies.
</h1>

<?php echo $pageData->msg; ?>

<fb:if-section-not-added section="profile"> 
	<div class="addmsg"><fb:add-section-button section="profile" />Your profile currently lacks a certain something</div>
</fb:if-section-not-added>

<form action="updateUser.php" method="POST">
	<table class="table" cellpadding="0" cellspacing="1">
	<thead>
		<tr>
		<th>Rank</th>
		<th>Seen?</th>
		<th>Title</th>	
		</tr>
	</thead>
	<tbody>
	<?php 
		$count = 0;
		foreach($pageData->films as $film) { 
			if($count++ % 2 == 0)
				$pageData->row = "even";
			else 
				$pageData->row = "odd"; 
	?>
		<tr class="<?php echo $pageData->row; ?>">
			<td class="count"><?php echo $film[$pageData->movielist]; ?></td>
			<td class="seenit"><input type="checkbox" <?php echo $film["checked"]; ?> name="film[<?php echo $film["id"]; ?>]"></td>
			<td class="mtitle"><?php echo $film["link"]; ?></td>
		</tr>
	<?php } ?>
	</tbody>
	</table>
	<input type="hidden" name="userid" value="<?php echo $pageData->userid; ?>"  />
	<input type="hidden" name="list" value="<?php echo $pageData->movielist; ?>"  />
	<input class="inputsubmit submit" type="submit" value="Update Movies" />
</form>