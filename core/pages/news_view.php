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

$news = $result->fetch_object();
echo	"<span class=\"news-view-date\">".date("d.m.Y H:i", strtotime($news->create_date))."</span>
		<h1>$news->title" . ((isset($login))?"<a class=\"edit-btn\" title=\"bearbeiten\" href=\"edit/?site=news_edit&task=edit&id=$news->id&backbtn\"></a>":'') . "</h1>
		$news->text";
?>
<br />
<br />
<a href="?site=news_list">&raquo;zur News Übersicht</a>