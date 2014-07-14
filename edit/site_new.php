<?php
if(!isset($site)) die("You can not open this file individually/Sie k&ouml;nnen diese Datei nicht einzeln &ouml;ffnen!");
if(!empty($_POST)) {
	$fullname = mysql_real_escape_string(preg_replace("/[^a-zA-Z0-9_üÜäÄöÖ&#,.()[]{}*\/ ]/" , "" , $_POST["fullname"]));
	$category = mysql_real_escape_string(preg_replace("/[^a-zA-Z0-9_üÜäÄöÖ&#,.()[]{}*\/ ]/" , "" , strtolower($_POST["category"])));
	if(!empty($fullname)) {
		$sql = "INSERT INTO XENUX_pages(fullname, category, text) VALUES ('$fullname', '$category', 'Seiteninhalt von $fullname');";
		$erg = mysql_query($sql) 
			or die("Fehlgeschlagen: " . mysql_error());
		$sql = "SELECT * FROM XENUX_pages WHERE fullname = '$fullname';";
		$erg = mysql_query($sql);
		$row = mysql_fetch_array($erg);
		foreach($row as $key => $val) {
			$$key = $val;
		}
		echo "<p>Die Seite wurde erfolgreich hinzugefügt!</p>";
		echo "<p>Die Seite <i>$fullname</i> <a href=\"./?site=site_edit&id=$id\">bearbeiten</a></p>";
	} else {
		echo "<p style=\"color:red;\">Bitte fülle alle Felder aus!</p>";
	}
}
?>
<p>Hier kannst du eine neue Seite erstellen!</p>
<form action="" method="post">
	<span <?php if (empty($fullname) and $_SERVER['REQUEST_METHOD'] == "POST"){echo 'style="color:#cc0000;"';} ?>>Seitentitel (Überschrift):</span><br />
	<input type="text" name="fullname" placeholder="Seitentitel" value="<?php echo @$fullname; ?>"><br /><br />
	Kategorie:<br />
	<input type="text" name="category" placeholder="Kategorie" value="<?php echo @$category; ?>"><br /><br />
	<input type="submit" name="submit" value="Seite hinzufügen">
</form>