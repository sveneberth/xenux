<?php
include("../config.php");
$link = mysql_connect($MYSQL_HOST, $MYSQL_BENUTZER, $MYSQL_KENNWORT);
$db_selected = mysql_select_db($MYSQL_DATENBANK, $link);
if(!$db_selected){
	echo 'Es ist keine Verbindung zur Datenbank möglich!';
	exit;
}
if(isset($_POST['submit'])) {
	foreach($_POST as $key => $val) {
		$$key = mysql_real_escape_string($val);
	}
	if($password == $password1) {
		$pw_stimmt = true;
	} else {
		$pw_stimmt = false;
	}

	if(!empty($FirstName) and !empty($LastName) and !empty($eMail) and !empty($username) and !empty($password) and $pw_stimmt){
		$sql = "SELECT COUNT(username) AS anzahl FROM XENUX_users WHERE username = '$username'";
		$erg = mysql_query($sql);
		$row = mysql_fetch_array($erg);
		if($row["anzahl"] >= 1) {
			$username_exist = true;
		} else {
			$username_exist = false;
		}
		$sql = "SELECT COUNT(email) AS anzahl FROM XENUX_users WHERE email = '$email'";
		$erg = mysql_query($sql);
		$row = mysql_fetch_array($erg);
		if($row["anzahl"] >= 1) {
			$email_exist = true;
		} else {
			$email_exist = false;
		}
		if(!$username_exist and !$email_exist) {
			$sql = "INSERT INTO XENUX_users (nachname, vorname, email, username, pw, admin, role) VALUES ('$LastName', '$FirstName', '$email', '$username', 'xkanf".md5($password)."v4sf5w', 'yes', '3');";
			$erg = mysql_query($sql) or die("Anfrage fehlgeschlagen.");
			echo "<p>Sie wurden erfolgreich registriert!</p>";
			$next = true;
		}
		mysql_close($link);
	}
}
?>
<form action="" method="post">
	<span <?php if (empty($FirstName) and $_SERVER['REQUEST_METHOD'] == "POST"){echo 'style="color:#cc0000;"';} ?>>Vorname</span><br />
	<input type="text" name="FirstName" value="<?php echo $FirstName; ?>" /><br /><br />
	<span <?php if (empty($LastName) and $_SERVER['REQUEST_METHOD'] == "POST"){echo 'style="color:#cc0000;"';} ?>>Nachname</span><br />
	<input type="text" name="LastName" value="<?php echo $LastName; ?>" /><br /><br />
	<span <?php if (empty($email) and $_SERVER['REQUEST_METHOD'] == "POST"){echo 'style="color:#cc0000;"';} ?>>E-Mail</span><br />
	<input type="email" name="email" value="<?php echo $email; ?>" /><br />
	<?php
	if($email_exist){echo 'Ein Account mit dieser E-Mail-Adresse existiert schon, zwei Accounts über eine E-Mail Adresse sind nicht zulässig!<br />';}
	?><br />
	<span <?php if (empty($username) and $_SERVER['REQUEST_METHOD'] == "POST"){echo 'style="color:#cc0000;"';} ?>>Benutzername</span><br />
	<input type="text" name="username" value="<?php echo $username; ?>" /><br />
	<?php
	if($username_exist){echo 'Der Benutzername ist schon vergeben, bitte wähle einen anderen!<br />';}
	?><br />
	<span <?php if (empty($password) and $_SERVER['REQUEST_METHOD'] == "POST"){echo 'style="color:#cc0000;"';} ?>>Passwort</span><br />
	<input type="password" name="password" value="<?php echo $password; ?>" /><br /><br />
	<span <?php if (empty($password1) and $_SERVER['REQUEST_METHOD'] == "POST"){echo 'style="color:#cc0000;"';} ?>>Passwort bestätigen</span><br />
	<input type="password" name="password1" value="<?php echo $password1; ?>" /><br />
	<?php
	if(!$pw_stimmt){echo 'Die angegebenen Passwörter stimmen nicht überein!<br />';}
	?>
	<input type="hidden" name="submit" value="submit" />
	<input type="submit" value="speichern"/>
</form>