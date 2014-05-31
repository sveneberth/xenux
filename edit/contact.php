<?php
if(!isset($site)) echo("You can not open this file individually/Sie k&ouml;nnen diese Datei nicht einzeln &ouml;ffnen!");
if($login['role'] == '0') {
	echo '<p>Du bist nicht berechtigt, diese Seite zu öffnen!</p>';
	return;
}
$form = false;
if(isset($_GET['newAnsprechpartner'])) {
	if($_GET['newAnsprechpartner'] == "yes") {
		$sql = "INSERT INTO `XENUX_ansprechpartner`(`id`, `name`, `email`, `text`) VALUES (NULL, 'Name', 'E-Mail', 'Ich bin ein Ansprechpartner')";
		$erg = mysql_query($sql);
	}
}
if(!empty($_GET['delid'])) {
	$sql = "DELETE FROM XENUX_ansprechpartner WHERE id = '".$_GET['delid']."'";
	$erg = mysql_query($sql);
	echo 'Der Ansprechpartner wurde soeben erfolgreich gelöscht!<br />';
}
if(!empty($_GET['id'])) {
$id = mysql_real_escape_string($_GET['id']);
	if(isset($_POST['sub2'])) {
		$name = mysql_real_escape_string(htmlentities($_POST['name']));
		$position = mysql_real_escape_string(htmlentities($_POST['position']));
		$email = mysql_real_escape_string(htmlentities($_POST['email']));
		$text = mysql_real_escape_string(nl2br(htmlentities($_POST['text'])));
			$sql = "UPDATE XENUX_ansprechpartner Set name = '$name',position = '$position', email = '$email', text = '$text' WHERE id = '$id'";
			$erg = mysql_query($sql);
			echo "Der Ansprechpartner wurde gespeichert!<br />";
			echo "<a href='./?site=$site'>Weitere Ansprechpartner bearbeiten</a><br />";
			return;
	}else {
		echo 'Du bearbeitest gerade den Ansprechpartner '.$id.'!';
		$sql = "SELECT * FROM XENUX_ansprechpartner WHERE id = '".$id."'";
		$erg = mysql_query($sql);
		$row = mysql_fetch_array($erg);
		$name = $row['name'];
		$position = $row['position'];
		$email = $row['email'];
		$text = $row['text'];
		$form = true;
	}
		if($form){
		?>
		<form action="" method="post">
			<span <?php if (empty($name) and $_SERVER['REQUEST_METHOD'] == "POST"){echo 'style="color:#cc0000;"';} ?>>Name:</span><br />
			<input type="text" name="name" size="70" value="<?php echo $name; ?>" /><br /><br />
			<span <?php if (empty($position) and $_SERVER['REQUEST_METHOD'] == "POST"){echo 'style="color:#cc0000;"';} ?>>Position:</span><br />
			<input type="text" name="position" size="70" value="<?php echo $position; ?>" /><br /><br />
			<span <?php if (empty($email) and $_SERVER['REQUEST_METHOD'] == "POST"){echo 'style="color:#cc0000;"';} ?>>E-Mail:</span><br />
			<input type="email" name="email" size="70" value="<?php echo $email; ?>" /><br /><br />
			<span <?php if (empty($text) and $_SERVER['REQUEST_METHOD'] == "POST"){echo 'style="color:#cc0000;"';} ?>>Text:</span><br />
			<textarea type="text" name="text" maxlength="250" cols="90" rows="3"><?php echo $text; ?></textarea><br /><br />
			<input type="submit" name="sub2" value="speichern">
		</form>
		<?php
		return;
		}
}
?>
<p>Hier kannst du die Ansprechpartner bearbeiten.</p>
<br />
<table id="table1">
<tr><th>Name</th><th>Position</th><th>E-Mail</th><th>Beschreibung</th><th></th><th></th></tr>
<?php
$sql = "SELECT * FROM XENUX_ansprechpartner";
$erg = mysql_query($sql);
while($row = mysql_fetch_array($erg)) {
	echo "<tr>";
	echo "<td>".$row['name']."</td>";
	echo "<td>".$row['position']."</td>";
	echo "<td>".$row['email']."</td>";
	echo "<td>".$row['text']."</td>";
	echo "<td><a id=\"edit_href\" style=\"font-size: 0.9em;\" href=\"./?site=$site&id=".$row['id']."\">Bearbeiten</a></td>";
	echo "<td><a id=\"edit_href\" style=\"font-size: 0.9em;\" href=\"./?site=$site&delid=".$row['id']."\">löschen</a></td>";
	echo "</tr>";
}
?>
</table>
<br />
<br />
<a id="edit_href" style="font-size: 1em;" href="./?site=<?php echo $site; ?>&newAnsprechpartner=yes">neue Ansprechpartner</a>