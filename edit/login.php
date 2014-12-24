<?php
if(!empty($_POST["submit_login"])) {
	$result = $db->query("SELECT * FROM XENUX_users WHERE username = '$post->username' AND password = SHA1('{$_POST['password']}') AND confirmed = true LIMIT 1;");
	$num = $result->num_rows;
	if($num > 0) {
		$login = $result->fetch_object();
		$result = $db->query("UPDATE XENUX_users SET lastlogin_date = NOW(), lastlogin_ip = '{$_SERVER['REMOTE_ADDR']}' WHERE id = '{$_SESSION['userid_xenux']}';");
		echo "<p>Der Login war erfolgreich.</p>";
		$_SESSION["login_xenux"] = 1;
		$_SESSION['userid_xenux'] = $login->id;
	} else {
		echo "<p>Deine Logindaten sind nicht korrekt, oder du wurdest noch nicht freigeschaltet.</p>";
	}
}

if(!isset($login)) {
?>
	<p>Um die Homepage zu bearbeiten, musst du dich zuerst anmelden!<br />
	Falls du noch keine Account hast kannst du dich <a href="./?site=registrieren">Registrieren</a>.</p>
	<form method="POST" action="./?site=login">
		<input type="text" name="username" placeholder="Benutzername">
		<a href="./?site=forgotusername">Benutzernamen vergessen?</a><br /><br />
		<input type="password" name="password" placeholder="Passwort">
		<a href="./?site=forgotpassword">Passwort vergessen?</a>
		<input type="hidden" name="submit_login" value="true">
		<input type="submit" value="Einloggen">
	</form>
	<p><a href="./?site=register">Registrieren</a></p>
<?php
} else {
	echo "<p>Hallo $login->firstname, du bist erfolgreich eingeloggt!<br />";
	echo "Beginne nun die Homepage über den <a href=\"?site=editroom\">Editroom</a> zu bearbeiten!</p>";
}
?>