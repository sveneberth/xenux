<?php
if(empty($_GET['page_id']) or !isset($_GET['page_id']) or !is_numeric($_GET['page_id'])) {
	echo "<p>Bei der Anfrage trat ein Fehler auf, möglicherweise haben sie auf einen fehlerhaften Link geklickt...</p>";
	return;
}

$result = $db->query("SELECT * FROM XENUX_sites WHERE id = '$get->page_id' LIMIT 1;");
$num = $result->num_rows;

if($num < 1) {
	echo "<p>Bei der Anfrage trat ein Fehler auf, möglicherweise haben sie auf einen fehlerhaften Link geklickt...</p>";
	return false;
}

$row = $result->fetch_object();

echo "<h1>$row->title" . ((isset($login))?"<a id=\"edit_href\" href=\"edit/?site=site_edit&token=edit_site&site_id=$row->id\">Bearbeiten</a>":'') . "</h1>";
echo $row->text;

if(!empty($row->category)) {
	
	$result = $db->query("SELECT * FROM XENUX_sites WHERE category = '$row->category' ORDER by title ASC;");
	$i = 1;
	$s_i_c = 0;
	$site_pos = array();
	while($row_s = $result->fetch_object()) {
		$site_pos[$i] = $row_s->id;
		if($row->id == $row_s->id) {
			$cur_pos = $i;
		}
		$i++;
		$s_i_c++;
	}
	if($cur_pos != 1) {
		$result = $db->query("SELECT * FROM XENUX_sites WHERE id = '{$site_pos[$cur_pos-1]}' ORDER by title ASC;");
		$prev = $result->fetch_object();
	}
	if($cur_pos != $s_i_c) {
		$result = $db->query("SELECT * FROM XENUX_sites WHERE id = '{$site_pos[$cur_pos+1]}' ORDER by title ASC;");
		$next = $result->fetch_object();
	}
}

if(isset($prev) or isset($next)) {
	echo "<div class=\"prevnextnavi\">";
		if(isset($prev)) {
			echo "<a class=\"prev\" title=\"$prev->title\" href=\"?site=page&page_id=$prev->id\">&larr;</a>";
		}
		if(isset($next)) {
			echo "<a class=\"next\" title=\"$next->title\" href=\"?site=page&page_id=$next->id\">&rarr;</a>";
		}
	echo "</div>";
}
?>