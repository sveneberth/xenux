<?php
if(empty($_GET['event_id']) or !isset($_GET['event_id']) or !is_numeric($_GET['event_id'])) {
	request_failed();
	return false;
}

$result = $db->query("SELECT *, DATE_FORMAT(date,'%d.%m.%Y %H:%i') as date_formatted FROM XENUX_dates WHERE id ='$get->event_id';");
$num = $result->num_rows;
if($num < 1) {
	request_failed();
	return false;
}

$row = $result->fetch_object();
echo "	<h3 style=\"margin: 20px 0 5px 0;\">$row->name</h3>
		<strong>$row->date_formatted</strong><br />".
		nl2br(htmlentities($row->text));
?>
<br />
<br />
<a href="?site=event_list">&raquo;zur Übersicht</a>