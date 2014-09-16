<?php
if(!isset($site)) echo("You can not open this file individually/Sie k&ouml;nnen diese Datei nicht einzeln &ouml;ffnen!");
if($login['role'] < 1) {
	echo '<p>Du bist nicht berechtigt, diese Seite zu öffnen!</p>';
	return;
}
$form = false;
if(isset($_GET['new'])) {
	if($_GET['new'] == "yes") {
		$sql = "INSERT INTO XENUX_ansprechpartner(id) VALUES(NULL);";
		$erg = mysql_query($sql);
		$sql = "SELECT * FROM XENUX_ansprechpartner ORDER by id DESC LIMIT 1;";
		$erg = mysql_query($sql);
		$row = mysql_fetch_array($erg);
		$_GET['id'] = $row['id'];
	}
}
if(!empty($_GET['delid'])) {
	$sql = "DELETE FROM XENUX_ansprechpartner WHERE id = '".$_GET['delid']."';";
	$erg = mysql_query($sql);
	echo '<p>Der Ansprechpartner wurde soeben erfolgreich gelöscht!</p>';
}
if(!empty($_GET['id'])) {
$id = mysql_real_escape_string($_GET['id']);
	if(isset($_POST['form'])) {
		foreach($_POST as $key => $val) {
			$$key = mysql_real_escape_string(nl2br(htmlentities($val)));
		}
		$sql = "UPDATE XENUX_ansprechpartner Set name = '$name', position = '$position', email = '$email', text = '$text' WHERE id = '$id';";
		$erg = mysql_query($sql);
		echo "<p>Der Ansprechpartner wurde gespeichert!</p>";
		$form = false;
	} else {
		$sql = "SELECT * FROM XENUX_ansprechpartner WHERE id = '".$id."'";
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
		<span <?php if (empty($name) and $_SERVER['REQUEST_METHOD'] == "POST"){echo 'style="color:#cc0000;"';} ?>>Name:</span><br />
		<input type="text" name="name" placeholder="Name" value="<?php echo $name; ?>" /><br /><br />
		<span <?php if (empty($position) and $_SERVER['REQUEST_METHOD'] == "POST"){echo 'style="color:#cc0000;"';} ?>>Position:</span><br />
		<input type="text" name="position" placeholder="Position" value="<?php echo $position; ?>" /><br /><br />
		<span <?php if (empty($email) and $_SERVER['REQUEST_METHOD'] == "POST"){echo 'style="color:#cc0000;"';} ?>>E-Mail:</span><br />
		<input type="email" name="email" placeholder="E-Mail" value="<?php echo $email; ?>" /><br /><br />
		<span <?php if (empty($text) and $_SERVER['REQUEST_METHOD'] == "POST"){echo 'style="color:#cc0000;"';} ?>>Text:</span><br />
		<textarea type="text" name="text" placeholder="Text" maxlength="250" cols="90" rows="3"><?php echo $text; ?></textarea><br /><br />
		<input type="hidden" name="form" value="form" />
		<input type="submit" value="speichern" />
	</form>
	<?php
	return;
	}
}
?>
<p>Hier kannst du die Ansprechpartner bearbeiten.</p>
<br />
<table id="table1" class="responsive-table">
<tr class="head"><th>Name</th><th>Position</th><th>E-Mail</th><th>Beschreibung</th><th></th></tr>
<?php
$sql = "SELECT * FROM XENUX_ansprechpartner";
$erg = mysql_query($sql);
while($row = mysql_fetch_array($erg)) {
	echo "<tr>";
	echo "<td data-title=\"Name\">".$row['name']."</td>";
	echo "<td data-title=\"Position\">".$row['position']."</td>";
	echo "<td data-title=\"E-Mail\">".$row['email']."</td>";
	echo "<td data-title=\"Beschreibung\">".$row['text']."</td>";
	echo "<td data-title=\"\"><a id=\"edit_href\" style=\"font-size: 0.9em;\" href=\"./?site=$site&id=".$row['id']."\">Bearbeiten</a> <a id=\"edit_href\" style=\"font-size: 0.9em;\" href=\"./?site=$site&delid=".$row['id']."\">löschen</a></td>";
	echo "</tr>";
}
?>
</table>
<br />
<br />
<a id="edit_href" style="font-size: 1em;" href="./?site=<?php echo $site; ?>&new=yes">neue Ansprechpartner</a>