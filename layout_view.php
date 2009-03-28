<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $pageData->css; ?>" />

<?php echo $pageData->tabs; ?>
	  
<h1 class="settings">
	<a class="inputsubmit compare" href="http://apps.facebook.com/<?php echo $appurl; ?>common.php?user%5B<?php echo $pageData->userid; ?>%5D=on&user%5B<?php echo $facebook->user; ?>%5D=on">Compare Lists</a><fb:name uid="<?php echo $pageData->userid; ?>" useyou="false" capitalize="true" /> is <?php echo $pageData->percent; ?>% addicted to film. 
</h1>

<?php echo $pageData->msg; ?>

<fb:if-section-not-added section="profile"> 
	<div class="addmsg"><fb:add-section-button section="profile" />Your profile currently lacks a certain something</div>
</fb:if-section-not-added>

	<table cellpadding="0" cellspacing="1" class="table">
	<thead>
	<tr>
		<th>IMDB Rank</th>
		<th>Seen it?</th>
		<th>Title</th>
	</tr>
	</thead>
	<tbody>
	<?php 
	foreach($pageData->films as $film) {
	if($film["seenit"] == 1) {
		$pageData->row = "yes";
		$pageData->image = "&#10003;"; }
	else {
		$pageData->row = "no";
		$pageData->image = "&#10007;"; }
	 ?>
		<tr class="<?php echo $pageData->row; ?>">
			<td class="count"><?php echo $film["imdb"]; ?></td>
			<td class="count"><?php echo $pageData->image; ?></td>
			<td><?php echo movielink($film["title"], $film["id"]); ?></td>
		</tr>
	<?php } ?>
	</tbody>
	</table>