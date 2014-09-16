<?php
if(empty($_GET['id']) or !isset($_GET['id']) or !is_numeric($_GET['id'])) {
	echo "<p>Bei der Anfrage trat ein Fehler auf, möglicherweise haben sie auf einen fehlerhaften Link geklickt...</p>";
	return;
}
$sql = "SELECT * FROM XENUX_news WHERE id = '".mysql_real_escape_string($_GET['id'])."' LIMIT 1;";
$erg = mysql_query($sql);
$num = mysql_num_rows($erg);
if($num < 1) {
	echo "<p>Bei der Anfrage trat ein Fehler auf, möglicherweise haben sie auf einen fehlerhaften Link geklickt...</p>";
	return;
}
$row = mysql_fetch_object($erg);
echo '<topic>'.$row->title;
if(@$_SESSION["login"] == 1) {
	echo '<a id="edit_href" href="edit/?site=news_edit&newspoint='.$_GET['id'].'">Bearbeiten</a>';
}
echo '</topic>';
echo nl2br(htmlentities($row->text));
?>
<br />
<br />
<a href="?site=newslist">&raquo;zur Übersicht</a>