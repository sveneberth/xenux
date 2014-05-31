<?php
$password0 = "";
$password = "";
$password1 = "";
$result = "";
if(!empty($_POST['sub1'])) {
	$password0 = mysql_real_escape_string($_POST['password0']);
	$password = mysql_real_escape_string($_POST['password']);
	$password1 = mysql_real_escape_string($_POST['password1']);
	if(!empty($password0) and !empty($password) and !empty($password1)) {
		$sql = "SELECT * FROM XENUX_users WHERE username = '".$_SESSION['user']['username']."'";
		$erg = mysql_query($sql);
		$row = mysql_fetch_object($erg);
		if($row->pw != 'xkanf'.md5($password0).'v4sf5w') {
			$result = 'Das eingegebene Passwort stimmt nicht mit dem jetzigen überein!';
		}else{
			if($password != $password1) {
				$result = 'Die eingegebenen Passwörter stimmen nicht überein!';
			}else{
				$sql = "UPDATE XENUX_users Set pw = 'xkanf".md5($password)."v4sf5w' WHERE username = '".$_SESSION['user']['username']."'";
				$erg = mysql_query($sql);
				$result = 'Das Passwort wurde geändert!';
			}
		}
	}
}
?>
<p>Hier kannst du dein Passwort ändern.</p>
<form action="" method="post">
	<span <?php if (empty($password0) and $_SERVER['REQUEST_METHOD'] == "POST"){echo 'style="color:#cc0000;"';} ?>>altes Passwort:</span><br />
	<input type="password" name="password0" size="70" /><br /><br />
	<span <?php if (empty($password) and $_SERVER['REQUEST_METHOD'] == "POST"){echo 'style="color:#cc0000;"';} ?>>neues Passwort:</span><br />
	<input type="password" name="password" size="70" /><br /><br />
	<span <?php if (empty($password1) and $_SERVER['REQUEST_METHOD'] == "POST"){echo 'style="color:#cc0000;"';} ?>>Passwort bestätigen</span><br />
	<input type="password" name="password1" size="70" />
	<p><?php echo $result ?></p>
	<input name="sub1" type="submit" value="ändern">
</form>