<?php
if(!isset($site)) die("You can not open this file individually/Sie k&ouml;nnen diese Datei nicht einzeln &ouml;ffnen!");
$random_pw = substr(md5(uniqid(rand())), 0, 10);
if(!empty($_POST['submit'])) {
	$username = mysql_real_escape_string($_POST['username']);
	if(!empty($username)) {
		$sql = "SELECT * FROM XENUX_users WHERE username = '$username'";
		$erg = mysql_query($sql);
		$anzahl = mysql_num_rows($erg);
		if($anzahl == 0) {
			$result = "Es konnte keinem Account den Benutzernamen <i>$username</i> zugeordnet werden.";
		} else {
			$sql = "SELECT * FROM XENUX_users WHERE username = '$username'";
			$erg = mysql_query($sql);
			$row = mysql_fetch_array($erg);
			$sql = "UPDATE XENUX_users Set pw = 'xkanf".md5($random_pw)."v4sf5w' WHERE username = '$username'";
			$erg = mysql_query($sql);
			$nachricht = '<html></head><title>Passwort vergessen</title></head><body>
Hallo!<br />
Dein neues Passwort für <a href="http://'.$HP_URL.'">http://'.$HP_URL.'</a>  lautet: '.$random_pw.'<br />
Es empfiehlt sich, das Passwort nach Login zu ändern.
<br /><br />
<span style="font-family:Verdana;color:#777;border-top: 1px #777 solid;">Die Mail wurde mit Xenux erstellt</span></body></html>';
			$header		 = 'From: '.$HP_Email."\r\n";
			$header		.= 'Reply-To: '.$HP_Email."\r\n";
			$header		.= 'MIME-Version: 1.0' . "\r\n";
			$header		.= 'Content-type: text/html; charset=utf-8' . "\r\n";
			mail($row['email'], 'Passwort  vergessen', $nachricht, $header)
			or die('fehler');
			$result = 'Dein neues Passwort wurde dir soeben per E-Mail zugeschickt!';
		}
	}
}
	echo @$row['email'];
?>
<p>Fals du deinen Passwort vergessen hast, kannst du dir hier ein neues an die Registrierte E-Mail-Adresse schicken.</p>
<form action="" method="post">
Benutzername:<br />
<input type="text" name="username" />
<p><?php echo @$result ?></p>
<input name="submit" type="submit" value="Passwort zusenden">
</form>