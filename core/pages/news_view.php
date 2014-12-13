<?php
if(empty($_GET['news_id']) or !isset($_GET['news_id']) or !is_numeric($_GET['news_id'])) {
	request_failed();
	return false;
}

$result = $db->query("SELECT * FROM XENUX_news WHERE id = '$get->news_id' LIMIT 1;");
$num = $result->num_rows;
if($num < 1) {
	request_failed();
	return false;
}

$row = $result->fetch_object();
echo	"<span class=\"news-view-date\">".date("d.m.Y H:i", strtotime($row->create_date))."</span>
		<h1>$row->title</h1>".
		nl2br(htmlentities($row->text));
?>
<br />
<br />
<a href="?site=news_list">&raquo;zur News Übersicht</a>