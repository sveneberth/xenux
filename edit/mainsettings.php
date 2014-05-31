<?php
if(!isset($site)) die("You can not open this file individually/Sie k&ouml;nnen diese Datei nicht einzeln &ouml;ffnen!");
if($login['role'] < 2) {
	echo '<p>Du bist nicht berechtigt, diese Seite zu öffnen!</p>';
	return;
}
if(isset($_POST['sumbit'])) {
	foreach($_POST as $key => $val) {
		$$key = $val;
		$sql = "UPDATE XENUX_main Set value = '".mysql_real_escape_string($val)."' WHERE name = '$key'";
		$erg = mysql_query($sql);
	}
	echo 'Die Einstellungen wurden geändert!';
}
?>
<p>Hier kannst du die Grundeinstellungen der Homepage und des Systems ändern.</p>
<form action="" method="post">
	<?php
	$sql = "SELECT * FROM XENUX_main";
	$erg = mysql_query($sql);
	while($row = mysql_fetch_array($erg)) {
		foreach($row as $key => $val) {
			$$key = $val;
		}
		echo "<span ";
		if(empty($value) and $_SERVER['REQUEST_METHOD'] == "POST") echo 'style="color:#cc0000;"';
		echo ">$label</span><br />";
		if($type == "textarea") {
			echo "<textarea name=\"$name\" placeholder=\"$label\">$value</textarea><br /><br />";
		} else {
			echo "<input type=\"$type\" name=\"$name\" value=\"$value\" placeholder=\"$label\" /><br /><br />";
		}
	}
	?>
	
	<input type="hidden" name="sumbit" value="none" />
	<input type="submit" value="ändern">
</form>