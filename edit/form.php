<?php
if(!isset($site)) die("You can not open this file individually/Sie k&ouml;nnen diese Datei nicht einzeln &ouml;ffnen!");
if($login['role'] < 2) {
	echo '<p>Du bist nicht berechtigt, diese Seite zu öffnen!</p>';
	return;
}
if(isset($_GET['new']) and $_GET['new'] == "yes") {
	$sql = "INSERT INTO XENUX_form(label) VALUES ('Neues Feld')";
	$erg = mysql_query($sql);
}

if(isset($_GET['id'])) {
	$id = $_GET['id'];
	$sql = "SELECT * FROM XENUX_form WHERE id = '$id'";
	$erg = mysql_query($sql);
	$row = mysql_fetch_array($erg);
	foreach($row as $key => $val) {
		$$key = $val;
	}
	if(isset($_POST['editor'])) {
		foreach($_POST as $key => $val) {
			$$key = $val;
		}
		$sql = "UPDATE XENUX_form Set type = '$type', label = '$label' WHERE id = '$id'";
		$erg = mysql_query($sql);
		echo "<p>Gespeichert</p>";
	} else {
		?>
		<form action="" method="post" name="form">
			Feldbeschreibung:<br />
			<input type="text" name="label" value="<?php echo $label; ?>"><br /><br />
			Feld-Typ:<br />
			<input type="radio" <?php if($type=="text")echo "checked"; ?> name="type" id="txt" value="text"><label for="txt">Eingabe-Feld</label><br />
			<input type="radio" <?php if($type=="number")echo "checked"; ?> name="type" id="number" value="number"><label for="number">Zahlen-Feld</label><br />
			<input type="radio" <?php if($type=="password")echo "checked"; ?> name="type" id="password" value="password"><label for="password">Passwort-Feld</label><br />
			<input type="radio" <?php if($type=="email")echo "checked"; ?> name="type" id="email" value="email"><label for="email">E-Mail-Feld</label><br />
			<input type="radio" <?php if($type=="textarea")echo "checked"; ?> name="type" id="textarea" value="textarea"><label for="textarea">Text-Feld</label><br />
			<br />
			<input type="hidden" name="editor" value="editor" />
			<input type="submit" value="speichern">
		</form>
		<?php
		return;
	}
}
?>
<p>Hier kannst du ein Formular für Beispielsweise Bestellungen erstellen um es später mit einem Befehl auf einer Seite anzuzeigen.</p>
<table id="table1" class="responsive-table">
	<tr class="head"><th>Beschreibung</th><th>Eingabeffeld-Typ</th><th></th></tr>
	<?php
	$sql = "SELECT * FROM XENUX_form order by label";
	$erg = mysql_query($sql);
	while ($row = mysql_fetch_array($erg)) {
		echo "<tr>";
		echo "<td data-title=\"Beschreibung\">".$row['label']."</td>";
		echo "<td data-title=\"Feld-Typ\">".$row['type']."</a></td>";
		echo "<td data-title=\"\"><a id=\"edit_href\" style=\"font-size: 0.9em;\" href=\"./?site=$site&id=".$row['id']."\">Bearbeiten</a> <a id=\"edit_href\" style=\"font-size: 0.9em;\" href=\"./?site=$site&delid=".$row['id']."\">löschen</a></td>";
		echo "</tr>";
	}
	?>
</table>
<br />
<a id="edit_href" style="font-size: 1em;" href="./?site=<?php echo $site; ?>&new=yes">neues Eingabefeld</a>
<br />
<p>Zum Einbinden des Formulares auf einer Seite, fügen sie folgenden Code hinzu:<br />
<textarea style="height:auto;width:auto;" onclick="this.select()">[form][/form]</textarea>
</p>