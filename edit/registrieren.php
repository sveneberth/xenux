<?php
if($_SERVER['REQUEST_METHOD'] == 'POST') {
	foreach($_POST as $var => $value) {
		$$var = mysql_real_escape_string($value);
	}
	if(preg_match("/[^a-zA-Z0-9_-]/", $username)) {
		echo "Der Benutzername enthält unerlaubte Zeichen, zulässig sind nur Buchstaben, Zahlen und (Unter-)Strich.<br />";
	} else {
		if(!empty($firstname) and !empty($lastname) and !empty($email) and !empty($username) and !empty($password) and $password == $passwordre) {
			$sql = "SELECT COUNT(username) AS anzahl FROM XENUX_users WHERE username = '$username'";
			$erg = mysql_query($sql);
			$res = mysql_fetch_array($erg);
			if($res['anzahl'] >= 1) {
				$username_exist = true;
			} else {
				$username_exist = false;
			}
			$sql = "SELECT COUNT(email) AS anzahl FROM XENUX_users WHERE email = '$email'";
			$erg = mysql_query($sql);
			$res = mysql_fetch_array($erg);
			if($res['anzahl'] >= 1) {
				$email_exist = true;
			} else {
				$email_exist = false;
			}
			if(!$username_exist and !$email_exist) {
				$sql = "INSERT INTO XENUX_users(vorname, nachname, email, username, pw, admin, role) VALUES ('$firstname', '$lastname', '$email', '$username', 'xkanf".md5($password)."v4sf5w', 'no', '0');";
				$erg = mysql_query($sql);
				$Freigabelink = 'http://'.$HP_URL.'edit/?site=freigabe&username='.$username.'&email='.$email;
				$nachricht = '<!Doctype html><html lang="de"><head><meta charset="UTF-8" ><title>Accountfreischaltung</title></head><body>
				Hallo!<br />
				Es hat sich jemand auf der Homepage http://'.$HP_URL.' registriert, er wartet nun auf die Freigabe!<br /><br />
				Name: '.$firstname.' '.$lastname.'<br />
				Datum: '.date("d.m.y H:i:s ").'<br /><br />
				Sie können ihn hier Freischalten: <a href="'.$Freigabelink.'">'.$Freigabelink.'</a><br />
				<br />
				<span style="font-family:Verdana;color:#777;border-top: 1px #777 solid;">Die Mail wurde mit Xenux erstellt</span></body></html>';
				$header		 = 'From: noreply@sven-eberth.de.hm'."\r\n";
				$header		.= 'MIME-Version: 1.0' . "\r\n";
				$header		.= 'Content-type: text/html; charset=utf-8' . "\r\n";
				mail($HP_Email, 'Accountfreischaltung', $nachricht, $header);
				echo '<br />Du wurdest erfolgreich registriert! Nun musst du warten, bis der Administrator deinen Account freischaltet, du wirst darüber per E-Mail benachrichtigt!';
				return;
			} else {
				echo "Es existiert bereits ein Account mit dem Benutzernamen oder der E-Mail-Adresse!<br />";
			}
		} else {
				echo "Alle Felder müssen ausgefüllt sein!<br />";
				if($password != $passwordre) {
					echo "Die eingeben Passwörter stimmen nicht überein!<br />";
				}
		}
	}
}
?>
<form action="" method="POST" name="form">
	<input type="text" name="firstname" placeholder="Vorname" /><br />
	<input type="text" name="lastname" placeholder="Nachname" /><br />
	<input type="email" name="email" placeholder="E-Mail" /><br />
	<input type="text" name="username" placeholder="Benutzername" /><br />
	<input type="password" name="password" placeholder="Passwort" /><br />
	<input type="password" name="passwordre" placeholder="Passwort wiederholen" /><br />
	<input type="submit" value="Registrieren" /><br />
</form>