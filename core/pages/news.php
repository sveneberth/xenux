<?php
# Hier bitte nichts ändern!
if(empty($_GET['id']) or !isset($_GET['id'])) {
	echo 'Bei der Anfrage trat ein Fehler auf, möglicherweise haben sie auf einen fehlerhaften Link geklickt...';
	exit;
}
$sql = "SELECT * FROM XENUX_news WHERE id = '".$_GET['id']."'";
$erg = mysql_query($sql);
$row = mysql_fetch_object($erg);
echo '<topic>'.$row->title;
if ($_SESSION["login"] == 1) {
			echo '<a id="edit_href" href="edit/?site=news_edit&newspoint='.$_GET['id'].'">Bearbeiten</a>';
		}
echo '</topic>';
echo $row->text;
?>
<br />
<br />
<a href="?site=newslist">&raquo;zur Übersicht</a>