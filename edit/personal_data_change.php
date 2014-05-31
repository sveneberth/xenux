<?php
if(!isset($site)) die("You can not open this file individually/Sie k&ouml;nnen diese Datei nicht einzeln &ouml;ffnen!");
if(isset($_POST['form'])) {
	foreach($_POST as $key => $val) {
		$$key = mysql_real_escape_string($val);
	}
	if(!empty($vorname) and !empty($nachname) and !empty($email)) {
		$sql = "UPDATE XENUX_users Set vorname = '$vorname', nachname = '$nachname', username = '$username', email = '$email' WHERE id = '".$_SESSION['userid']."'";
		$erg = mysql_query($sql);
		echo "Die Daten wurden geändert!";
	}
} else {
	$sql = "SELECT * FROM XENUX_users WHERE id = '".$_SESSION['userid']."'";
	$erg = mysql_query($sql);
	$row = mysql_fetch_array($erg);
	foreach($row as $key => $val) {
		$$key = $val;
	}
}
?>
<p>Hier kannst du deine Daten ändern.</p>
<form action="" method="post">
	<span <?php if(empty($vorname) and $_SERVER['REQUEST_METHOD'] == "POST"){echo 'style="color:#cc0000;"';} ?>>Vorname</span><br />
	<input type="text" name="vorname" value="<?php echo @$vorname; ?>" /><br /><br />
	<span <?php if(empty($vorname) and $_SERVER['REQUEST_METHOD'] == "POST"){echo 'style="color:#cc0000;"';} ?>>Nachname</span><br />
	<input type="text" name="nachname" value="<?php echo @$nachname; ?>" /><br /><br />
	<span <?php if(empty($username) and $_SERVER['REQUEST_METHOD'] == "POST"){echo 'style="color:#cc0000;"';} ?>>Benutzername</span><br />
	<input type="text" name="username" value="<?php echo @$username; ?>" /><br /><br />
	<span <?php if(empty($vorname) and $_SERVER['REQUEST_METHOD'] == "POST"){echo 'style="color:#cc0000;"';} ?>>E-Mail</span><br />
	<input type="text" name="email" value="<?php echo @$email; ?>" /><br /><br />
	<input type="hidden" name="form" value="form" />
	<input type="submit" value="ändern" />
</form>