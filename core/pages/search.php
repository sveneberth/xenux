<?php
if(!isset($_GET['q'])) {
	echo "<p>Bei der Anfrage trat ein Fehler auf, möglicherweise haben sie auf einen fehlerhaften Link geklickt...</p>";
	return;
}
$result = $db->query("SELECT * FROM XENUX_sites WHERE (
				(
						title	LIKE	'%".$get->q."%' 
					OR	text	LIKE	'%".$get->q."%'
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
	$text_shortened = shortstr(strip_tags($row->text), 300);
	
	if(contains($row->site, 'imprint', 'contact', 'home')) {
		echo "	<div class=\"searchresult\">
					<b><a href=\"?site=$row->site\">$row->title</a></b><br />
					$text_shortened
				</div>";
	} else {
		echo "	<div class=\"searchresult\">
					<b><a href=\"?site=page&page_id=$row->id\">$row->title</a></b><br />
					$text_shortened
				</div>";
	}
}
?>