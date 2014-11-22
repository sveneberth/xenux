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
				$db->query("INSERT INTO XENUX_users(firstname, lastname, email, username, password, confirmed, role) VALUES ('$post->firstname', '$post->lastname', '$post->email', '$post->username', SHA1('$post->password'), true, 3);");
				echo "<p>Du wurdest erfolgreich registriert!</p>";
				$next = true;
				$db->close(); //close the connection to the db
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