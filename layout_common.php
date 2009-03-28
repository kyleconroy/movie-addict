<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $pageData->css; ?>" />

<?php echo $pageData->tabs; ?>
	  
<h1 class="settings">
	Movies we haven't seen
	<?php 
	foreach($pageData->users as $key => $value) { ?>
		<fb:profile-pic uid="<?php echo $key  ?>" linked="true" size="q" />
	<?php } ?>
</h1>

<?php echo $pageData->msg; ?>

<fb:if-section-not-added section="profile"> 
	<div class="addmsg"><fb:add-section-button section="profile" />Your profile currently lacks a certain something</div>
</fb:if-section-not-added>


<table cellpadding="0" cellspacing="1" class="table">
	<thead>
		<tr>
		<th>Title</th>
		<th>IMDB Rank</th>
		<th>AFI Rank</th>
		</tr>
	</thead>
	<tbody>
<?php 
foreach($pageData->films as $film) {
if($count++ % 2 == 0)
	$pageData->row = "even";
else 
	$pageData->row = "odd";
?>	
	<tr class="<?php echo $pageData->row ; ?>">
		<td><?php echo $film["link"]; ?></td>
		<td><?php echo $film["imdb"]; ?></td>
		<td><?php echo $film["afi"]; ?></td>
	</tr>
<?php } ?>
	</tbody>
</table>
