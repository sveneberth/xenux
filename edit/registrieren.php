<?php
$show='yes';
$submit = $_POST['submit'];
if($submit=='Registrieren') {
	$FirstName	= $_POST['FirstName'];
	$LastName	= $_POST['LastName'];
	$eMail		= $_POST['eMail'];
	$username	= $_POST['username'];
	$password	= $_POST['password'];
	$password1	= $_POST['password1'];

	if($FirstName == '')		{$FirstName_fill='n';}	else{$FirstName_fill='y';}
	if($LastName == '')			{$LastName_fill='n';}	else{$LastName_fill='y';}
	if($eMail == '')			{$eMail_fill='n';}		else{$eMail_fill='y';}
	if($username == '')			{$username_fill='n';}	else{$username_fill='y';}
	if($password == '')			{$pw_fill='n';}			else{$pw_fill='y';}
	if($password != $password1)	{$pw_stimmt='n';}		else{$pw_stimmt='y';}


	if($FirstName_fill == 'y' and $LastName_fill == 'y' and $eMail_fill == 'y' and $username_fill == 'y' and $pw_fill == 'y' and $pw_stimmt != 'n'){
		$sql = "SELECT COUNT(username) AS anzahl FROM XENUX_users WHERE username = '".mysql_real_escape_string($username)."'";
		$erg = mysql_query($sql);
		$var = mysql_fetch_object($erg);
		if($var->anzahl >= 1) {
			$username_exist="y";
		} else {
			$username_exist="n";
		}
		$sql = "SELECT COUNT(email) AS anzahl FROM XENUX_users WHERE email = '".mysql_real_escape_string($eMail)."'";
		$erg = mysql_query($sql);
		$var = mysql_fetch_object($erg);
		if($var->anzahl >= 1) {
			$email_exist="y";
		} else {
			$email_exist="n";
		}
		if($username_exist=="n" and $email_exist=="n") {
			$sql = "INSERT INTO `XENUX_users`(`id`, `nachname`, `vorname`, `email`, `username`, `pw`, `admin`, `role`) VALUES (NULL, '".mysql_real_escape_string($LastName)."', '".mysql_real_escape_string($FirstName)."', '".mysql_real_escape_string($eMail)."', '".mysql_real_escape_string($username)."', 'xkanf".md5(mysql_real_escape_string($password))."v4sf5w', 'no', '0');
			";
			$erg = mysql_query($sql);
			$Freigabelink = 'http://'.$_SERVER['SERVER_NAME'].dirname($_SERVER['SCRIPT_NAME']).'/?site=freigabe&username='.$username.'&email='.$eMail;
			$nachricht = '<html></head><title>Accountfreischaltung</title></head><body>
			Hallo!<br />
			Es hat sich jemand auf der Homepage '.$_SERVER['SERVER_NAME'].' registriert, er wartet nun auf die Freigabe!<br /><br />
			Name: '.$FirstName.' '.$LastName.'<br />
			Datum: '.date("d.m.y H:i:s ").'<br /><br />
			Sie können ihn hier Freischalten: <a href="'.$Freigabelink.'">'.$Freigabelink.'</a><br />
			<br />
			<span style="font-family:Verdana;color:#777;border-top: 1px #777 solid;">Die Mail wurde mit Xenux erstellt</span></body></html>';
			$header		 = 'From: '.$HP_Email."\r\n";
			$header		.= 'Reply-To: '.$HP_Email."\r\n";
			$header		.= 'MIME-Version: 1.0' . "\r\n";
			$header		.= 'Content-type: text/html; charset=utf-8' . "\r\n";
			mail($HP_Email, 'Accountfreischaltung', $nachricht, $header);
			echo '<br />Du wurdest erfolgreich registriert! Nun musst du warten, bis der Administrator deinen Account freischaltet, du wirst darüber per E-Mail benachrichtig!';
			$show='no';
		}
	}
}

if($show!='no'){
?>
<form action="" method="post">
	<span <?php if (empty($FirstName) and $_SERVER['REQUEST_METHOD'] == "POST"){echo 'style="color:#cc0000;"';} ?>>Vorname</span><br />
	<input type="text" name="FirstName" value="<?php echo $FirstName; ?>" /><br /><br />
	<span <?php if (empty($LastName) and $_SERVER['REQUEST_METHOD'] == "POST"){echo 'style="color:#cc0000;"';} ?>>Nachname</span><br />
	<input type="text" name="LastName" value="<?php echo $LastName; ?>" /><br /><br />
	<span <?php if (empty($eMail) and $_SERVER['REQUEST_METHOD'] == "POST"){echo 'style="color:#cc0000;"';} ?>>E-Mail</span><br />
	<input type="email" name="eMail" value="<?php echo $eMail; ?>" /><br />
	<?php
	if($email_exist == 'y'){echo 'Ein Account mit dieser E-Mail-Adresse existiert schon, zwei Accounts über eine E-Mail Adresse sind nicht zulässig!<br />';}
	?><br />
	<span <?php if (empty($username) and $_SERVER['REQUEST_METHOD'] == "POST"){echo 'style="color:#cc0000;"';} ?>>Benutzername</span><br />
	<input type="text" name="username" value="<?php echo $username; ?>" /><br />
	<?php
	if($username_exist == 'y'){echo 'Der Benutzername ist schon vergeben, bitte wähle einen anderen!<br />';}
	?><br />
	<span <?php if (empty($password) and $_SERVER['REQUEST_METHOD'] == "POST"){echo 'style="color:#cc0000;"';} ?>>Passwort</span><br />
	<input type="password" name="password" value="<?php echo $password; ?>" /><br /><br />
	<span <?php if (empty($password1) and $_SERVER['REQUEST_METHOD'] == "POST"){echo 'style="color:#cc0000;"';} ?>>Passwort bestätigen</span><br />
	<input type="password" name="password1" value="<?php echo $password1; ?>" /><br />
	<?php
	if($pw_stimmt == 'n'){echo 'Die angegebenen Passwörter stimmen nicht überein!<br />';}
	?>
	<input type="submit" name="submit" value="Registrieren"/>
</form>
<?php } ?>