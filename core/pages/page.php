<?php
if(empty($_GET['page_id']) or !isset($_GET['page_id']) or !is_numeric($_GET['page_id'])) {
	echo "<p>Bei der Anfrage trat ein Fehler auf, möglicherweise haben sie auf einen fehlerhaften Link geklickt...</p>";
	return;
}
$sql = "SELECT * FROM XENUX_pages WHERE id = '".mysql_real_escape_string($_GET['page_id'])."' LIMIT 1;";
$erg = mysql_query($sql);
$num = mysql_num_rows($erg);
if($num < 1) {
	echo "<p>Bei der Anfrage trat ein Fehler auf, möglicherweise haben sie auf einen fehlerhaften Link geklickt...</p>";
	return;
}
$row = mysql_fetch_object($erg);
echo "<h1>$row->fullname";
if (@$_SESSION["login"] == 1) {
	echo "<a id=\"edit_href\" href=\"edit/?site=site_edit&id=$row->id\">Bearbeiten</a>";
}
echo "</h1>";
echo /*nl2br*/($row->text);
if(!empty($row->category)) {
	$sql = "SELECT * FROM XENUX_pages WHERE category = '$row->category' ORDER by fullname";
	$erg = mysql_query($sql);
	$i = 1;
	$s_i_c = 0;
	$site_pos = array();
	while($row_s = mysql_fetch_array($erg)) {
		$site_pos[$i] = $row_s["id"];
		if($row->id == $row_s['id']) {
			$cur_pos = $i;
		}
		$i++;
		$s_i_c++;
	}
	if($cur_pos != 1) {
		$sql = "SELECT * FROM XENUX_pages WHERE id = '".($site_pos[$cur_pos-1])."' ORDER by fullname";
		$erg = mysql_query($sql);
		$row_prev = mysql_fetch_array($erg);
		foreach($row_prev as $key => $val) {
			$a = "prev_$key";
			$$a = $val;
		}
	}
	if($cur_pos != $s_i_c) {
		$sql = "SELECT * FROM XENUX_pages WHERE id = '".($site_pos[$cur_pos+1])."' ORDER by fullname";
		$erg = mysql_query($sql);
		$row_next = mysql_fetch_array($erg);
		foreach($row_next as $key => $val) {
			$a = "next_$key";
			$$a = $val;
		}
	}
}
if(isset($prev_fullname) or isset($next_fullname)) {
			echo "<div class=\"prevnextnavi\">";
				if(isset($prev_fullname)) {
					echo "<a class=\"prev\" href=\"?site=page&page_id=$prev_id\">&laquo;$prev_fullname</a>";
				}
				if(isset($next_fullname)) {
					echo "<a class=\"next\" href=\"?site=page&page_id=$next_id\">$next_fullname&raquo;</a>";
				}
			echo "</div>";
		}
?>