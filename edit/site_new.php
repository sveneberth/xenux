<?php
if(!isset($site)) die("You can not open this file individually/Sie k&ouml;nnen diese Datei nicht einzeln &ouml;ffnen!");
if(!empty($_POST)) {
	$filename = mysql_real_escape_string(strtolower(preg_replace("/[^a-zA-Z0-9_]/" , "" , $_POST["filename"])));
	$fullname = mysql_real_escape_string($_POST["fullname"]);
	$category = mysql_real_escape_string(strtolower($_POST["category"]));
	if(!empty($filename) and !empty($fullname)) {
		$sql = "SELECT COUNT(filename) AS anzahl FROM XENUX_pages WHERE filename = '".$filename."'";
		$erg = mysql_query($sql);
		$var = mysql_fetch_object($erg);
		if($var->anzahl >= 1) {
			echo '<p style="color:red;">Es existiert bereits eine Seite mit dem Kurzname <i>'.$filename.'</i> Duplikate sind nicht erlaubt!</p>';
		} else{
			$content= 'Hier der Inhalt von '.$fullname;
			$file = '../core/pages/'.$filename.'.php';
			$datei = fopen($file, "w");
			fwrite($datei, $content);
			fclose($datei);
			$sql = "INSERT INTO XENUX_pages(filename, fullname, category) VALUES ('$filename','$fullname', '$category')";
			$erg = mysql_query($sql) 
				or die("Fehlgeschlagen: " . mysql_error());
			$sql = "SELECT * FROM XENUX_pages WHERE filename = '$filename'";
			$erg = mysql_query($sql);
			$row = mysql_fetch_array($erg);
			foreach($row as $key => $val) {
				$$key = $val;
			}
			echo 'Die Seite wurde erfolgreich hinzugefügt!<br />';
			echo "Die Seite <i>$fullname</i> <a href=\"./?site=site_edit&id=$id\">bearbeiten</a><br />";
		}
	} else {
		echo '<p style="color:red;">Bitte füllen sie alle Felder aus!</p>';
	}
}
?>
<p>Hier kannst du eine neue Seite erstellen!</p>
<form action="" method="post">
	<span <?php if (empty($filename) and $_SERVER['REQUEST_METHOD'] == "POST"){echo 'style="color:#cc0000;"';} ?>>Seitenkurzname (Bsp.: <i>Die Geschichte von uns</i> => <i>geschichte</i>):</span><br />
	<input type="text" name="filename" maxlength="15" size="70" value="<?php echo @$filename; ?>"><br /><br />
	<span <?php if (empty($fullname) and $_SERVER['REQUEST_METHOD'] == "POST"){echo 'style="color:#cc0000;"';} ?>>Seitentitel (Überschrift):</span><br />
	<input type="text" name="fullname" size="70" value="<?php echo @$fullname; ?>"><br /><br />
	Kategorie:<br />
	<input type="text" name="category" value="<?php echo @$category; ?>"><br /><br />
	<input type="submit" name="submit" value="Seite hinzufügen">
</form>