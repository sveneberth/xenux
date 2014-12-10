<?php
if(isset($_POST['submit_register'])) {
	if(preg_match("/[^a-zA-Z0-9_-]/", $post->username)) {
		echo "<p>Der Benutzername enthält unerlaubte Zeichen, zulässig sind nur Buchstaben, Zahlen und (Unter-)Strich.</p>";
	} else {
		if(!empty($post->firstname) and !empty($post->lastname) and !empty($post->email) and !empty($post->username) and !empty($post->password) and $post->password == $post->passwordre) {
			$result = $db->query("SELECT * FROM XENUX_users WHERE username = '$post->username';");
			if($result->num_rows >= 1) {
				$username_exist = true;
			} else {
				$username_exist = false;
			}
			$result = $db->query("SELECT * FROM XENUX_users WHERE email = '$post->email';");
			if($result->num_rows >= 1) {
				$email_exist = true;
			} else {
				$email_exist = false;
			}
			if(!$username_exist and !$email_exist) {
				$db->query("INSERT INTO XENUX_users(firstname, lastname, email, username, password, confirmed, role) VALUES ('$post->firstname', '$post->lastname', '$post->email', '$post->username', SHA1('$post->password'), false, 0);");

				$confirmlink = BASEURL.'/edit/?site=confirm&username='.$post->username.'&email='.$post->email;
				$message = '<!DOCTYPE html><html lang="de"><head><meta charset="UTF-8" ><title>Accountfreischaltung</title></head><body>
				Hallo!<br />
				Es hat sich jemand auf der Homepage '.BASEURL.' registriert, er wartet nun auf die Freigabe!<br /><br />
				Name: '.$post->firstname.' '.$post->lastname.'<br />
				Datum: '.date("d.m.y H:i:s").'<br /><br />
				Du kannst ihn hier Freischalten: <a href="'.$confirmlink.'">'.$confirmlink.'</a><br />
				<br />
				<span style="font-family:Verdana;color:#777;border-top: 1px #777 solid;">Diese E-Mail wurde mit Xenux erstellt</span></body></html>';
				$header		 = "From: $main->noreplay_email \r\n";
				$header		.= 'MIME-Version: 1.0' . "\r\n";
				$header		.= 'Content-type: text/html; charset=utf-8' . "\r\n";
				mail($main->noreplay_email, 'Accountfreischaltung', $message, $header);
				echo "<p>Du wurdest erfolgreich registriert! Nun musst du warten, bis der Administrator deinen Account freischaltet, du wirst darüber per E-Mail benachrichtigt!</p>";
				return;
			} else {
				echo "<p>Es existiert bereits ein Account mit dem Benutzernamen oder der E-Mail-Adresse!</p>";
			}
		} else {
				echo "<p>Alle Felder müssen richtig ausgefüllt sein!</p>";
				if($post->password != $post->passwordre) {
					echo "<p>Die eingeben Passwörter stimmen nicht überein!</p>";
				}
		}
	}
}
?>
<form action="" method="POST" name="form">
	<input <?php if(empty(@$post->firstname) && isset($post->firstname)) echo 'class="wrong"'; ?> type="text" name="firstname" placeholder="Vorname" value="<?php echo @$post->firstname; ?>" />
	<input <?php if(empty(@$post->lastname) && isset($post->lastname)) echo 'class="wrong"'; ?> type="text" name="lastname" placeholder="Nachname" value="<?php echo @$post->lastname; ?>" />
	<input <?php if(empty(@$post->email) && isset($post->email)) echo 'class="wrong"'; ?> type="email" name="email" placeholder="E-Mail" value="<?php echo @$post->email; ?>" />
	<input <?php if(empty(@$post->username) && isset($post->username)) echo 'class="wrong"'; ?> type="text" name="username" placeholder="Benutzername" value="<?php echo @$post->username; ?>" />
	<input <?php if(empty(@$post->password) && isset($post->password)) echo 'class="wrong"'; ?> type="password" name="password" placeholder="Passwort" />
	<input <?php if(empty(@$post->passwordre) && isset($post->passwordre)) echo 'class="wrong"'; ?> type="password" name="passwordre" placeholder="Passwort wiederholen" />
	<input type="hidden" name="submit_register" value="true" />
	<input type="submit" value="Registrieren" />
</form>