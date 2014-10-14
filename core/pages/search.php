<?php
if(!isset($_GET['searchtxt'])) {
	echo "<p>Bei der Anfrage trat ein Fehler auf, möglicherweise haben sie auf einen fehlerhaften Link geklickt...</p>";
	return;
}
$result = $db->query("SELECT * FROM XENUX_sites WHERE (
				(
						title	LIKE	'%".$get->searchtxt."%' 
					OR	text	LIKE	'%".$get->searchtxt."%'
				)
				AND		site	!=		'event_list'
				AND		site	!=		'event_view'
				AND		site	!=		'page'
				AND		site	!=		'news_list'
				AND		site	!=		'news_view'
				AND		site	!=		'error'
				AND		site	!=		'search'
				) ORDER by title ASC;");
$num = $result->num_rows;
if($num < 1) {
	echo "<p>keine Suchergebnisse...</p>";
	return;
}
while($row = $result->fetch_object()) {
	if(contains($row->site, 'impressum', 'kontakt', 'home')) {
		echo "	<div class=\"searchresult\">
					<b><a href=\"?site=$row->site\">$row->title</a></b><br />
					".nl2br(maxlines($row->text,5))."
				</div>";
	} else {
		echo "	<div class=\"searchresult\">
					<b><a href=\"?site=page&page_id=$row->id\">$row->title</a></b><br />
					".nl2br($row->text)."<br />
				</div>";
	}
}
?>