<?php
if(isset($_GET['verifykey']) && isset($_GET['user'])) {
	$result = $db->query("SELECT * FROM XENUX_users WHERE SHA1(id) = '$get->user' AND verifykey = '$get->verifykey' LIMIT 1;");
	$num = $result->num_rows;
	if($num == 0) { // data false
		echo "<p>Es trat ein Fehler auf... Stellen sie sicher, das der Link stimmt und aktuell ist.";
		return false;
	}
	$user = $result->fetch_object();
	if(isset($_POST['newpassword']) && isset($_POST['newpasswordre'])) {
		if($_POST['newpassword'] == $_POST['newpasswordre']) { // if equal
			// update
			$result = $db->query("UPDATE XENUX_users Set password = SHA1('{$_POST['newpassword']}'), verifykey = '' WHERE id = '$user->id';");
			echo "<p>Das Passwort wurde erfolgreich zurückgesetzt!</p>";
			return false;
		} else {
			echo "<p>Die eingegeben Passwörter sind nicht identisch!<p>";
		}
	}
	// print form
	echo <<<EOF
<form action="" method="POST">
	<input type="password" name="newpassword" placeholder="Passwort" />
	<input type="password" name="newpasswordre" placeholder="Passwort wiederholen" />
	<input type="submit" value="zurücksetzen" />
</form>
EOF;
	return false;
}
$verifykey = base64_encode(time());
if(isset($_POST['resetusername'])) {
	$username = $db->real_escape_string($_POST['resetusername']);
	if(!empty($username)) {
		$result = $db->query("SELECT * FROM XENUX_users WHERE username = '$username';");
		$num = $result->num_rows;
		if($num == 0) {
			echo "<p>Es konnte keinem Account der Benutzername <i>$username</i> zugeordnet werden. Bitte stelle sicher, das der Benutzername stimmt, und ob du dich bereits registriert hast.</p>";
		} else {
			$result = $db->query("SELECT * FROM XENUX_users WHERE username = '$username' LIMIT 1;");
			$user = $result->fetch_object();
			$result = $db->query("UPDATE XENUX_users Set verifykey = '$verifykey' WHERE username = '$username';");
			$mailtxt = 
"<!DOCTYPE html>
<html>
	<head>
		<meta charset=\"UTF-8\" />
		<title>Passwort vergessen</title>
	</head>
	<body>
		Hallo $user->firstname $user->lastname,<br />
		<p>Sie haben am ".date("d.m.Y")." um ".date("H:i")." von der IP-Adresse {$_SERVER['REMOTE_ADDR']} eine Passwortrücksetzung angefordert. Das Passwort kann unter der URL<br />
		<a href=\"$XENUX_URL/edit?site=forgotpassword&user=".SHA1($user->id)."&verifykey=$verifykey\">$XENUX_URL/edit?site=forgotpassword&user=".SHA1($user->id)."&verifykey=$verifykey</a><br />
		neu gesetzt werden.</p>
		<p>Sollten Sie nicht selbst diese Rücksetzung angefordert haben, handelt es sich wohl um einen Fehler (z.B. bei der Eingabe des Benutzernamens vertippt), bitte in so einem Fall einfach diese E-Mail ignorieren.</p>
		<br /><br />
		<span style=\"font-family:Verdana;color:#777;border-top: 1px #777 solid;\">Diese E-Mail wurde mit Xenux generiert und versendet.</span>
	</body>
</html>";
			$header		 = "From: $main->reply_email \r\n";
			$header		.= 'MIME-Version: 1.0' . "\r\n";
			$header		.= 'Content-type: text/html; charset=utf-8' . "\r\n";
			mail($user->email, "Passwortwortruecksetzung Xenux", $mailtxt, $header) 
				or die('es trat ein Fehler auf...');
			echo "<p>Bitte öffne nun in der dir soeben zu gesendeten E-Mail den Link, um das Passwort zurückzusetzen.</p>";
			return false;
		}
	}
}
?>
<p>Falls du deinen Passwort vergessen hast, kannst du es über einen Link aus der gesendeten E-Mail zurücksetzten.</p>
<form action="" method="post">
	<input type="text" placeholder="Benutzername" name="resetusername" />
	<input type="submit" value="Passwort zurücksetzten">
</form>