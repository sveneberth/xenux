<?php
if(!isset($site)) die("You can not open this file individually/Sie k&ouml;nnen diese Datei nicht einzeln &ouml;ffnen!");
$form = false;
if(isset($_GET['new']) and $_GET['new'] == "yes") {
	$sql = "INSERT INTO XENUX_news(title, text) VALUES ('Titel','Text')";
	$erg = mysql_query($sql);
}
if(!empty($_GET['delnews'])) {
	$sql = "DELETE FROM XENUX_news WHERE id = '".$_GET['delnews']."'";
	$erg = mysql_query($sql);
	echo 'Die News wurde soeben erfolgreich gelöscht!<br />';
}
if(!empty($_GET['id'])) {
$id = mysql_real_escape_string($_GET['id']);
	if(isset($_POST['form'])) {
		$title = mysql_real_escape_string($_POST['title']);
		$text = mysql_real_escape_string($_POST['text']);
			$sql = "UPDATE XENUX_news Set title = '$title', text = '$text' WHERE id = '$id'";
			$erg = mysql_query($sql);
			echo "<p>Die News wurde gespeichert!</p>";
			echo "<p><a href='../?site=news&id=$id'>News anzeigen</a></p>";
			$form = false;
	} else {
		$sql = "SELECT * FROM XENUX_news WHERE id = '".$id."'";
		$erg = mysql_query($sql);
		$row = mysql_fetch_array($erg);
		foreach($row as $key => $val) {
			$$key = $val;
		}
		$form = true;
	}
	if($form){
	?>
	<form action="" method="post">
		<span <?php if (empty($title) and $_SERVER['REQUEST_METHOD'] == "POST"){echo 'style="color:#cc0000;"';} ?>>Titel:</span><br />
		<input type="text" name="title" value="<?php echo $title; ?>" /><br /><br />
		<span <?php if (empty($text) and $_SERVER['REQUEST_METHOD'] == "POST"){echo 'style="color:#cc0000;"';} ?>>Text:</span><br />
		<textarea type="text" name="text" class="big"><?php echo $text; ?></textarea><br /><br />
		<input type="hidden" name="form" value="form" />
		<input type="submit" value="speichern" />
	</form>
	<?php
	return;
	}
}
?>
<p>Hier kannst du die News bearbeiten.</p>
<br />
<table id="table1" class="responsive-table">
<tr class="head"><th>Titel</th><th>Text</th><th></th></tr>
<?php
$sql = "SELECT * FROM XENUX_news";
$erg = mysql_query($sql);
while($row = mysql_fetch_array($erg)) {
	echo "<tr>";
	echo "<td data-title=\"Titel\">".$row['title']."</td>";
	echo "<td data-title=\"Text\">";
	if(strlen($row['text']) > 300) {
		echo substr($row['text'], 0, strpos($row['text'], " ", 300))."...";
	} else {
		echo $row['text'];
	}
	echo "</td>";
	echo "<td data-title=\"\"><a id=\"edit_href\" style=\"font-size: 0.9em;\" href=\"./?site=$site&id=".$row['id']."\">Bearbeiten</a> <a id=\"edit_href\" style=\"font-size: 0.9em;\" href=\"./?site=$site&delnews=".$row['id']."\">löschen</a></td>";
	echo "</tr>";
}
?>
</table>
<br />
<br />
<a id="edit_href" style="font-size: 1em;" href="./?site=<?php echo $site; ?>&new=yes">neue News</a>