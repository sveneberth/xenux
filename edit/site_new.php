<?php
if(!isset($site)) die("You can not open this file individually/Sie k&ouml;nnen diese Datei nicht einzeln &ouml;ffnen!");
if(isset($post->submit_new)) {
	$post->title = preg_replace("/[^a-zA-Z0-9_üÜäÄöÖ&#,.()[]{}*\/ ]/" , "" , $post->title);
	$post->category = preg_replace("/[^a-zA-Z0-9_üÜäÄöÖ&#,.()[]{}*\/ ]/" , "" , $post->category);
	if(!empty($post->title)) {
		$db->query("INSERT INTO XENUX_sites(title, category, text) VALUES ('$post->title', '$post->category', 'Seiteninhalt von $post->title');");
		$result = $db->query("SELECT * FROM XENUX_sites WHERE title = '$post->title';");
		$site_new = $result->fetch_object();
		echo "<script>window.location=\"./?site=site_edit&token=edit_site&site_id=$site_new->id\";</script>
		<p>Die Seite <a href=\"./?site=site_edit&token=edit_site&site_id=$site_new->id\"><i>$site_new->title</i> bearbeiten</a></p>";
	}
}
?>
<p>Hier kannst du eine neue Seite erstellen!</p>
<form action="" method="post">
	<input <?php if(empty($post->title) && isset($post->submit_new)) echo 'class="wrong"'; ?> type="text" name="title" placeholder="Seitentitel" value="<?php echo @$title; ?>">
	<input type="text" name="category" placeholder="Kategorie" value="<?php echo @$category; ?>">
	
	<input type="hidden" name="submit_new" value="true">
	<input type="submit" value="Seite hinzufügen">
</form>