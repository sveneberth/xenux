<?php
if($page->site == 'error') {
	echo "	<h1>Error 404 - Seite nicht gefunden</h1>
			<p>Bei der Anfrage trat ein Fehler auf, möglicherweise haben sie auf einen fehlerhaften Link geklickt...</p>";
	return false;
}

echo "<h1>$page->title" . ((isset($login))?"<a class=\"edit-btn\" title=\"bearbeiten\" href=\"edit/?site=site_edit&token=edit_site&site_id=$page->id&backbtn&gotosite\"></a>":'') . "</h1>";
echo $page->text;

if($page->parent_id != 0) {
	$result = $db->query("SELECT * FROM XENUX_sites WHERE parent_id = '$page->parent_id' ORDER by title ASC;");
	$i = 1;
	$s_i_c = 0;
	$site_pos = array();
	while($row_s = $result->fetch_object()) {
		$site_pos[$i] = $row_s->id;
		if($page->id == $row_s->id) {
			$cur_pos = $i;
		}
		$i++;
		$s_i_c++;
	}
	if($cur_pos != 1) {
		$result = $db->query("SELECT * FROM XENUX_sites WHERE id = '{$site_pos[$cur_pos-1]}' LIMIT 1;");
		$prev = $result->fetch_object();
	}
	if($cur_pos != $s_i_c) {
		$result = $db->query("SELECT * FROM XENUX_sites WHERE id = '{$site_pos[$cur_pos+1]}' LIMIT 1;");
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