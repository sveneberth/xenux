<?php
if(!isset($site)) die("You can not open this file individually/Sie k&ouml;nnen diese Datei nicht einzeln &ouml;ffnen!");
if($login->role < 2) {
	echo '<p>Du bist nicht berechtigt, diese Seite zu öffnen!</p>';
	return false;
}
if(isset($post->sumbit)) {
	foreach($post as $key => $val) {
		$db->query("UPDATE XENUX_main Set value = '$val' WHERE name = '$key'");
	}
	header("Location: ./?site=mainsettings&updated=success");
}
if(@$get->updated == 'success') {
	echo 'Die Einstellungen wurden geändert!';
}
?>
<p>Hier kannst du die Grundeinstellungen der Homepage und des Systems ändern.</p>
<form action="" method="post">
	<?php
	$result = $db->query("SELECT * FROM XENUX_main;");
	while($row = $result->fetch_object()) {
		if($row->type == "textarea") {
			echo "<textarea ".(empty($row->value)&&isset($post->sumbit)?'class="wrong"':'')." name=\"$row->name\" placeholder=\"$row->label\">$row->value</textarea>";
		} else {
			echo "<input type=\"$row->type\" name=\"$row->name\" value=\"$row->value\" placeholder=\"$row->label\" />";
		}
	}
	?>
	
	<input type="hidden" name="sumbit" value="none" />
	<input type="submit" value="ändern">
</form>