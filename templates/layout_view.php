<?php require_once 'layout_tabs.php'; ?>

<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $pageData->css; ?>" />

<?php echo tabs($pageData->tabs, $pageData->userid); ?>
	  
<h1 class="settings">
	<fb:name uid="<?php echo $pageData->userid; ?>" useyou="false" capitalize="true" /> is <?php echo $pageData->percent; ?>% addicted to film.
	<fb:pronoun useyou="false" capitalize="true" uid="<?php echo $pageData->userid; ?>" /> 
	has seen <?php echo $pageData->moviecount; ?> out of <?php echo $pageData->totalcount; ?> movies.
</h1>

<?php echo $pageData->msg; ?>

<fb:if-section-not-added section="profile"> 
	<div class="addmsg"><fb:add-section-button section="profile" />Your profile currently lacks a certain something</div>
</fb:if-section-not-added>

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
		foreach($pageData->movies as $movie) { 
			if(in_array($movie['id'], $pageData->seen_movies)){
				$pageData->row = "yes";
				$pageData->image = "&#10003;";
			} else {
				$pageData->row = "no";
				$pageData->image = "&#10007;";
			}
			$count++;
		
	?>		
		<tr class="<?php echo $pageData->row; ?>">
			<td class="count"><?php echo $count ?></td>
			<td class="seenit"><?php echo $pageData->image ?></td>
			<td class="mtitle"><?php echo "<a href=\"http://www.imdb.com/title/tt" . $movie['id'] . "\"> " . $movie['title'] . "</a>" ?></td>
		</tr>
	<?php } ?>
	</tbody>
	</table>