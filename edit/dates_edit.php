<?php
if(!isset($site)) die("You can not open this file individually/Sie k&ouml;nnen diese Datei nicht einzeln &ouml;ffnen!");
$form = false;
if(isset($_GET['new'])) {
	if($_GET['new'] == "yes") {
		$sql = "INSERT INTO XENUX_dates(id) VALUES (NULL);";
		$erg = mysql_query($sql);
		$sql = "SELECT * FROM XENUX_dates ORDER by id DESC LIMIT 1;";
		$erg = mysql_query($sql);
		$row = mysql_fetch_array($erg);
		$_GET['id'] = $row['id'];
	}
}
if(!empty($_GET['deldate'])) {
	$sql = "DELETE FROM XENUX_dates WHERE id = '".$_GET['deldate']."';";
	$erg = mysql_query($sql);
	echo '<p>Der Termin wurde soeben erfolgreich gelöscht!</p>';
}
if(!empty($_GET['id'])) {
$id = mysql_real_escape_string($_GET['id']);
	if(isset($_POST['form'])) {
		$name = mysql_real_escape_string($_POST['name']);
		$dat = mysql_real_escape_string($_POST['dat']);
		$time = mysql_real_escape_string($_POST['time']);
		$text = mysql_real_escape_string($_POST['text']);
			$sql = "UPDATE XENUX_dates Set name = '$name', date = '$dat $time', text = '$text' WHERE id = '$id'";
			$erg = mysql_query($sql);
			echo "<p>Der Termin wurde gespeichert!</p>";
			echo "<p><a href='../?site=terminview&id=$id'>Termin anzeigen</a></p>";
			$form = false;
	} else {
		$sql = "SELECT *, DATE_FORMAT(date,'%Y-%m-%d') as dat, DATE_FORMAT(date,'%H:%i:%s') as time FROM XENUX_dates WHERE id = '".$id."'";
		$erg = mysql_query($sql);
		$row = mysql_fetch_array($erg);
		foreach($row as $key => $val) {
			$$key = $val;
		}
		$form = true;
	}
	if($form){
	?>
	<form action="<?php echo "?site=$site&id=$id"; ?>" method="post">
		<span <?php if(empty($name) and $_SERVER['REQUEST_METHOD'] == "POST"){echo 'style="color:#cc0000;"';} ?>>Name:</span><br />
		<input type="text" name="name" placeholder="Name" value="<?php echo $name; ?>" /><br /><br />
		<span <?php if(empty($dat) and $_SERVER['REQUEST_METHOD'] == "POST"){echo 'style="color:#cc0000;"';} ?>>Datum:</span><br />
		<input type="date" name="dat" placeholder="Datum" value="<?php echo $dat; ?>" /><br /><br />
		<span <?php if (empty($time) and $_SERVER['REQUEST_METHOD'] == "POST"){echo 'style="color:#cc0000;"';} ?>>Zeit:</span><br />
		<input type="time" name="time" placeholder="Zeit" value="<?php echo $time; ?>" /><br /><br />
		<span <?php if(empty($text) and $_SERVER['REQUEST_METHOD'] == "POST"){echo 'style="color:#cc0000;"';} ?>>Text:</span><br />
		<textarea type="text" name="text" placeholder="Text" class="big"><?php echo $text; ?></textarea><br /><br />
		<input type="hidden" name="form" value="form" />
		<input type="submit" value="speichern">
	</form>
	<?php
	return;
	}
}
?>
<p>Hier kannst du die Termine bearbeiten.</p>
<br />
<table id="table1" class="responsive-table">
<tr class="head"><th>Name</th><th>Text</th><th>Datum</th><th></th></tr>
<?php
$sql = "SELECT *, DATE_FORMAT(date,'%d.%m.%Y %H:%i') as dat FROM XENUX_dates ORDER by date";
$erg = mysql_query($sql);
while($row = mysql_fetch_array($erg)) {
	echo "<tr>";
	echo "<td data-title=\"Name\">".$row['name']."</td>";
	echo "<td data-title=\"Text\">";
	if(strlen($row['text']) > 300) {
		echo htmlentities(substr($row['text'], 0, strpos($row['text'], " ", 300)))."...";
	} else {
		echo htmlentities($row['text']);
	}
	echo "</td>";
	echo "<td data-title=\"Datum\">".$row['dat']."</td>";
	echo "<td data-title=\"\"><a id=\"edit_href\" style=\"font-size: 0.9em;\" href=\"./?site=$site&id=".$row['id']."\">Bearbeiten</a> <a id=\"edit_href\" style=\"font-size: 0.9em;\" href=\"./?site=$site&deldate=".$row['id']."\">löschen</a></td>";
	echo "</tr>";
}
?>
</table>
<br />
<br />
<a id="edit_href" style="font-size: 1em;" href="./?site=<?php echo $site; ?>&new=yes">neuer Termin</a>