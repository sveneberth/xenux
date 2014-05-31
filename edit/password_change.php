<?php
if(!isset($site)) die("You can not open this file individually/Sie k&ouml;nnen diese Datei nicht einzeln &ouml;ffnen!");
if(!empty($_POST['form'])) {
	foreach($_POST as $key => $val) {
		$$key = mysql_real_escape_string($val);
	}
	if(!empty($password0) and !empty($password) and !empty($password1)) {
		$sql = "SELECT * FROM XENUX_users WHERE id = '".$_SESSION['userid']."'";
		$erg = mysql_query($sql);
		$row = mysql_fetch_array($erg);
		if($row['pw'] != 'xkanf'.md5($password0).'v4sf5w') {
			echo 'Das eingegebene Passwort stimmt nicht mit dem jetzigen überein!';
		} else {
			if($password != $password1) {
				echo 'Die eingegebenen Passwörter stimmen nicht überein!';
			} else {
				$sql = "UPDATE XENUX_users Set pw = 'xkanf".md5($password)."v4sf5w' WHERE id = '".$_SESSION['userid']."'";
				$erg = mysql_query($sql);
				echo 'Das Passwort wurde geändert!';
			}
		}
	}
}
?>
<p>Hier kannst du dein Passwort ändern.</p>
<form action="" method="post">
	<span <?php if (empty($password0) and $_SERVER['REQUEST_METHOD'] == "POST"){echo 'style="color:#cc0000;"';} ?>>altes Passwort:</span><br />
	<input type="password" name="password0" /><br /><br />
	<span <?php if (empty($password) and $_SERVER['REQUEST_METHOD'] == "POST"){echo 'style="color:#cc0000;"';} ?>>neues Passwort:</span><br />
	<input type="password" name="password" /><br /><br />
	<span <?php if (empty($password1) and $_SERVER['REQUEST_METHOD'] == "POST"){echo 'style="color:#cc0000;"';} ?>>Passwort bestätigen</span><br />
	<input type="password" name="password1" /><br /><br />
	<input type="hidden" name="form" value="form" />
	<input type="submit" value="ändern" />
</form>