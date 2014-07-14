<?php
if(!isset($_GET['searchtxt'])) {
	echo "<p>Bei der Anfrage trat ein Fehler auf, möglicherweise haben sie auf einen fehlerhaften Link geklickt...</p>";
	return;
}
$searchtxt = mysql_real_escape_string($_GET['searchtxt']);
$sql = "SELECT * FROM XENUX_pages WHERE (
				(
					fullname	LIKE	'%".$searchtxt."%' 
					OR	text	LIKE	'%".$searchtxt."%'
				)
				AND	filename	!=		'termine'
				AND	filename	!=		'terminview'
				AND	filename	!=		'page'
				AND	filename	!=		'newslist'
				AND	filename	!=		'news'
				AND	filename	!=		'error'
				AND	filename	!=		'search'
				) ORDER by fullname;";
$erg = mysql_query($sql) or die(mysql_error());
$num = mysql_num_rows($erg);
if($num < 1) {
	echo "<p>keine Suchergebnisse...</p>";
	return;
}
while($row = mysql_fetch_object($erg)) {
	if(contains($row->filename, 'impressum', 'kontakt', 'home')) {
		echo "<div class=\"searchresult\"><b><a href=\"?site=$row->filename\">$row->fullname</a></b><br />
		".nl2br(maxlines($row->text,5))."</div>";
	} else {
		echo "<div class=\"searchresult\"><b><a href=\"?site=page&page_id=$row->id\">$row->fullname</a></b><br />
		".nl2br($row->text)."<br /></div>";
	}
}
# nl2br(substr($row->text,0,200))
?>