<?php
if (!empty($_POST["submit"])) {
	$username = mysql_real_escape_string($_POST["username"]);
	$password = mysql_real_escape_string($_POST["password"]);
	$sql = "SELECT * FROM XENUX_users WHERE username='".$username."' AND pw='xkanf".md5($password)."v4sf5w' AND admin = 'yes' LIMIT 1";
	$res = mysql_query($sql);
	$anzahl = mysql_num_rows($res);
	$erg = mysql_fetch_array($res);
	if($anzahl > 0) {
		echo "<p>Der Login war erfolgreich.</p>";
		$_SESSION["login"] = 1;
		$_SESSION["userid"] = $erg['id'];
	} else {
		echo "<p>Deine Logindaten sind nicht korrekt, oder du wurdest noch nicht freigeschaltet.</p>";
	}
	if (@$_SESSION['login'] == 1) {
		$sql = "SELECT * FROM XENUX_users WHERE id = '".$_SESSION['userid']."'";
		$erg = mysql_query($sql);
		$login = mysql_fetch_array($erg);
	}
}

if(@$_SESSION["login"] == 0) {
?>
	<p>Um die Homepage zu bearbeiten, musst du dich zuerst anmelden!<br />
	Falls du noch keine Account hast kannst du dich <a href="./?site=registrieren">Registrieren</a>.</p>
	<form method="POST" action="./?site=login">
	<input type="text" name="username" size="40" placeholder="Benutzername"><br />
	<a href="./?site=forgotusername">Benutzernamen vergessen?</a><br /><br />
	<input type="password" name="password" size="40" placeholder="Passwort"><br />
	<a href="./?site=forgotpassword">Passwort vergessen?</a><br /><br />
	<input type="submit" name="submit" value="Einloggen"></form>
	<p><a href="./?site=registrieren">Registrieren</a></p>
<?php
} elseif($_SESSION['login'] == 1) {
	echo "<p>Hallo ".$login['vorname'].", du bist erfolgreich eingeloggt!<br />";
	echo "Beginne nun die Homepage über den <a href=\"?site=editroom\">Editroom</a> zu bearbeiten!</p>";
}
?>